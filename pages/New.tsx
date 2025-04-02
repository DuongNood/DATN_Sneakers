import React, { useEffect, useState } from 'react'
import axios from 'axios'
import { useNavigate } from 'react-router-dom'

interface News {
  id: number
  title: string
  image: string
  content: string
  author: string
  published_at: string
  created_at?: string
  updated_at?: string
}

interface ApiResponse {
  status: boolean
  message: string
  data: News[] | null
}

const NewsList: React.FC = () => {
  const [newsList, setNewsList] = useState<News[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [error, setError] = useState<string | null>(null)
  const [message, setMessage] = useState<string>('')
  const [currentPage, setCurrentPage] = useState<number>(1)
  const itemsPerPage = 8
  const navigate = useNavigate()

  useEffect(() => {
    const fetchNews = async () => {
      try {
        const response = await axios.get<ApiResponse>('http://localhost:8000/api/news', { timeout: 10000 })
        if (response.data.status) {
          setNewsList(response.data.data || [])
          setMessage(response.data.data && response.data.data.length > 0 ? '' : 'Hiện tại không có tin tức nào')
        } else {
          setError(`Lỗi từ server: ${response.data.message}`)
        }
      } catch (err) {
        setError('Lỗi khi gọi API: ' + (err.response?.data?.message || err.message))
      } finally {
        setLoading(false)
      }
    }
    fetchNews()
  }, [])

  const totalPages = Math.ceil(newsList.length / itemsPerPage)
  const paginatedNews = newsList.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)

  const handlePageChange = (page: number) => {
    if (page >= 1 && page <= totalPages) setCurrentPage(page)
  }

  const handleNewsClick = (id: number) => {
    navigate(`/news/${id}`)
  }

  if (loading)
    return (
      <div className='flex justify-center items-center h-screen'>
        <p className='text-lg text-gray-500 animate-pulse'>Đang tải...</p>
      </div>
    )
  if (error)
    return (
      <div className='flex justify-center items-center h-screen'>
        <p className='text-lg text-red-500'>{error}</p>
      </div>
    )

  return (
    <div className='container mx-auto px-4 py-10'>
      <h1 className='text-2xl font-bold text-center mb-8 text-gray-800'>Tin tức</h1>
      {message ? (
        <p className='text-center text-gray-600'>{message}</p>
      ) : (
        <>
          <div className='grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-w-7xl mx-auto'>
            {paginatedNews.map((news) => (
              <div
                key={news.id}
                className='bg-white shadow rounded-lg overflow-hidden cursor-pointer'
                onClick={() => handleNewsClick(news.id)}
              >
                <img
                  src={news.image}
                  alt={news.title}
                  className='w-full h-40 object-cover rounded-t-lg'
                  onError={(e) => (e.currentTarget.src = 'https://via.placeholder.com/150')}
                />
                <div className='p-4'>
                  <h2 className='text-lg font-semibold text-gray-800 line-clamp-1'>{news.title}</h2>
                  <p className='text-gray-600 text-xs line-clamp-1'>{news.content}</p>
                  <p className='text-gray-500 text-xs mt-1'>Tác giả: {news.author || 'Admin'} </p>
                  <p className='text-gray-500 text-xs mt-1'>
                    Đăng ngày: {new Date(news.created_at).toLocaleDateString('vi-VN')}
                  </p>
                </div>
              </div>
            ))}
          </div>
          {totalPages > 1 && (
            <div className='flex justify-center mt-8 space-x-1'>
              <button
                onClick={() => handlePageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className='px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50'
              >
                Trước
              </button>
              {Array.from({ length: totalPages }, (_, i) => (
                <button
                  key={i + 1}
                  onClick={() => handlePageChange(i + 1)}
                  className={`px-3 py-1 rounded ${currentPage === i + 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'}`}
                >
                  {i + 1}
                </button>
              ))}
              <button
                onClick={() => handlePageChange(currentPage + 1)}
                disabled={currentPage === totalPages}
                className='px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50'
              >
                Sau
              </button>
            </div>
          )}
        </>
      )}
    </div>
  )
}

export default NewsList
