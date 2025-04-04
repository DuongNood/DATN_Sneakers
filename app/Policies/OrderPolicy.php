<?php

// app/Policies/OrderPolicy.php
namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Giả sử bạn có role 'admin' hoặc phương thức isAdmin()
        if ($user->hasRole('admin')) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine whether the user can request cancellation for the model.
     */
    public function requestCancellation(User $user, Order $order): bool
    {
        // Chỉ chủ đơn hàng và khi đơn hàng ở trạng thái cho phép hủy
        return $user->id === $order->user_id && $order->canBeCancelledByUser();
    }

    // Thêm các policy khác nếu cần (create, update, delete...)
}
