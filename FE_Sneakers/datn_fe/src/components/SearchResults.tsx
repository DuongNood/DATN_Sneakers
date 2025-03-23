import React, { useEffect, useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'

const SearchResults: React.FC = () => {
  const [results, setResults] = useState<any[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const location = useLocation()
  const navigate = useNavigate()

  const query = new URLSearchParams(location.search).get('query') || ''

  useEffect(() => {
    const fetchResults = async () => {
      if (!query) {
        setResults([])
        return
      }

      setLoading(true)
      setError(null)

      try {
        const response = await fetch(`http://localhost:8000/api/products?query=${encodeURIComponent(query)}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
        })

        if (!response.ok) {
          throw new Error('Không thể tải dữ liệu')
        }

        const data = await response.json()
        console.log('API data in SearchResults:', data)
        if (data.success && Array.isArray(data.data)) {
          const filteredResults = data.data.filter((item: any) =>
            item.product_name.toLowerCase().includes(query.toLowerCase())
          )
          setResults(filteredResults)
        } else {
          setResults([])
        }
      } catch (err) {
        setError('Có lỗi xảy ra khi tải dữ liệu')
        console.error(err)
      } finally {
        setLoading(false)
      }
    }

    fetchResults()
  }, [query])

  const handleProductClick = (id: number) => {
    navigate(`/detail-product/${id}`)
  }

  return (
    <div className='search-results p-4 max-w-7xl mx-auto'>
      <h1 className='text-2xl font-bold mb-6'>Kết quả tìm kiếm cho: "{query}"</h1>

      {loading && <p className='text-gray-500 text-center'>Đang tải...</p>}
      {error && <p className='text-red-500 text-center'>{error}</p>}

      {results.length > 0 ? (
        <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8'>
          {results.map((item) => (
            <div
              key={item.id}
              className='bg-white shadow-lg rounded-lg p-4 text-center relative cursor-pointer hover:shadow-xl transition-shadow duration-300 h-[18rem]'
              onClick={() => handleProductClick(item.id)}
            >
              {/* Phần trăm giảm giá (chỉ hiển thị nếu có giá) */}
              {item.original_price && item.discounted_price && (
                <div className='absolute top-0 left-0 bg-red-600 text-white text-xs px-2 py-1 rounded-tr-md z-10'>
                  <p className='text-sm text-white-500'>
                    -
                    {Math.round(
                      ((Number(item.original_price) - Number(item.discounted_price)) / Number(item.original_price)) *
                        100
                    )}
                    %
                  </p>
                </div>
              )}

              {/* Hình ảnh với hiệu ứng hover */}
              <div className='relative group'>
                <img
                  src={item.image}
                  alt={item.product_name}
                  className='w-full h-48 object-cover rounded-md mb-4 transition-all duration-300 ease-in-out'
                />
                <div className='absolute inset-0 bg-gradient-to-r from-transparent to-transparent group-hover:from-white group-hover:to-white opacity-30 transition-all duration-300 ease-in-out z-0' />
              </div>

              {/* Tên sản phẩm */}
              <h3 className='text-sm mb-2'>{item.product_name}</h3>

              {/* Giá (chỉ hiển thị nếu có giá) */}
              {item.original_price && item.discounted_price ? (
                <div className='flex justify-center items-center mb-2'>
                  <p className='text-gray-500 line-through text-sm mr-2'>
                    {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Number(item.price))}
                  </p>
                  <p className='text-sm font-semibold text-red-500'>
                    {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(
                      Number(item.discounted_price)
                    )}
                  </p>
                </div>
              ) : (
                <p className='text-sm text-gray-500 mb-2'>Giá không khả dụng</p>
              )}

              {/* Đánh giá (rating) */}
              <div className='my-2'>
                {Array.from({ length: item.rating || 0 }, (_, index) => (
                  <svg
                    key={index}
                    xmlns='http://www.w3.org/2000/svg'
                    fill='yellow'
                    viewBox='0 0 24 24'
                    width='20'
                    height='20'
                    className='inline'
                  >
                    <path d='M12 .587l3.668 7.431 8.232 1.186-5.958 5.759 1.406 8.206-7.348-3.86-7.348 3.86 1.406-8.206-5.958-5.759 8.232-1.186z' />
                  </svg>
                ))}
              </div>
            </div>
          ))}
        </div>
      ) : (
        !loading && <p className='text-gray-500 text-center'>Không tìm thấy kết quả nào</p>
      )}
    </div>
  )
}

export default SearchResults
