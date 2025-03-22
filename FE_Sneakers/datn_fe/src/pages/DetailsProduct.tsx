import React, { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'

interface Product {
  id: number
  name: string
  price: string
  salePrice: string
  imageUrl: string
  rating: number
}

const ProductDetail = () => {
  const navigate = useNavigate()

  const [products, setProducts] = useState<Product[]>([])
  const [currentPage, setCurrentPage] = useState(1)
  const [isLoading, setIsLoading] = useState(true)
  const itemsPerPage = 8

  useEffect(() => {
    const fetchProducts = async () => {
      setIsLoading(true)
      try {
        const response = await fetch('http://localhost:8000/api/home-products')
        if (!response.ok) throw new Error('API không phản hồi')
        const data = await response.json()
        console.log('Dữ liệu từ API:', data)
        setProducts(Array.isArray(data) ? data : data.data || [])
      } catch (error) {
        console.error('Lỗi khi fetch sản phẩm:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchProducts()
  }, [])

  const calculateDiscount = (price: string, salePrice: string): string => {
    const originalPrice = parseFloat(price.replace(/[^0-9.-]+/g, ''))
    const discountedPrice = parseFloat(salePrice.replace(/[^0-9.-]+/g, ''))
    const discountPercentage = ((originalPrice - discountedPrice) / originalPrice) * 100
    return discountPercentage.toFixed(0)
  }

  const handleProductClick = (id: number) => {
    navigate(`/products-detail/${id}`)
  }

  const indexOfLastProduct = currentPage * itemsPerPage
  const indexOfFirstProduct = indexOfLastProduct - itemsPerPage
  const currentProducts = products.slice(indexOfFirstProduct, indexOfLastProduct)
  console.log('Current Products:', currentProducts)

  const paginate = (pageNumber: number) => setCurrentPage(pageNumber)
  const totalPages = Math.ceil(products.length / itemsPerPage)

  return (
    <div className='container mx-auto px-4 sm:px-8 md:px-16 my-12'>
      
      {isLoading ? (
        <div className='flex justify-center items-center h-64'>
          <div className='animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500'></div>
        </div>
      ) : (
        <>
          <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8'>
            {currentProducts.length > 0 ? (
              currentProducts.map((product) => (
                <div
                  key={product.id}
                  className='bg-white shadow-lg rounded-lg p-4 text-center relative cursor-pointer hover:shadow-xl transition-shadow duration-300'
                  onClick={() => handleProductClick(product.id)}
                >
                  {product.salePrice && (
                    <div className='absolute top-0 left-0 bg-red-500 text-white text-xs px-2 py-1 rounded-tr-md z-10'>
                      -{calculateDiscount(product.price, product.salePrice)}%
                    </div>
                  )}
                  <div className='relative group'>
                    <img
                      src={product.imageUrl}
                      alt={product.name}
                      className='w-full h-48 object-cover rounded-md mb-4 transition-all duration-300 ease-in-out'
                    />
                    <div className='absolute inset-0 bg-gradient-to-r from-transparent to-transparent group-hover:from-white group-hover:to-white opacity-30 transition-all duration-300 ease-in-out z-0' />
                  </div>
                  <h3 className='text-xl font-semibold mb-2'>{product.name}</h3>
                  <div className='flex justify-center items-center mb-2'>
                    <p className='text-gray-500 line-through text-sm mr-2'>{product.price}</p>
                    <p className='text-sm font-semibold text-red-500'>{product.salePrice}</p>
                  </div>
                  <div className='my-2'>
                    {Array.from({ length: product.rating }, (_, index) => (
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
              ))
            ) : (
              <p>Không có sản phẩm nào để hiển thị.</p>
            )}
          </div>

          {totalPages > 1 && (
            <div className='flex justify-center mt-8'>
              <button
                onClick={() => paginate(currentPage - 1)}
                className='px-4 py-2 mx-1 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 disabled:opacity-50'
                disabled={currentPage === 1}
              >
                Previous
              </button>
              {Array.from({ length: totalPages }, (_, index) => (
                <button
                  key={index}
                  onClick={() => paginate(index + 1)}
                  className={`px-4 py-2 mx-1 rounded-lg ${
                    currentPage === index + 1 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                  }`}
                >
                  {index + 1}
                </button>
              ))}
              <button
                onClick={() => paginate(currentPage + 1)}
                className='px-4 py-2 mx-1 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 disabled:opacity-50'
                disabled={currentPage === totalPages}
              >
                Next
              </button>
            </div>
          )}
        </>
      )}
    </div>
  )
}

export default ProductDetail
