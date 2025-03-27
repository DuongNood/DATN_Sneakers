import React, { useState, useEffect } from 'react'
import { useParams, useLocation, useNavigate } from 'react-router-dom'
import { FaBox, FaTruck, FaExchangeAlt } from 'react-icons/fa'

// Định nghĩa interface Product
interface Product {
  id: number
  name: string
  original_price: string
  discounted_price: string
  product_code: string
  imageUrl: string | null
  rating: number
  description: string
  quantity?: number
  images?: string[]
}

const ProductDetail = () => {
  const { slug } = useParams<{ slug: string }>() // Lấy slug từ URL
  const location = useLocation()
  const navigate = useNavigate()

  // Lấy id từ state
  const { id } = (location.state as { id?: number }) || {}

  const [product, setProduct] = useState<Product | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [selectedSize, setSelectedSize] = useState<string | null>(null)
  const [quantity, setQuantity] = useState(1)
  const [selectedImage, setSelectedImage] = useState<string | null>(null)
  const [zoomStyle, setZoomStyle] = useState<React.CSSProperties>({})

  // Gọi API khi component được mount
  useEffect(() => {
    const fetchProduct = async () => {
      if (!id) {
        setIsLoading(false)
        return
      }

      setIsLoading(true)
      try {
        const response = await fetch(`http://localhost:8000/api/detail-product/${id}`)
        if (!response.ok) throw new Error('API không phản hồi')
        const data = await response.json()
        const productData = data.data

        const defaultImages = [
          'https://via.placeholder.com/100',
          'https://via.placeholder.com/100',
          'https://via.placeholder.com/100',
          'https://via.placeholder.com/100',
          'https://via.placeholder.com/100'
        ]
        const apiImages = productData.images || []
        const images = [...apiImages, ...defaultImages].slice(0, 5)

        setProduct({
          id: productData.id,
          name: productData.product_name,
          original_price: productData.original_price,
          discounted_price: productData.discounted_price,
          imageUrl: productData.image || 'https://via.placeholder.com/500',
          rating: productData.rating || 5,
          description: productData.description || 'Không có mô tả.',
          product_code: productData.product_code || 'SP123',
          quantity: productData.quantity,
          images: images
        })
        setSelectedImage(productData.image || 'https://via.placeholder.com/500')
      } catch (error) {
        console.error('Lỗi khi fetch sản phẩm:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchProduct()
  }, [id])

  // Xử lý thay đổi số lượng
  const handleQuantityChange = (change: number) => {
    setQuantity((prev) => {
      const newQuantity = prev + change
      if (newQuantity < 1) return 1
      if (product && newQuantity > (product.quantity || 0)) return product.quantity || 0
      return newQuantity
    })
  }

  // Xử lý thêm vào giỏ hàng
  const handleAddToCart = () => {
    console.log(`Thêm ${quantity} sản phẩm ${product?.name} (Size: ${selectedSize || 'Chưa chọn'}) vào giỏ hàng`)
  }

  // Xử lý mua ngay
  const handleBuyNow = () => {
    console.log(`Mua ngay ${quantity} sản phẩm ${product?.name} (Size: ${selectedSize || 'Chưa chọn'})`)
    navigate('/checkout', {
      state: { products: [{ ...product, quantity, variant: selectedSize }], quantity }
    })
  }

  // Xử lý chọn hình ảnh
  const handleImageClick = (image: string) => {
    setSelectedImage(image)
  }

  // Xử lý chọn kích thước
  const handleSizeClick = (size: string) => {
    setSelectedSize(size)
  }

  // Xử lý hiệu ứng zoom khi hover
  const handleMouseMove = (e: React.MouseEvent<HTMLImageElement>) => {
    const { left, top, width, height } = e.currentTarget.getBoundingClientRect()
    const x = ((e.clientX - left) / width) * 100
    const y = ((e.clientY - top) / height) * 100

    setZoomStyle({
      transform: 'scale(2)',
      transformOrigin: `${x}% ${y}%`
    })
  }

  // Reset zoom khi rời chuột
  const handleMouseLeave = () => {
    setZoomStyle({
      transform: 'scale(1)',
      transformOrigin: 'center center'
    })
  }

  // Dữ liệu kích thước
  const sizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44']

  // Skeleton loading
  const SkeletonLoading = () => (
    <div className='container mx-auto px-2 sm:px-10 md:px-20 py-10 sm:py-20 animate-pulse'>
      <div className='flex flex-col md:flex-row gap-6'>
        {/* Phần hình ảnh sản phẩm */}
        <div className='md:w-1/2 flex flex-col items-center'>
          <div className='relative w-full max-w-xs sm:max-w-sm md:max-w-md h-64 bg-gray-300 rounded-lg'></div>
          <div className='flex gap-2 mt-4 flex-wrap justify-center'>
            {Array(5)
              .fill(0)
              .map((_, index) => (
                <div key={index} className='w-10 h-10 sm:w-14 sm:h-14 bg-gray-300 rounded-lg'></div>
              ))}
          </div>
        </div>

        {/* Phần thông tin sản phẩm */}
        <div className='md:w-1/2 px-[30px] md:px-0'>
          {/* Tiêu đề sản phẩm */}
          <div className='h-8 w-3/4 bg-gray-300 rounded'></div>

          {/* Trạng thái hàng */}
          <div className='h-4 w-20 bg-gray-300 rounded mt-1'></div>

          {/* Thông tin bổ sung (Thương hiệu, Loại, Mã sản phẩm) */}
          <div className='flex flex-wrap gap-4 mt-1'>
            <div className='h-4 w-24 bg-gray-300 rounded'></div>
            <div className='h-4 w-24 bg-gray-300 rounded'></div>
            <div className='h-4 w-24 bg-gray-300 rounded'></div>
          </div>

          {/* Giá */}
          <div className='mt-4 flex items-center gap-2'>
            <div className='h-5 w-24 bg-gray-300 rounded'></div>
            <div className='h-7 w-28 bg-gray-300 rounded'></div>
            <div className='h-5 w-12 bg-gray-300 rounded'></div>
          </div>

          {/* Kích thước */}
          <div className='mt-4'>
            <div className='h-4 w-28 bg-gray-300 rounded'></div>
            <div className='flex gap-2 mt-2 flex-wrap'>
              {sizes.map((_, index) => (
                <div key={index} className='w-10 h-10 bg-gray-300 rounded-full'></div>
              ))}
            </div>
          </div>

          {/* Số lượng */}
          <div className='mt-4 flex items-center gap-2'>
            <div className='w-8 h-8 bg-gray-300 rounded-md'></div>
            <div className='w-8 h-6 bg-gray-300 rounded'></div>
            <div className='w-8 h-8 bg-gray-300 rounded-md'></div>
            <div className='h-4 w-28 bg-gray-300 rounded'></div>
          </div>

          {/* Nút hành động */}
          <div className='mt-4 flex gap-2'>
            <div className='w-32 h-10 bg-gray-300 rounded-md'></div>
            <div className='w-32 h-10 bg-gray-300 rounded-md'></div>
          </div>

          {/* Mô tả sản phẩm */}
          <div className='mt-4'>
            <div className='h-4 w-32 bg-gray-300 rounded'></div>
            <div className='mt-1 space-y-2'>
              <div className='h-4 w-full bg-gray-300 rounded'></div>
              <div className='h-4 w-3/4 bg-gray-300 rounded'></div>
              <div className='h-4 w-1/2 bg-gray-300 rounded'></div>
            </div>
          </div>

          {/* Thông tin bổ sung */}
          <div className='mt-6 flex flex-col sm:flex-row gap-4'>
            <div className='flex items-center gap-2'>
              <div className='w-5 h-5 bg-gray-300 rounded-full'></div>
              <div className='h-4 w-40 bg-gray-300 rounded'></div>
            </div>
            <div className='flex items-center gap-2'>
              <div className='w-5 h-5 bg-gray-300 rounded-full'></div>
              <div className='h-4 w-40 bg-gray-300 rounded'></div>
            </div>
            <div className='flex items-center gap-2'>
              <div className='w-5 h-5 bg-gray-300 rounded-full'></div>
              <div className='h-4 w-40 bg-gray-300 rounded'></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )

  if (isLoading) {
    return <SkeletonLoading />
  }

  if (!product || !id) {
    return <p className='text-lg sm:text-xl text-center text-gray-600'>Không có sản phẩm nào để hiển thị.</p>
  }

  return (
    <div className='container mx-auto px-2 sm:px-10 md:px-20 py-10 sm:py-20 flex flex-col md:flex-row gap-6'>
      {/* Phần hình ảnh sản phẩm */}
      <div className='md:w-1/2 flex flex-col items-center'>
        <div className='relative w-full max-w-xs sm:max-w-sm md:max-w-md overflow-hidden'>
          <img
            src={selectedImage || product.imageUrl}
            alt={product.name}
            className='w-full h-auto object-cover rounded-lg shadow-md transition-transform duration-300'
            style={zoomStyle}
            onMouseMove={handleMouseMove}
            onMouseLeave={handleMouseLeave}
          />
        </div>
        <div className='flex gap-2 mt-4 flex-wrap justify-center'>
          {(product.images || []).slice(0, 5).map((image, index) => (
            <img
              key={index}
              src={image}
              alt={`${product.name} variant ${index + 1}`}
              onClick={() => handleImageClick(image)}
              className={`w-10 h-10 sm:w-14 sm:h-14 object-cover rounded-lg border-2 cursor-pointer transition-all duration-300 ${
                selectedImage === image ? 'border-red-500' : 'border-gray-300'
              } hover:border-red-500 hover:shadow-md`}
            />
          ))}
        </div>
      </div>

      {/* Phần thông tin sản phẩm */}
      <div className='md:w-1/2 px-[30px] md:px-0'>
        <h1 className='text-xl sm:text-2xl font-bold'>{product.name}</h1>
        <p className='text-green-600 text-sm mt-1'>{product.quantity ? 'Còn Hàng' : 'Hết Hàng'}</p>
        <div className='flex flex-wrap gap-4 text-sm mt-1'>
          <span className='text-blue-500'>Thương hiệu: New Balance</span> |
          <span className='text-blue-500'>Mã sản phẩm: {product.product_code}</span>
        </div>

        {/* Giá */}
        <div className='mt-4 flex items-center gap-2'>
          {product.original_price && (
            <p className='text-base sm:text-lg text-gray-500 line-through'>
              {Number(product.original_price).toLocaleString('vi-VN')}đ
            </p>
          )}
          <p className='text-xl sm:text-2xl font-bold'>{Number(product.discounted_price).toLocaleString('vi-VN')}đ</p>

          {product.original_price && (
            <p className='text-sm text-white bg-red-500 px-2 rounded-md py-1'>
              -
              {Math.round(
                ((Number(product.original_price) - Number(product.discounted_price)) / Number(product.original_price)) *
                  100
              )}
              %
            </p>
          )}
        </div>

        {/* Kích thước */}
        <div className='mt-4'>
          <p className='text-sm font-semibold'>Chọn Size Giày:</p>
          <div className='flex gap-2 mt-2 flex-wrap'>
            {sizes.map((size) => (
              <button
                key={size}
                onClick={() => handleSizeClick(size)}
                className={`w-10 h-10 rounded-full border flex items-center justify-center text-sm sm:text-base ${
                  selectedSize === size ? 'border-black' : 'border-gray-300'
                } hover:border-black transition`}
              >
                {size}
              </button>
            ))}
          </div>
        </div>

        {/* Số lượng */}
        <div className='mt-4 flex items-center gap-2'>
          <button onClick={() => handleQuantityChange(-1)} className='w-8 h-8 border rounded-md text-lg sm:text-xl'>
            −
          </button>
          <span className='w-8 text-center text-base sm:text-lg'>{quantity}</span>
          <button onClick={() => handleQuantityChange(1)} className='w-8 h-8 border rounded-md text-lg sm:text-xl'>
            +
          </button>
          <p className='text-sm text-gray-600 capitalize'>{product.quantity} sản phẩm có sẵn</p>
        </div>

        {/* Nút hành động */}
        <div className='mt-4 flex gap-2'>
          <button
            onClick={handleAddToCart}
            className='bg-yellow-500 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-yellow-600 transition text-sm sm:text-base capitalize'
          >
            Thêm vào giỏ
          </button>
          <button
            onClick={handleBuyNow}
            className='border border-none bg-blue-500 text-gray-100 px-4 sm:px-6 py-2 rounded-md hover:bg-blue-600 hover:text-white transition text-sm sm:text-base capitalize'
          >
            Mua ngay
          </button>
        </div>

        {/* Mô tả sản phẩm */}
        <div className='mt-4'>
          <p className='text-sm font-semibold capitalize'>Mô tả sản phẩm:</p>
          <p className='text-sm text-gray-600 mt-1'>{product.description}</p>
        </div>

        {/* Thông tin bổ sung */}
        <div className='mt-6 flex flex-col sm:flex-row gap-4 capitalize'>
          <div className='flex items-center gap-2'>
            <FaBox className='text-red-500' />
            <p className='text-sm'>Đóng gói cẩn thận double box</p>
          </div>
          <div className='flex items-center gap-2'>
            <FaExchangeAlt className='text-red-500' />
            <p className='text-sm'>Miễn phí đổi hàng trong 07 ngày</p>
          </div>
          <div className='flex items-center gap-2'>
            <FaTruck className='text-red-500' />
            <p className='text-sm'>Giao hàng nhanh toàn quốc</p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ProductDetail
