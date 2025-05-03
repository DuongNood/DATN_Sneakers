import React, { useEffect, useState } from 'react'
import axios from 'axios'
import { useNavigate } from 'react-router-dom'
import { FiChevronLeft, FiChevronRight, FiFileText } from 'react-icons/fi'

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

const NewsList = () => {
  const [newsList, setNewsList] = useState<News[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [error, setError] = useState<string | null>(null)
  const [message, setMessage] = useState<string>('')
  const [currentPage, setCurrentPage] = useState<number>(1)
  const itemsPerPage = 4
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
      <div className='flex justify-center items-center h-screen bg-gray-50'>
        <p className='text-xl text-gray-600 animate-pulse'>Đang tải...</p>
      </div>
    )
  if (error)
    return (
      <div className='flex justify-center items-center h-screen bg-gray-50'>
        <p className='text-xl text-red-600'>{error}</p>
      </div>
    )

  return (
    <div className='min-h-screen bg-gray-50'>
      {/* Header */}

      <div className='max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8'>
        <h1 className='text-4xl font-extrabold text-black text-center'>Tin tức</h1>
      </div>

      {/* Main Content */}
      <main className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12'>
        {message ? (
          <div className='flex flex-col items-center justify-center bg-white shadow-xl rounded-xl p-8 mx-auto max-w-md transition-all duration-300 hover:shadow-2xl'>
            <FiFileText className='text-blue-400 text-6xl mb-4 animate-pulse' />
            <p className='text-lg sm:text-xl text-gray-700 text-center'>{message}</p>
          </div>
        ) : (
          <>
            <div className='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6'>
              {paginatedNews.map((news) => (
                <div
                  key={news.id}
                  className='bg-white shadow-lg rounded-xl overflow-hidden cursor-pointer transform hover:shadow-2xl transition-all duration-300 border border-gray-200 hover:border-indigo-300'
                  onClick={() => handleNewsClick(news.id)}
                >
                  <div className='relative overflow-hidden'>
                    <img
                      src={news.image}
                      alt={news.title}
                      className='w-full h-48 object-cover rounded-t-xl transform hover:scale-105 transition-transform duration-300'
                      onError={(e) => (e.currentTarget.src = 'https://via.placeholder.com/150')}
                    />
                    <div className='absolute inset-0 bg-gradient-to-t from-black/50 to-transparent'></div>
                  </div>
                  <div className='p-5'>
                    <h2 className='text-xl sm:text-2xl font-bold text-gray-900 line-clamp-1 hover:text-indigo-600 transition-colors duration-200'>
                      {news.title}
                    </h2>
                    <p className='text-gray-700 text-lg line-clamp-2 mt-2'>{news.content}</p>
                    <div className='flex justify-between items-center mt-4'>
                      <p className='text-sm text-gray-600'>Tác giả: {news.author || 'Admin'}</p>
                      <p className='text-sm text-gray-600'>{new Date(news.created_at).toLocaleDateString('vi-VN')}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            {totalPages > 1 && (
              <div className='flex justify-center items-center mt-12 gap-3 flex-wrap'>
                <button
                  onClick={() => handlePageChange(currentPage - 1)}
                  disabled={currentPage === 1}
                  className={`px-4 py-2 rounded-full flex items-center gap-1 text-sm font-medium transition-all duration-300 ${
                    currentPage === 1
                      ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                      : 'bg-gray-100 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700'
                  }`}
                >
                  <FiChevronLeft />
                  Trước
                </button>
                {Array.from({ length: totalPages }, (_, i) => (
                  <button
                    key={i + 1}
                    onClick={() => handlePageChange(i + 1)}
                    className={`px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 ${
                      currentPage === i + 1
                        ? 'bg-indigo-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700'
                    }`}
                  >
                    {i + 1}
                  </button>
                ))}
                <button
                  onClick={() => handlePageChange(currentPage + 1)}
                  disabled={currentPage === totalPages}
                  className={`px-4 py-2 rounded-full flex items-center gap-1 text-sm font-medium transition-all duration-300 ${
                    currentPage === totalPages
                      ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                      : 'bg-gray-100 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700'
                  }`}
                >
                  Sau
                  <FiChevronRight />
                </button>
              </div>
            )}
          </>
        )}
      </main>
    </div>
  )
}

export default NewsList
