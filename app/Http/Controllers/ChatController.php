<?php
namespace App\Http\Controllers;

// Import các class cần thiết
use App\Events\MessageSent; // Event để gửi thông báo khi có tin nhắn mới
use App\Models\Conversation; // Model để quản lý các cuộc trò chuyện
use App\Models\Message;
use Illuminate\Http\Request; // Class để xử lý các request HTTP
use Illuminate\Support\Facades\Auth; // Facade để quản lý xác thực người dùng
use Illuminate\Support\Facades\Log; // Facade để ghi log cho hệ thống

class ChatController extends Controller
{
    // Hàm hiển thị danh sách các cuộc trò chuyện cho admin
    public function adminIndex()
    {
        // Lấy tất cả các cuộc trò chuyện, kèm thông tin người dùng và tin nhắn mới nhất
        $conversations = Conversation::with(['user', 'messages' => function ($query) {
            $query->latest()->take(1)->with('sender'); // Lấy tin nhắn mới nhất và thông tin người gửi
        }])->get();
        // Trả về view 'admin.chats.index' với dữ liệu các cuộc trò chuyện
        return view('admin.chats.index', compact('conversations'));
    }

    // Hàm lấy danh sách tin nhắn trong một cuộc trò chuyện cụ thể
    public function getMessages($conversationId)
    {
        // Lấy thông tin người dùng đang đăng nhập
        $user = Auth::user();
        // Tìm cuộc trò chuyện theo ID, nếu không tìm thấy thì trả về lỗi
        $conversation = Conversation::findOrFail($conversationId);

        // Kiểm tra quyền truy cập: Nếu là người dùng (role_id = 3) và không phải chủ cuộc trò chuyện
        if ($user->role_id == 3 && $conversation->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403); // Trả về lỗi 403
        // Nếu là admin nhưng không phải admin được gán cho cuộc trò chuyện và cuộc trò chuyện đã có admin
        } elseif ($user->role_id != 3 && $conversation->admin_id != $user->id && $conversation->admin_id !== null) {
            return response()->json(['error' => 'Unauthorized'], 403); // Trả về lỗi 403
        }

        // Nếu là admin và cuộc trò chuyện chưa có admin, gán admin hiện tại cho cuộc trò chuyện
        if ($user->role_id != 3 && $conversation->admin_id === null) {
            $conversation->admin_id = $user->id;
            $conversation->save();
        }

        // Lấy tất cả tin nhắn của cuộc trò chuyện, kèm thông tin người gửi
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->get();

        // Ghi log thông tin về số lượng tin nhắn được lấy
        Log::info('Fetched messages for conversation', [
            'conversation_id' => $conversationId,
            'user_id' => $user->id,
            'message_count' => $messages->count()
        ]);

        // Trả về danh sách tin nhắn dưới dạng JSON
        return response()->json($messages);
    }

    // Hàm gửi tin nhắn mới trong một cuộc trò chuyện
    public function sendMessage(Request $request, $conversationId)
    {
        try {
            // Lấy thông tin người dùng đang đăng nhập
            $user = Auth::user();
            // Tìm cuộc trò chuyện theo ID
            $conversation = Conversation::findOrFail($conversationId);

            // Kiểm tra quyền: Nếu là người dùng và không phải chủ cuộc trò chuyện
            if ($user->role_id == 3 && $conversation->user_id != $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403); // Trả về lỗi 403
            // Nếu là admin nhưng không phải admin được gán và cuộc trò chuyện đã có admin
            } elseif ($user->role_id != 3 && $conversation->admin_id != $user->id && $conversation->admin_id !== null) {
                return response()->json(['error' => 'Unauthorized'], 403); // Trả về lỗi 403
            }

            // Lấy nội dung tin nhắn từ request
            $content = $request->input('content');
            // Kiểm tra nội dung tin nhắn có rỗng hay không
            if (empty(trim($content))) {
                return response()->json(['error' => 'Message content cannot be empty'], 400); // Trả về lỗi 400
            }

            // Tạo mới một tin nhắn
            $message = new Message();
            $message->conversation_id = $conversationId; // Gán ID cuộc trò chuyện
            $message->sender_id = $user->id; // Gán ID người gửi
            $message->content = $content; // Gán nội dung tin nhắn
            $message->save(); // Lưu tin nhắn vào database

            // Load thông tin người gửi cho tin nhắn
            $message->load('sender');

            // Ghi log khi tin nhắn được lưu và sự kiện được kích hoạt
            Log::info('Message saved and event triggered', [
                'conversation_id' => $conversationId,
                'message_id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_role' => $user->role_id == 3 ? 'user' : 'admin', // Ghi vai trò người gửi
                'content' => $message->content,
                'channel' => 'conversation.' . $conversationId // Kênh sự kiện
            ]);

            // Kích hoạt sự kiện MessageSent để thông báo tin nhắn mới
            event(new MessageSent($message));

            // Trả về tin nhắn vừa gửi dưới dạng JSON
            return response()->json($message);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có ngoại lệ xảy ra
            Log::error('Error sending message: ' . $e->getMessage(), [
                'conversation_id' => $conversationId,
                'user_id' => Auth::id(),
                'content' => $request->input('content'),
                'stack' => $e->getTraceAsString()
            ]);
            // Trả về lỗi server 500 với thông báo chi tiết
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // Hàm lấy hoặc tạo mới một cuộc trò chuyện cho người dùng
    public function getOrCreateConversation(Request $request)
    {
        try {
            // Lấy thông tin người dùng đang đăng nhập
            $user = Auth::user();
            // Kiểm tra xem người dùng có đăng nhập hay không
            if (!$user) {
                return response()->json(['error' => 'Unauthorized: User not authenticated'], 401); // Trả về lỗi 401
            }

            // Kiểm tra quyền: Chỉ người dùng (role_id = 3) được tạo cuộc trò chuyện
            if ($user->role_id != 3) {
                return response()->json(['error' => 'Unauthorized: Only users can create conversations'], 403); // Trả về lỗi 403
            }

            // Kiểm tra xem người dùng đã có cuộc trò chuyện nào chưa
            $conversation = Conversation::where('user_id', $user->id)->first();

            // Nếu đã có cuộc trò chuyện
            if ($conversation) {
                // Ghi log thông tin cuộc trò chuyện hiện có
                Log::info('Existing conversation found', [
                    'user_id' => $user->id,
                    'conversation_id' => $conversation->id
                ]);
                // Trả về cuộc trò chuyện dưới dạng JSON
                return response()->json($conversation);
            }

            // Nếu chưa có, tạo mới một cuộc trò chuyện
            $conversation = new Conversation();
            $conversation->user_id = $user->id; // Gán ID người dùng
            $conversation->status = 'open'; // Đặt trạng thái là đang mở
            $conversation->save(); // Lưu cuộc trò chuyện vào database

            // Ghi log khi tạo mới cuộc trò chuyện
            Log::info('New conversation created', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
            ]);

            // Kích hoạt sự kiện NewConversation để thông báo cuộc trò chuyện mới
            event(new \App\Events\NewConversation($conversation));

            // Trả về cuộc trò chuyện mới tạo dưới dạng JSON
            return response()->json($conversation);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có ngoại lệ xảy ra
            Log::error('Error creating conversation: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString()
            ]);
            // Trả về lỗi server 500 với thông báo chi tiết
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // Hàm lấy danh sách các cuộc trò chuyện của người dùng
    public function getConversations(Request $request)
    {
        try {
            // Lấy thông tin người dùng đang đăng nhập
            $user = Auth::user();
            // Kiểm tra xem người dùng có đăng nhập hay không
            if (!$user) {
                return response()->json(['error' => 'Unauthorized: User not authenticated'], 401); // Trả về lỗi 401
            }

            // Lấy danh sách các cuộc trò chuyện của người dùng, kèm 3 tin nhắn mới nhất
            $conversations = Conversation::where('user_id', $user->id)
                ->with(['messages' => function ($query) {
                    $query->latest()->take(3)->with('sender'); // Lấy 3 tin nhắn mới nhất và thông tin người gửi
                }])
                ->get();

            // Ghi log thông tin về số lượng cuộc trò chuyện được lấy
            Log::info('Fetched conversations for user', [
                'user_id' => $user->id,
                'conversation_count' => $conversations->count()
            ]);

            // Trả về danh sách các cuộc trò chuyện dưới dạng JSON
            return response()->json($conversations);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có ngoại lệ xảy ra
            Log::error('Error fetching conversations: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString()
            ]);
            // Trả về lỗi server 500 với thông báo chi tiết
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    // Hàm gán một cuộc trò chuyện cho admin
    public function assignConversation(Request $request, $conversationId)
    {
        try {
            // Lấy thông tin người dùng đang đăng nhập
            $user = Auth::user();
            // Kiểm tra xem người dùng có phải là admin hay không
            if (!$user || $user->role_id == 3) {
                return response()->json(['error' => 'Unauthorized: Only admins can assign conversations'], 403); // Trả về lỗi 403
            }

            // Tìm cuộc trò chuyện theo ID
            $conversation = Conversation::findOrFail($conversationId);
            // Gán admin hiện tại cho cuộc trò chuyện
            $conversation->admin_id = $user->id;
            $conversation->save(); // Lưu thay đổi vào database

            // Ghi log khi cuộc trò chuyện được gán
            Log::info('Conversation assigned to admin', [
                'conversation_id' => $conversationId,
                'admin_id' => $user->id
            ]);

            // Trả về thông báo thành công dưới dạng JSON
            return response()->json(['message' => 'Conversation assigned successfully']);
        } catch (\Exception $e) {
            // Ghi log lỗi nếu có ngoại lệ xảy ra
            Log::error('Error assigning conversation: ' . $e->getMessage(), [
                'conversation_id' => $conversationId,
                'user_id' => Auth::id(),
                'stack' => $e->getTraceAsString()
            ]);
            // Trả về lỗi server 500 với thông báo chi tiết
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}