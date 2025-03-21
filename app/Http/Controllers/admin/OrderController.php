<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Oder;
use App\Models\OderDetail;
use App\Models\Product;
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
        $orders = Oder::with('user')->orderBy('created_at', 'desc')->get();

        return view(self::PATH_VIEW . __FUNCTION__, compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'recipient_address' => 'required|string',
            'total_price' => 'required|numeric',
            'payment_method' => 'required|in:COD,Online',
            'products' => 'required|array',
            'promotion_code' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $discountAmount = 0;
            if ($request->promotion_code) {
                $promotion = Promotion::where('code', $request->promotion_code)->first();
                if ($promotion) {
                    $discountAmount = $promotion->discount;
                } else {
                    return redirect()->back()->with('error', 'Mã giảm giá không hợp lệ!');
                }
            }

            $order = new Oder();
            $order->recipient_name = $request->recipient_name;
            $order->recipient_phone = $request->recipient_phone;
            $order->recipient_address = $request->recipient_address;
            $order->total_price = $request->total_price - $discountAmount;
            $order->payment_method = $request->payment_method;
            $order->payment_status = $request->payment_method == 'Online' ? 'Đã thanh toán' : 'Chưa thanh toán';
            $order->status = $request->payment_method == 'Online' ? 'Đang chuẩn bị' : 'Chờ xác nhận';
            $order->promotion_code = $request->promotion_code;
            $order->discount_amount = $discountAmount;
            $order->save();

            foreach ($request->products as $product) {
                OderDetail::create([
                    'oder_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $product['discount'] ?? 0,
                ]);
            }

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
        $order = Oder::with('details.product')->findOrFail($id);
        $statusOptions = ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'];
        return view(self::PATH_VIEW . __FUNCTION__, compact('order', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Oder::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($order->payment_status !== 'Đã thanh toán') {
                $order->payment_status = $request->payment_status;
            }

            if (!in_array($order->status, ['Đã giao hàng', 'Hủy đơn hàng'])) {
                $order->status = $request->status;
            }

            foreach ($request->products as $product) {
                $orderDetail = OderDetail::where('oder_id', $order->id)->where('product_id', $product['id'])->first();
                if ($orderDetail) {
                    $orderDetail->quantity = $product['quantity'];
                    $orderDetail->price = $product['price'];
                    $orderDetail->discount = $product['discount'] ?? 0;
                    $orderDetail->save();
                }
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
}
