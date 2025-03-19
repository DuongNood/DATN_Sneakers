import React, { useState, useEffect } from 'react'
import { useLocation } from 'react-router-dom'

interface Product {
  id: number
  name: string
  price: string
  salePrice: string
  imageUrl: string
  rating: number
}

const ProductHot = () => {
  const location = useLocation()

  if (location.pathname !== '/') {
    return null
  }

  const [products, setProducts] = useState<Product[]>([])
  const [currentPage, setCurrentPage] = useState(1)
  const itemsPerPage = 12

  useEffect(() => {
    const fetchProducts = async () => {
      const data: Product[] = Array.from({ length: 30 }, (_, index) => ({
        id: index + 1,
        name: `Sản phẩm ${index + 1}`,
        price: '1,200,000 đ',
        salePrice: '700,000 đ',
        imageUrl:
          'https://kingshoes.vn/data/upload/thumb/px231was575c-giay-jeep-nau-gia-tot-den-king-shoes-12jpeg/500_px231was575c-giay-jeep-nau-gia-tot-den-king-shoes-12.jpeg.webp',
        rating: 5
      }))
      setProducts(data)
    }

    fetchProducts()
  }, [])

  // Hàm tính toán tỷ lệ giảm giá
  const calculateDiscount = (price: string, salePrice: string): string => {
    const originalPrice = parseFloat(price.replace(/[^0-9.-]+/g, ''))
    const discountedPrice = parseFloat(salePrice.replace(/[^0-9.-]+/g, ''))
    const discountPercentage = ((originalPrice - discountedPrice) / originalPrice) * 100
    return discountPercentage.toFixed(0)
  }

  // Xử lý phân trang
  const indexOfLastProduct = currentPage * itemsPerPage
  const indexOfFirstProduct = indexOfLastProduct - itemsPerPage
  const currentProducts = products.slice(indexOfFirstProduct, indexOfLastProduct)

  const paginate = (pageNumber: number) => setCurrentPage(pageNumber)

  const totalPages = Math.ceil(products.length / itemsPerPage)

  return (
    <div className='container mx-auto px-4 sm:px-8 md:px-16 my-12'>
      <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8'>
        {currentProducts.map((product) => (
          <div key={product.id} className='bg-white shadow-lg rounded-lg p-4 text-center relative'>
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
              {Array.from({ length: 5 }, (_, index) => (
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

      {/* Phân trang */}
      <div className='flex justify-center mt-8'>
        <button
          onClick={() => paginate(currentPage - 1)}
          className='px-4 py-2 mx-1 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 disabled:opacity-50'
          disabled={currentPage === 1}
        >
          Previous
        </button>

        {/* Hiển thị các số trang */}
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
    </div>
  )
}

export default ProductHot
