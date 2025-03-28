import React, { useState, useEffect } from 'react'
import { useParams, useLocation, useNavigate } from 'react-router-dom'
import { FaBox, FaTruck, FaExchangeAlt } from 'react-icons/fa'
import axios from 'axios'
import { toast } from 'react-toastify'

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
  images: string[]
  sizes: { size: string; quantity: number }[]
  category: { id: number; category_name: string }
}

const ProductDetail: React.FC = () => {
  const { slug } = useParams<{ slug: string }>()
  const location = useLocation()
  const navigate = useNavigate()
  const { id } = (location.state as { id?: number }) || {}

  const [product, setProduct] = useState<Product | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [selectedSize, setSelectedSize] = useState<string | null>(null)
  const [selectedSizeQuantity, setSelectedSizeQuantity] = useState<number>(0)
  const [quantity, setQuantity] = useState<number>(1)
  const [selectedImage, setSelectedImage] = useState<string | null>(null)
  const [zoomStyle, setZoomStyle] = useState<React.CSSProperties>({})
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    const fetchProduct = async () => {
      if (!id) {
        setError('Không có ID sản phẩm được cung cấp')
        setIsLoading(false)
        return
      }

      setIsLoading(true)
      try {
        const response = await fetch(`http://localhost:8000/api/detail-product/${id}`)
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`)
        }
        const data = await response.json()
        console.log('Dữ liệu từ API:', data)

        const productData = data.data
        if (!productData) {
          throw new Error('Không có dữ liệu sản phẩm trong phản hồi')
        }

        const newProduct: Product = {
          id: productData.id,
          name: productData.product_name,
          original_price: productData.original_price.toString(),
          discounted_price: productData.discounted_price.toString(),
          imageUrl: productData.image || 'https://via.placeholder.com/500',
          rating: productData.rating || 5,
          description: productData.description || 'Không có mô tả.',
          product_code: productData.product_code || 'SP123',
          quantity:
            productData.quantity ||
            productData.product_variant.reduce((sum: number, variant: any) => sum + variant.quantity, 0),
          images: productData.image_product.map((img: any) => img.image_product) || [],
          sizes:
            productData.product_variant.map((variant: any) => ({
              size: variant.product_size.name,
              quantity: variant.quantity
            })) || [],
          category: {
            id: productData.category.id,
            category_name: productData.category.category_name
          }
        }

        console.log('Product sizes:', newProduct.sizes)
        console.log('Product images:', newProduct.images)
        console.log('Product category:', newProduct.category)
        setProduct(newProduct)
        setSelectedImage(newProduct.imageUrl || (newProduct.images.length > 0 ? newProduct.images[0] : null))
      } catch (error: any) {
        console.error('Lỗi khi fetch sản phẩm:', error)
        setError(error.message || 'Có lỗi xảy ra khi tải dữ liệu sản phẩm')
      } finally {
        setIsLoading(false)
      }
    }

    fetchProduct()
  }, [id])

  const handleQuantityChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = parseInt(e.target.value, 10)
    if (isNaN(value) || value < 1) {
      setQuantity(1)
    } else if (value > selectedSizeQuantity) {
      setQuantity(selectedSizeQuantity)
    } else {
      setQuantity(value)
    }
  }

  const handleAddToCart = async () => {
    if (!product || !selectedSize) {
      toast.error('Vui lòng chọn size trước khi thêm vào giỏ hàng!', { autoClose: 1000 })
      return
    }

    try {
      const response = await axios.post('http://localhost:8000/api/carts/add', {
        product_id: product.id,
        quantity,
        size: selectedSize
      })
      console.log('Thêm vào giỏ hàng:', response.data)
      alert(`Đã thêm ${quantity} sản phẩm ${product.name} (Size: ${selectedSize}) vào giỏ hàng`)
    } catch (error) {
      console.error('Lỗi khi thêm vào giỏ hàng:', error)
      alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!')
    }
  }

  const handleBuyNow = () => {
    if (!product || !selectedSize) {
      alert('Vui lòng chọn size trước khi mua!')
      return
    }
    navigate('/checkout', {
      state: { products: [{ ...product, quantity, variant: selectedSize }], quantity }
    })
  }

  const handleImageClick = (image: string) => {
    setSelectedImage(image)
  }

  const handleSizeClick = (size: string, sizeQuantity: number) => {
    setSelectedSize(size)
    setSelectedSizeQuantity(sizeQuantity)
    setQuantity(1)
  }

  const handleMouseMove = (e: React.MouseEvent<HTMLImageElement>) => {
    const { left, top, width, height } = e.currentTarget.getBoundingClientRect()
    const x = ((e.clientX - left) / width) * 100
    const y = ((e.clientY - top) / height) * 100
    setZoomStyle({ transform: 'scale(2)', transformOrigin: `${x}% ${y}%` })
  }

  const handleMouseLeave = () => {
    setZoomStyle({ transform: 'scale(1)', transformOrigin: 'center center' })
  }

  const SkeletonLoading = () => (
    <div className='container mx-auto px-2 sm:px-10 md:px-20 py-10 sm:py-20 animate-pulse'>
      <div className='flex flex-col md:flex-row gap-6'>
        <div className='md:w-1/2 flex flex-col items-center'>
          <div className='w-full max-w-xs sm:max-w-sm md:max-w-md h-64 bg-gray-300 rounded-lg'></div>
          <div className='flex gap-2 mt-4'>
            {Array(4)
              .fill(0)
              .map((_, index) => (
                <div key={index} className='w-10 h-10 sm:w-14 sm:h-14 bg-gray-300 rounded-lg'></div>
              ))}
          </div>
        </div>
        <div className='md:w-1/2'>
          <div className='h-8 bg-gray-300 rounded w-3/4 mb-2'></div>
          <div className='h-4 bg-gray-300 rounded w-1/2 mb-4'></div>
          <div className='h-6 bg-gray-300 rounded w-1/4 mb-2'></div>
          <div className='h-6 bg-gray-300 rounded w-1/3 mb-4'></div>
          <div className='h-4 bg-gray-300 rounded w-full mb-2'></div>
          <div className='h-4 bg-gray-300 rounded w-3/4'></div>
        </div>
      </div>
    </div>
  )

  if (isLoading) return <SkeletonLoading />
  if (error) return <p className='text-lg text-center text-red-600'>{error}</p>
  if (!product || !id) {
    return <p className='text-lg sm:text-xl text-center text-gray-600'>Không có sản phẩm nào để hiển thị.</p>
  }

  return (
    <div className='container mx-auto px-2 sm:px-10 md:px-20 py-10 sm:py-20 flex flex-col md:flex-row gap-6'>
      <div className='md:w-1/2 flex flex-col items-center'>
        <div className='relative w-full h-[400px] max-w-xs sm:max-w-sm md:max-w-md overflow-hidden'>
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
          {product.images.length > 0 ? (
            product.images.map((image, index) => (
              <img
                key={index}
                src={image}
                alt={`${product.name} variant ${index + 1}`}
                onClick={() => handleImageClick(image)}
                className={`w-17 h-1 sm:w-20 sm:h-14 object-cover rounded-lg border-2 cursor-pointer transition-all duration-300 ${
                  selectedImage === image ? 'border-red-500' : 'border-gray-300'
                } hover:border-red-500 hover:shadow-md`}
              />
            ))
          ) : (
            <p className='text-sm text-gray-500'>Không có ảnh con nào để hiển thị</p>
          )}
        </div>
      </div>

      <div className='md:w-1/2 px-[30px] md:px-0'>
        <h1 className='text-xl sm:text-2xl font-bold'>{product.name}</h1>
        <p className='text-green-600 text-sm mt-1'>{product.quantity ? 'Còn Hàng' : 'Hết Hàng'}</p>
        <div className='flex flex-wrap gap-4 text-sm mt-1'>
          <span className='text-blue-500'>Thương hiệu: {product.category.category_name}</span> |
          <span className='text-blue-500'>Mã sản phẩm: {product.product_code}</span>
        </div>

        <div className='mt-4 flex items-center gap-2'>
          {product.original_price && (
            <p className='text-base sm:text-lg text-gray-500 line-through'>
              {Number(product.original_price).toLocaleString('vi-VN')}đ
            </p>
          )}
          <p className='text-xl sm:text-2xl font-bold'>{Number(product.discounted_price).toLocaleString('vi-VN')}đ</p>
          {product.original_price && Number(product.original_price) > Number(product.discounted_price) && (
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

        <div className='mt-4'>
          <p className='text-sm font-semibold'>Chọn Size Giày:</p>
          <div className='flex gap-2 mt-2 flex-wrap'>
            {product.sizes.length > 0 ? (
              product.sizes.map((sizeObj) => {
                const { size, quantity: sizeQuantity } = sizeObj
                const isAvailable = sizeQuantity > 0
                return (
                  <button
                    key={size}
                    onClick={() => isAvailable && handleSizeClick(size, sizeQuantity)}
                    disabled={!isAvailable}
                    className={`w-10 h-10 rounded-full border flex items-center justify-center text-sm sm:text-base ${
                      selectedSize === size
                        ? 'border-blue-500 bg-blue-500 text-white'
                        : isAvailable
                          ? 'border-gray-300 hover:border-black hover:bg-gray-100'
                          : 'border-gray-300 bg-gray-100 opacity-50 cursor-not-allowed'
                    } transition`}
                  >
                    {size}
                  </button>
                )
              })
            ) : (
              <p className='text-sm text-gray-500'>Không có size nào để hiển thị</p>
            )}
          </div>
        </div>

        <div className='mt-4 flex items-center gap-2'>
          <p className='text-sm font-semibold'>Số lượng:</p>
          <input
            type='number'
            value={quantity}
            onChange={handleQuantityChange}
            min='1'
            max={selectedSizeQuantity}
            disabled={!selectedSize}
            className='w-16 h-8 border rounded-md text-center text-base focus:outline-none focus:ring-2 focus:ring-blue-500'
          />
          <p className='text-sm text-gray-600 capitalize'>
            {selectedSize ? `${selectedSizeQuantity} sản phẩm có sẵn cho size ${selectedSize}` : 'Vui lòng chọn size'}
          </p>
        </div>

        <div className='mt-4 flex gap-2'>
          <button
            onClick={handleAddToCart}
            className='bg-yellow-500 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-yellow-600 transition text-sm sm:text-base capitalize'
          >
            Thêm vào giỏ
          </button>
          <button
            onClick={handleBuyNow}
            className='bg-blue-500 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-600 transition text-sm sm:text-base capitalize'
          >
            Mua ngay
          </button>
        </div>

        <div className='mt-4'>
          <p className='text-sm font-semibold capitalize'>Mô tả sản phẩm:</p>
          <div className='text-sm text-gray-600 mt-1' dangerouslySetInnerHTML={{ __html: product.description }} />
        </div>

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
