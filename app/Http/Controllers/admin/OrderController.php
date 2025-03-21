<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Oder;
use App\Models\OderDetail;
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
        $orders = Oder::with('user')->orderBy('created_at', 'desc')->get();

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
            $order = new Oder();
            $order->recipient_name = $request->recipient_name;
            $order->recipient_phone = $request->recipient_phone;
            $order->recipient_address = $request->recipient_address;
            $order->total_price = $request->total_price;
            $order->payment_method = $request->payment_method;
            $order->payment_status = $request->payment_method == 'Online' ? 'Đã thanh toán' : 'Chưa thanh toán';
            $order->status = $request->payment_method == 'Online' ? 'Đang chuẩn bị' : 'Chờ xác nhận';
            $order->save();

            foreach ($request->products as $product) {
                $promotion = ProductPromotion::where('product_variant_id', $product['id'])->first();
                $discount = $promotion ? $promotion->promotion->discount_value : 0;
                $total_price = ($product['price'] - $discount) * $product['quantity'];

                OderDetail::create([
                    'oder_id' => $order->id,
                    'product_variant_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $discount,
                    'total_price' => $total_price,
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
                $orderDetail = OderDetail::where('oder_id', $order->id)->where('product_variant_id', $product['id'])->first();
                if ($orderDetail) {
                    $promotion = ProductPromotion::where('product_variant_id', $product['id'])->first();
                    $discount = $promotion ? $promotion->promotion->discount_value : 0;
                    $total_price = ($product['price'] - $discount) * $product['quantity'];

                    $orderDetail->quantity = $product['quantity'];
                    $orderDetail->price = $product['price'];
                    $orderDetail->discount = $discount;
                    $orderDetail->total_price = $total_price;
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
