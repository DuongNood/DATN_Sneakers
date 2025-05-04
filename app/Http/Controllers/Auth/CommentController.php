<?php

namespace App\Http\Controllers\Auth;

// Namespace xác định vị trí của lớp này trong cấu trúc ứng dụng.
// Bộ điều khiển này nằm trong namespace Auth, thường được dùng cho các bộ điều khiển liên quan đến xác thực,
// nhưng ở đây nó xử lý các chức năng liên quan đến bình luận.

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Nhập các lớp cần thiết:
// - Controller: Lớp điều khiển cơ bản để kế thừa.
// - Comment: Mô hình Eloquent cho bảng comments trong cơ sở dữ liệu.
// - CommentLimit: Mô hình Eloquent để theo dõi giới hạn bình luận hàng ngày của mỗi người dùng.
// - Request: Xử lý dữ liệu yêu cầu HTTP.
// - Log: Cung cấp chức năng ghi log để gỡ lỗi và theo dõi lỗi.
// - Carbon: Thư viện xử lý ngày giờ, dùng để thao tác với ngày (ví dụ: lấy ngày hôm nay).

class CommentController extends Controller
{
    // Lớp CommentController kế thừa từ lớp Controller cơ bản, thừa hưởng các chức năng của nó.
    // Bộ điều khiển này xử lý các thao tác CRUD cho bình luận, bao gồm lấy, tạo và xóa bình luận.

    public function index($productId)
    {
        // Phương thức index lấy danh sách bình luận cho một sản phẩm cụ thể dựa trên $productId.
        try {
            // Khối try-catch để xử lý lỗi có thể xảy ra trong quá trình truy vấn.
            $comments = Comment::where('product_id', $productId)
                // Lấy các bình luận từ bảng comments có product_id khớp với $productId.
                ->with(['user' => function ($query) {
                    $query->select('id', 'name');
                }])
                // Nạp trước (eager load) thông tin người dùng liên quan đến bình luận,
                // chỉ lấy các cột id và name từ bảng users để tối ưu hiệu suất.
                ->orderBy('created_at', 'desc')
                // Sắp xếp bình luận theo thời gian tạo, mới nhất trước.
                ->get();
                // Lấy tất cả bản ghi phù hợp.
            return response()->json($comments);
            // Trả về danh sách bình luận dưới dạng JSON.
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try.
            Log::error('Error fetching comments: ' . $e->getMessage());
            // Ghi log lỗi với thông điệp chi tiết để hỗ trợ gỡ lỗi.
            return response()->json(['message' => 'Lỗi, vui lòng thử lại'], 2000);
            // Trả về phản hồi JSON với thông báo lỗi và mã trạng thái 2000 (lưu ý: mã 2000 không phải mã HTTP chuẩn).
        }
    }

    public function adminIndex(Request $request)
    {
        // Phương thức adminIndex lấy danh sách bình luận cho giao diện quản trị (admin).
        try {
            $comments = Comment::with([
                'product:id,product_name',
                'user:id,name',
                'replies' => function ($query) {
                    $query->with('user:id,name');
                }
            ])
                // Nạp trước thông tin sản phẩm (chỉ lấy id, product_name), người dùng (id, name),
                // và các phản hồi (replies) của bình luận, bao gồm thông tin người dùng của phản hồi.
                ->orderBy('id', 'desc')
                // Sắp xếp bình luận theo id, mới nhất trước.
                ->paginate(10);
                // Phân trang kết quả, mỗi trang 10 bình luận.
            Log::info('Fetched comments for admin: ', ['count' => $comments->count()]);
            // Ghi log thông tin về số lượng bình luận đã lấy.
            return view('admin.comments.index', compact('comments'));
            // Trả về view admin/comments/index.blade.php, truyền biến $comments để hiển thị danh sách bình luận.
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try.
            Log::error('Error fetching comments for admin: ' . $e->getMessage());
            // Ghi log lỗi với thông điệp chi tiết.
            return redirect()->route('admin.dashboard')->with('error', 'Lỗi khi lấy danh sách bình luận.');
            // Chuyển hướng về trang dashboard admin với thông báo lỗi.
        }
    }

    public function store(Request $request)
    {
        // Phương thức store xử lý việc tạo bình luận mới.
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'content' => 'required|string|max:1000',
            ]);
            // Xác thực dữ liệu đầu vào:
            // - product_id: Bắt buộc và phải tồn tại trong bảng products, cột id.
            // - content: Bắt buộc, là chuỗi, tối đa 1000 ký tự.

            $userId = auth()->id();
            // Lấy ID của người dùng hiện tại đang đăng nhập.
            $today = Carbon::today()->toDateString();
            // Lấy ngày hiện tại dưới dạng chuỗi (YYYY-MM-DD).

            // Kiểm tra giới hạn bình luận
            $limit = CommentLimit::firstOrCreate(
                ['user_id' => $userId, 'date' => $today],
                ['comment_count' => 0]
            );
            // Tìm hoặc tạo bản ghi trong bảng comment_limits cho người dùng và ngày hiện tại.
            // Nếu không tìm thấy, tạo bản ghi mới với comment_count = 0.

            if ($limit->comment_count >= 2) {
                // Kiểm tra nếu người dùng đã bình luận 2 lần trong ngày.
                return response()->json([
                    'message' => 'Bạn chỉ được bình luận 2 lần mỗi ngày!',
                    'remaining_comments' => 0
                ], 429);
                // Trả về lỗi 429 (Too Many Requests) với thông báo và số bình luận còn lại (0).
            }

            // Tạo bình luận
            $comment = Comment::create([
                'product_id' => $validated['product_id'],
                'user_id' => $userId,
                'content' => $validated['content'],
            ]);
            // Tạo bản ghi bình luận mới trong bảng comments với product_id, user_id và content.

            // Tăng số lượng bình luận
            $limit->increment('comment_count');
            // Tăng giá trị comment_count trong bảng comment_limits lên 1.

            $comment->load('user:id,name');
            // Nạp thông tin người dùng (id, name) cho bình luận vừa tạo.
            return response()->json([
                'comment' => $comment,
                'remaining_comments' => 2 - $limit->comment_count
            ], 201);
            // Trả về phản hồi JSON với bình luận vừa tạo, số bình luận còn lại, và mã trạng thái 201 (Created).
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try.
            Log::error('Error adding comment: ' . $e->getMessage());
            // Ghi log lỗi với thông điệp chi tiết.
            return response()->json(['message' => 'Lỗi khi thêm bình luận'], 500);
            // Trả về phản hồi JSON với thông báo lỗi và mã trạng thái 500 (Internal Server Error).
        }
    }

    public function destroy($id)
    {
        // Phương thức destroy xóa một bình luận dựa trên $id (dành cho giao diện quản trị).
        try {
            $comment = Comment::findOrFail($id);
            // Tìm bình luận theo id, nếu không tìm thấy sẽ ném ngoại lệ.
            $comment->delete();
            // Xóa bình luận khỏi cơ sở dữ liệu.
            return redirect()->route('admin.comments.index')->with('success', 'Xóa bình luận thành công.');
            // Chuyển hướng về trang danh sách bình luận với thông báo thành công.
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try.
            Log::error('Error deleting comment: ' . $e->getMessage());
            // Ghi log lỗi với thông điệp chi tiết.
            return redirect()->route('admin.comments.index')->with('error', 'Lỗi khi xóa bình luận.');
            // Chuyển hướng về trang danh sách bình luận với thông báo lỗi.
        }
    }

    public function apiDestroy($id)
    {
        // Phương thức apiDestroy xóa một bình luận dựa trên $id (dành cho API).
        try {
            $comment = Comment::findOrFail($id);
            // Tìm bình luận theo id, nếu không tìm thấy sẽ ném ngoại lệ.

            if (auth()->id() !== $comment->user_id && auth()->user()->role_id !== 1) {
                // Kiểm tra quyền xóa: Chỉ người tạo bình luận hoặc quản trị viên (role_id = 1) được xóa.
                return response()->json(['message' => 'Không có quyền xóa'], 403);
                // Trả về lỗi 403 (Forbidden) nếu không có quyền.
            }

            $comment->delete();
            // Xóa bình luận khỏi cơ sở dữ liệu.
            return response()->json(['message' => 'Xóa bình luận thành công'], 200);
            // Trả về phản hồi JSON với thông báo thành công và mã trạng thái 200 (OK).
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra trong khối try.
            Log::error('Error deleting comment: ' . $e->getMessage());
            // Ghi log lỗi với thông điệp chi tiết.
            return response()->json(['message' => 'Lỗi khi xóa bình luận'], 500);
            // Trả về phản hồi JSON với thông báo lỗi và mã trạng thái 500 (Internal Server Error).
        }
    }
}