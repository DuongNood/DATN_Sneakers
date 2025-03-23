<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin/orders.';

    public function index()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        return view(self::PATH_VIEW . __FUNCTION__, compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_address' => 'required|string',
            'total_price' => 'required|numeric',
            'payment_method' => 'required|in:COD,Online',
            'products' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            // Tạo mã đơn hàng duy nhất
            $orderCode = 'ODR' . time() . rand(1000, 9999);

            // Xác định trạng thái ban đầu
            $paymentStatus = $request->payment_method == 'Online' ? 'Đã thanh toán' : 'Chưa thanh toán';
            $shippingStatus = $request->payment_method == 'Online' ? 'Đang chuẩn bị' : 'Chờ xác nhận';

            // Tạo đơn hàng
            $order = Order::create([
                'order_code'       => $orderCode,
                'recipient_name'   => $request->recipient_name,
                'recipient_phone'  => $request->recipient_phone,
                'recipient_address' => $request->recipient_address,
                'total_price'      => 0,
                'payment_method'   => $request->payment_method,
                'payment_status'   => $paymentStatus,
                'status'           => $shippingStatus,
            ]);

            $totalOrderPrice = 0;

            foreach ($request->products as $productData) {
                $variant = ProductVariant::findOrFail($productData['id']);

                // Kiểm tra số lượng tồn kho
                if ($variant->quantity < $productData['quantity']) {
                    throw new \Exception("Sản phẩm {$variant->id} không đủ hàng trong kho.");
                }

                // Tìm khuyến mãi hợp lệ
                $promotion = ProductPromotion::where('product_variant_id', $variant->id)
                    ->whereHas('promotion', function ($query) {
                        $query->where('start_date', '<=', now())->where('end_date', '>=', now());
                    })
                    ->first();

                $discountedPrice = $variant->price;
                $discount = 0;

                if ($promotion) {
                    $promo = $promotion->promotion;
                    if ($promo->discount_type == Promotion::PHAN_TRAM) {
                        $discount = min(($variant->price * $promo->discount_value / 100), $promo->max_discount_value);
                    } elseif ($promo->discount_type == Promotion::SO_TIEN) {
                        $discount = min($promo->discount_value, $promo->max_discount_value);
                    }
                    $discountedPrice = max($variant->price - $discount, 0);
                }

                $subtotal = $discountedPrice * $productData['quantity'];
                $totalOrderPrice += $subtotal;

                // Lưu chi tiết đơn hàng
                OrderDetail::create([
                    'order_id'               => $order->id,
                    'product_variant_id'    => $variant->id,
                    'quantity'              => $productData['quantity'],
                    'price'                 => $variant->price,
                    'discount'              => $discount,
                    'total_price'           => $subtotal,
                ]);

                // Trừ số lượng hàng tồn kho
                $variant->decrement('quantity', $productData['quantity']);
            }

            // Cập nhật tổng tiền đơn hàng
            $order->update(['total_price' => $totalOrderPrice]);

            DB::commit();
            return redirect()
                ->route(self::PATH_VIEW . 'index')
                ->with('success', 'Đơn hàng đã được tạo!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi tạo đơn hàng!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = Order::with('orderDetails.productVariant.product')->findOrFail($id);
        $statusOptions = ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'];
        return view(self::PATH_VIEW . __FUNCTION__, compact('order', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        DB::beginTransaction();
        try {
            // Nếu đơn hàng bị hủy, hoàn lại số lượng hàng trong kho
            if ($request->status === 'Hủy đơn hàng' && $order->status !== 'Hủy đơn hàng') {
                foreach ($order->details as $detail) {
                    $variant = ProductVariant::findOrFail($detail->product_variant_id);
                    $variant->increment('quantity', $detail->quantity);
                }
            }

            // Chỉ cập nhật trạng thái nếu hợp lệ
            if ($order->payment_status !== 'Đã thanh toán' && $request->payment_status === 'Đã thanh toán') {
                $order->payment_status = 'Đã thanh toán';
                if ($order->status === 'Chờ xác nhận') {
                    $order->status = 'Đang chuẩn bị';
                }
            }

            if ($request->status && $this->canUpdateShippingStatus($order->status, $request->status)) {
                $order->status = $request->status;
            }

            $order->save();
            DB::commit();
            return redirect()
                ->route(self::PATH_VIEW . 'index')
                ->with('success', 'Đơn hàng đã được cập nhật!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
    }

    private function canUpdateShippingStatus($currentStatus, $newStatus)
    {
        $validTransitions = [
            'Chờ xác nhận'    => ['Đang chuẩn bị', 'Đang vận chuyển', 'Hủy đơn hàng'],
            'Đang chuẩn bị'   => ['Đang vận chuyển', 'Hủy đơn hàng'],
            'Đang vận chuyển' => ['Đã giao hàng'],
        ];

        return isset($validTransitions[$currentStatus]) && in_array($newStatus, $validTransitions[$currentStatus]);
    }
}
