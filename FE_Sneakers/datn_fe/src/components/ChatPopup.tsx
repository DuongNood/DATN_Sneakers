import React, { useEffect, useState, useRef } from 'react'
import axios from 'axios'
import echo from '../echo'

interface Message {
  id: number
  sender_id: number
  content: string
  created_at: string
  sender: {
    id: number
    name: string
    image_user?: string
  }
}

interface Conversation {
  id: number
  user_id: number
  admin_id?: number
  messages: Message[]
}

const ChatPopup: React.FC = () => {
  const [isOpen, setIsOpen] = useState(false)
  const [conversations, setConversations] = useState<Conversation[]>([])
  const [selectedConversation, setSelectedConversation] = useState<Conversation | null>(null)
  const [messages, setMessages] = useState<Message[]>([])
  const [newMessage, setNewMessage] = useState('')
  const messagesEndRef = useRef<HTMLDivElement>(null)
  const token = localStorage.getItem('token')

  // Lấy danh sách conversation
  useEffect(() => {
    if (token) {
      axios
        .get('http://localhost:8000/api/conversations', {
          headers: { Authorization: `Bearer ${token}` }
        })
        .then((response) => {
          setConversations(response.data)
        })
        .catch((error) => console.error('Error fetching conversations:', error))
    }
  }, [token])

  // Tạo conversation mới
  const createConversation = async () => {
    if (!token) return
    try {
      const response = await axios.post(
        'http://localhost:8000/api/conversations',
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      )
      setConversations([...conversations, response.data])
      setSelectedConversation(response.data)
    } catch (error) {
      console.error('Error creating conversation:', error)
    }
  }

  // Lấy tin nhắn và lắng nghe Pusher
  useEffect(() => {
    if (selectedConversation && token) {
      axios
        .get(`http://localhost:8000/api/conversations/${selectedConversation.id}/messages`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        .then((response) => {
          setMessages(response.data)
        })
        .catch((error) => console.error('Error fetching messages:', error))

      echo.channel(`conversation.${selectedConversation.id}`).listen('MessageSent', (e: { message: Message }) => {
        setMessages((prev) => [...prev, e.message])
      })

      return () => {
        echo.leave(`conversation.${selectedConversation.id}`)
      }
    }
  }, [selectedConversation, token])

  // Cuộn xuống cuối tin nhắn
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' })
  }, [messages])

  // Gửi tin nhắn
  const sendMessage = async () => {
    if (!newMessage.trim() || !selectedConversation || !token) return

    try {
      const response = await axios.post(
        `http://localhost:8000/api/conversations/${selectedConversation.id}/messages`,
        { content: newMessage },
        { headers: { Authorization: `Bearer ${token}` } }
      )
      setMessages([...messages, response.data])
      setNewMessage('')
    } catch (error) {
      console.error('Error sending message:', error)
    }
  }

  // Nếu chưa đăng nhập, không hiển thị ô chat
  if (!token) return null

  return (
    <div className='fixed bottom-4 right-4 z-50 mb-20'>
      {/* Nút bật/tắt chat */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className='bg-blue-500 text-white rounded-full p-4 shadow-lg hover:bg-blue-600 transition'
      >
        {isOpen ? '✕' : '💬'}
      </button>

      {/* Popup chat */}
      {isOpen && (
        <div className='w-80 bg-white rounded-lg shadow-xl flex flex-col h-[400px] mt-2'>
          {/* Header */}
          <div className='bg-blue-500 text-white p-2 rounded-t-lg'>
            <h3 className='text-sm font-bold'>Chat Support</h3>
          </div>

          {/* Danh sách conversation */}
          <div className='flex-1 overflow-y-auto border-b'>
            <div className='p-2'>
              <button
                onClick={createConversation}
                className='w-full bg-blue-500 text-white px-2 py-1 rounded text-sm mb-2'
              >
                Bắt đầu đoạn chat mới
              </button>
            </div>
            {conversations.length === 0 ? (
              <div className='p-2 text-gray-500 text-sm'>
                <p>Chưa có đoạn chat nào</p>
              </div>
            ) : (
              conversations.map((conv) => (
                <div
                  key={conv.id}
                  className={`p-2 cursor-pointer text-sm ${selectedConversation?.id === conv.id ? 'bg-blue-100' : ''}`}
                  onClick={() => setSelectedConversation(conv)}
                >
                  <div className='font-semibold'>Đoạn chat cũ</div>
                  {conv.messages
                    .sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime())
                    .slice(0, 3)
                    .map((msg) => (
                      <div key={msg.id} className='text-xs text-gray-600 truncate'>
                        {msg.sender.name}: {msg.content.length > 30 ? `${msg.content.slice(0, 30)}...` : msg.content}
                      </div>
                    ))}
                </div>
              ))
            )}
          </div>

          {/* Khu vực tin nhắn */}
          {selectedConversation ? (
            <div className='flex-1 flex flex-col'>
              <div className='flex-1 p-2 overflow-y-auto'>
                {messages.map((message) => (
                  <div
                    key={message.id}
                    className={`mb-2 text-sm ${
                      message.sender_id === selectedConversation.user_id ? 'text-left' : 'text-right'
                    }`}
                  >
                    <div className='flex items-start'>
                      {message.sender_id === selectedConversation.user_id && (
                        <img
                          src={message.sender.image_user || '/default-avatar.png'}
                          alt={message.sender.name}
                          className='w-6 h-6 rounded-full mr-1'
                        />
                      )}
                      <div>
                        <p className='text-xs text-gray-500'>{message.sender.name}</p>
                        <p className='bg-gray-200 p-1 rounded-lg text-xs'>{message.content}</p>
                        <p className='text-xs text-gray-400'>{new Date(message.created_at).toLocaleTimeString()}</p>
                      </div>
                    </div>
                  </div>
                ))}
                <div ref={messagesEndRef} />
              </div>
              <div className='p-2 border-t'>
                <div className='flex'>
                  <input
                    type='text'
                    value={newMessage}
                    onChange={(e) => setNewMessage(e.target.value)}
                    className='flex-1 border rounded-l-lg p-1 text-sm'
                    placeholder='Type a message...'
                  />
                  <button onClick={sendMessage} className='bg-blue-500 text-white px-2 rounded-r-lg text-sm'>
                    Send
                  </button>
                </div>
              </div>
            </div>
          ) : (
            <div className='flex-1 flex items-center justify-center text-sm text-gray-500'>Chọn một đoạn chat</div>
          )}
        </div>
      )}
    </div>
  )
}

export default ChatPopup
