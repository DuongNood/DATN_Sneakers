import React, { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'

interface Product {
  id: number
  name: string
  original_price: string
  discounted_price: string
  product_code: string
  imageUrl: string | null
  rating: number
  quantity?: number
}

const ProductDetail = () => {
  const navigate = useNavigate()
  const { id } = useParams()

  const [product, setProduct] = useState<Product | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [quantity, setQuantity] = useState(1)
  const [selectedImage, setSelectedImage] = useState<string | null>(null)
  const [selectedSize, setSelectedSize] = useState<string | null>(null) // State mới để lưu size chọn

  useEffect(() => {
    const fetchProduct = async () => {
      setIsLoading(true)
      try {
        const response = await fetch(`http://localhost:8000/api/detail-product/${id}`)
        if (!response.ok) throw new Error('API không phản hồi')
        const data = await response.json()
        console.log('Dữ liệu từ API:', data)

        const productData = data.data
        setProduct({
          id: productData.id,
          name: productData.product_name,
          original_price: productData.original_price,
          discounted_price: productData.discounted_price,
          imageUrl: productData.image || 'https://via.placeholder.com/300',
          rating: productData.rating || 5,
          product_code: productData.product_code || 'SP123',
          quantity: productData.quantity
        })
        setSelectedImage(productData.image || 'https://via.placeholder.com/300')
      } catch (error) {
        console.error('Lỗi khi fetch sản phẩm:', error)
      } finally {
        setIsLoading(false)
      }
    }

    if (id) fetchProduct()
  }, [id])

  const handleQuantityChange = (change: number) => {
    setQuantity((prev) => {
      const newQuantity = prev + change
      if (newQuantity < 1) return 1
      if (product && newQuantity > product.stock) return product.stock
      return newQuantity
    })
  }

  const handleAddToCart = () => {
    console.log(`Thêm ${quantity} sản phẩm ${product?.name} (Size: ${selectedSize || 'Chưa chọn'}) vào giỏ hàng`)
  }

  const handleBuyNow = () => {
    console.log(`Mua ngay ${quantity} sản phẩm ${product?.name} (Size: ${selectedSize || 'Chưa chọn'})`)
    navigate('/checkout')
  }

  const handleImageClick = (image: string) => {
    setSelectedImage(image)
  }

  const handleSizeClick = (size: string) => {
    setSelectedSize(size) // Cập nhật size được chọn
  }

  if (isLoading) {
    return (
      <div className='flex justify-center items-center h-64'>
        <div className='animate-spin rounded-full h-14 w-14 border-t-3 border-blue-500'></div>
      </div>
    )
  }

  if (!product) {
    return <p className='text-lg sm:text-xl text-center text-gray-600'>Không có sản phẩm nào để hiển thị.</p>
  }

  // Danh sách size giày (có thể thay đổi tùy theo sản phẩm)
  const sizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44']

  return (
    <div className='container mx-auto px-[30px] my-12'>
      <div className='grid grid-cols-1 lg:grid-cols-2 gap-8'>
        <div className='flex flex-col items-center'>
          <div className='relative w-full max-w-sm'>
            <img
              src={selectedImage || product.imageUrl}
              alt={product.name}
              className='w-full h-auto object-cover rounded-lg shadow-md transition-transform duration-300 hover:scale-105'
            />
          </div>
          <div className='flex space-x-2 mt-4'>
            {['red', 'black', 'white', 'green', 'blue'].map((color, index) => (
              <img
                key={index}
                src={product.imageUrl || 'https://via.placeholder.com/300'}
                alt={`${product.name} ${color}`}
                onClick={() => handleImageClick(product.imageUrl || 'https://via.placeholder.com/300')}
                className={`w-14 h-14 object-cover rounded-lg border-2 cursor-pointer transition-all duration-300 ${
                  selectedImage === product.imageUrl ? 'border-red-500' : 'border-gray-300'
                } hover:border-red-500 hover:shadow-md`}
              />
            ))}
          </div>
        </div>

        <div className='flex flex-col space-y-5'>
          <h1 className='text-2xl sm:text-3xl font-bold uppercase text-gray-800'>{product.name}</h1>
          <span className='text-sm sm:text-base text-gray-600'>Mã sản phẩm: {product.product_code}</span>

          {/* <div className='flex items-center space-x-2'>
            <div className='flex'>
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
            <span className='text-sm sm:text-base text-gray-600'>482 Đánh giá</span>
          </div> */}

          <div className='flex items-center space-x-4'>
            <p className='text-lg sm:text-xl text-gray-500 line-through'>
              {(Number(product.original_price) || 0).toLocaleString('vi-VN')} ₫
            </p>
            <p className='text-2xl sm:text-3xl font-semibold text-red-600'>
              {(Number(product.discounted_price) || 0).toLocaleString('vi-VN')} ₫
            </p>
            <p className='text-sm text-white-500 bg-red-500 px-2 text-white border-roudned-md'>
              -
              {Math.round(
                ((Number(product.original_price) - Number(product.discounted_price)) / Number(product.original_price)) *
                  100
              )}
              %
            </p>
          </div>

          <div className='flex flex-col space-y-2'>
            <p className='text-sm sm:text-base font-medium text-gray-700 uppercase'>Chọn size giày</p>
            <div className='flex flex-wrap gap-2'>
              {sizes.map((size) => (
                <button
                  key={size}
                  onClick={() => handleSizeClick(size)}
                  className={`px-3 py-1 border rounded-lg text-sm font-medium transition-all duration-200 sm:px-4 sm:py-2 ${
                    selectedSize === size
                      ? 'bg-blue-500 text-white border-blue-500'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
                  }`}
                >
                  {size}
                </button>
              ))}
            </div>
          </div>
          <div className='flex items-center space-x-4'>
            <p className='text-sm sm:text-base font-medium text-gray-700'>Số lượng</p>
            <div className='flex items-center border border-gray-300 rounded-lg'>
              <button
                onClick={() => handleQuantityChange(-1)}
                className='px-3 py-1 text-lg text-gray-600 hover:bg-gray-100 transition-colors duration-200'
              >
                -
              </button>
              <span className='px-4 py-1 text-base sm:text-lg font-medium'>{quantity}</span>
              <button
                onClick={() => handleQuantityChange(1)}
                className='px-3 py-1 text-lg text-gray-600 hover:bg-gray-100 transition-colors duration-200'
              >
                +
              </button>
            </div>
            <p className='text-sm sm:text-base text-gray-600'>{product.quantity} sản phẩm có sẵn</p>
          </div>

          <div className='flex gap-3'>
            <button
              onClick={handleAddToCart}
              className='bg-yellow-500 text-white px-5 py-2 rounded text-base font-medium transition-all duration-300 ease-in-out hover:bg-yellow-600 hover:shadow-md hover:scale-105'
            >
              Thêm vào giỏ hàng
            </button>
            <button
              onClick={handleBuyNow}
              className='bg-blue-500 text-white px-5 py-2 rounded text-base font-medium transition-all duration-300 ease-in-out hover:bg-blue-600 hover:shadow-md hover:scale-105'
            >
              Mua ngay
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ProductDetail
