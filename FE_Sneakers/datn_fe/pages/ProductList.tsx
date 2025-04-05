import React, { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'

interface Product {
  id: number
  product_name: string
  original_price: string
  discounted_price: string
  image: string
  rating: number
}

// slug url
const createSlug = (name: string): string => {
  return name
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

const ProductList = () => {
  const navigate = useNavigate()

  const [products, setProducts] = useState<Product[]>([])
  const [currentPage, setCurrentPage] = useState(1)
  const [isLoading, setIsLoading] = useState(true)
  const itemsPerPage = 12

  useEffect(() => {
    const fetchProducts = async () => {
      setIsLoading(true)
      try {
        const response = await fetch('http://localhost:8000/api/home-products')
        if (!response.ok) throw new Error('API không phản hồi')
        const data = await response.json()
        setProducts(Array.isArray(data) ? data : data.data || [])
      } catch (error) {
        console.error('Lỗi khi fetch sản phẩm:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchProducts()
  }, [])

  const handleProductClick = (id: number, productName: string) => {
    const slug = createSlug(productName)
    navigate(`/${slug}`, { state: { id } })
  }

  const indexOfLastProduct = currentPage * itemsPerPage
  const indexOfFirstProduct = indexOfLastProduct - itemsPerPage
  const currentProducts = products.slice(indexOfFirstProduct, indexOfLastProduct)

  const paginate = (pageNumber: number) => setCurrentPage(pageNumber)
  const totalPages = Math.ceil(products.length / itemsPerPage)

  const SkeletonLoading = () => (
    <div className=''>
      <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8'>
        {Array(itemsPerPage)
          .fill(0)
          .map((_, index) => (
            <div key={index} className='bg-white shadow-lg rounded-lg p-4 text-center relative h-[18rem]'>
              <div className='absolute top-0 left-0 h-6 w-12 bg-gray-300 rounded-tr-md'></div>
              <div className='w-full h-48 bg-gray-300 rounded-md mb-4'></div>
              <div className='h-4 w-3/4 bg-gray-300 rounded mx-auto mb-2'></div>
              <div className='flex justify-center items-center mb-2 space-x-2'>
                <div className='h-4 w-16 bg-gray-300 rounded'></div>
                <div className='h-4 w-20 bg-gray-300 rounded'></div>
              </div>
            </div>
          ))}
      </div>
    </div>
  )

  return (
    <div className='container mx-auto px-4 sm:px-8 md:px-16 my-12'>
      {isLoading ? (
        <SkeletonLoading />
      ) : (
        <>
          <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8'>
            {currentProducts.length > 0 ? (
              currentProducts.map((product) => (
                <div
                  key={product.id}
                  className='bg-white shadow-lg rounded-lg p-4 text-center relative cursor-pointer hover:shadow-xl transition-shadow duration-300 h-[18rem]'
                  onClick={() => handleProductClick(product.id, product.product_name)}
                >
                  <div className='absolute top-0 left-0 bg-red-600 text-white text-xs px-2 py-1 rounded-tr-md z-10'>
                    <p className='text-sm text-white-500'>
                      -
                      {Math.round(
                        ((Number(product.original_price) - Number(product.discounted_price)) /
                          Number(product.original_price)) *
                          100
                      )}
                      %
                    </p>
                  </div>

                  <div className='relative group'>
                    <img
                      src={product.image}
                      alt={product.product_name}
                      className='w-full h-48 object-cover rounded-md mb-4 transition-all duration-300 ease-in-out'
                    />
                    <div className='absolute inset-0 bg-gradient-to-r from-transparent to-transparent group-hover:from-white group-hover:to-white opacity-30 transition-all duration-300 ease-in-out z-0' />
                  </div>
                  <h3 className='text-sm mb-2 whitespace-nowrap overflow-hidden text-ellipsis'>
                    {product.product_name}
                  </h3>
                  <div className='flex justify-center items-center mb-2'>
                    <p className='text-gray-500 line-through text-sm mr-2'>
                      {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(
                        Number(product.original_price)
                      )}
                    </p>
                    <p className='text-sm text-red-500 font-bold'>
                      {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(
                        Number(product.discounted_price)
                      )}
                    </p>
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

export default ProductList
