import React, { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import axios from 'axios'

const NewsDetail = () => {
  const { id } = useParams()
  const [news, setNews] = useState(null)
  const [relatedNews, setRelatedNews] = useState([])
  const [loading, setLoading] = useState(true)

  // Hàm định dạng ngày
  const formatDate = (dateString) => {
    if (!dateString) return 'Không có ngày'
    const [datePart] = dateString.split('T')
    const [year, month, day] = datePart.split('-')
    return `${day}/${month}/${year}`
  }

  useEffect(() => {
    const fetchNewsDetail = async () => {
      try {
        const detailResponse = await axios.get(`http://127.0.0.1:8000/api/news/${id}`)
        setNews(detailResponse.data.data)

        const allNewsResponse = await axios.get('http://127.0.0.1:8000/api/news')
        const filteredNews = allNewsResponse.data.data.filter((item) => item.id !== parseInt(id)).slice(0, 3) // Giảm xuống 3 tin
        setRelatedNews(filteredNews)

        setLoading(false)
      } catch (err) {
        console.error('Lỗi khi lấy chi tiết tin tức:', err)
        setLoading(false)
      }
    }
    fetchNewsDetail()
  }, [id])

  if (loading) {
    return (
      <div className='min-h-screen flex items-center justify-center'>
        <div className='animate-spin rounded-full h-12 w-12 border-t-2 border-indigo-600'></div>
      </div>
    )
  }

  if (!news) {
    return (
      <div className='min-h-screen flex items-center justify-center'>
        <p className='text-lg text-gray-600'>Không tìm thấy tin tức.</p>
      </div>
    )
  }

  return (
    <div className='min-h-screen flex flex-col'>
      {/* Header */}
      <header className='bg-white shadow-sm py-20'>
        <div className='container mx-auto px-5 text-center'>
          <h1 className='text-3xl d text-gray-900 '>{news.title}</h1>
          <p className='mt-2 text-sm text-gray-600'>
            {news.author || 'Đội ngũ Admin'} • {formatDate(news.created_at)}
          </p>
        </div>
      </header>

      <main className='flex-grow container mx-auto px-5 py-6'>
        <div className='bg-white rounded-lg shadow-sm border border-gray-200 max-w-3xl mx-auto'>
          <img
            src={news.image || 'https://via.placeholder.com/600x300'}
            alt={news.title}
            className='w-full h-64 object-cover rounded-t-lg'
          />
          <div className='p-5'>
            <div className='text-gray-700 text-base leading-relaxed'>{news.content}</div>
            <Link
              to='/news'
              className='mt-4 inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium'
            >
              <svg
                className='w-4 h-4 mr-1'
                fill='none'
                stroke='currentColor'
                viewBox='0 0 24 24'
                xmlns='http://www.w3.org/2000/svg'
              >
                <path strokeLinecap='round' strokeLinejoin='round' strokeWidth='2' d='M10 19l-7-7m0 0l7-7m-7 7h18' />
              </svg>
              Quay lại
            </Link>
          </div>
        </div>

        {relatedNews.length > 0 && (
          <section className='mt-8 max-w-3xl mx-auto'>
            <h2 className='text-xl font-semibold text-gray-900 mb-4'>Tin tức liên quan</h2>
            <div className='grid grid-cols-1 sm:grid-cols-3 gap-4'>
              {relatedNews.map((item) => (
                <Link
                  key={item.id}
                  to={`/news/${item.id}`}
                  className='bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:bg-gray-50 transition-colors'
                >
                  <img
                    src={item.image || 'https://via.placeholder.com/300x150'}
                    alt={item.title}
                    className='w-full h-32 object-cover'
                  />
                  <div className='p-3'>
                    <h3 className='text-base font-medium text-gray-900 line-clamp-2'>{item.title}</h3>
                    <p className='text-gray-600 text-xs mt-1 line-clamp-2'>{item.content.substring(0, 60)}...</p>
                    <p className='text-gray-500 text-xs mt-2'>
                      {news.author || 'Đội ngũ Admin'} • {formatDate(news.created_at)}
                    </p>
                  </div>
                </Link>
              ))}
            </div>
          </section>
        )}
      </main>
    </div>
  )
}

export default NewsDetail
