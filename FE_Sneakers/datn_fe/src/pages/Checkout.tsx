import React, { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'

// Định nghĩa interface
interface Product {
  id: number
  name: string
  original_price: string
  discounted_price: string
  product_code: string
  imageUrl: string | null
  rating: number
  description: string
  quantity: number
  variant: string
}

interface CheckoutState {
  products: Product[]
  quantity: number
}

const Checkout: React.FC = () => {
  // Nhận dữ liệu sản phẩm từ ProductDetail qua useLocation
  const location = useLocation()
  const { products: initialProducts, quantity: initialQuantity } = (location.state as CheckoutState) || {
    products: [],
    quantity: 1
  }

  // Dữ liệu giả lập (mock data) với 3 sản phẩm
  const mockProducts: Product[] = [
    {
      id: 1,
      name: 'Son kem TINT Bóng Hàn Quốc Ro mand Juicy Lasting Tint 5.5g',
      original_price: '180000',
      discounted_price: '149000',
      product_code: 'SONKEM001',
      imageUrl: 'https://via.placeholder.com/80',
      rating: 4.5,
      description: 'Son kem chất lượng cao',
      quantity: 2,
      variant: '#13 Eat Dotori'
    },
    {
      id: 2,
      name: 'Mặt nạ dưỡng da Innisfree My Real Squeeze Mask',
      original_price: '30000',
      discounted_price: '25000',
      product_code: 'MATNA001',
      imageUrl: 'https://via.placeholder.com/80',
      rating: 4.0,
      description: 'Mặt nạ dưỡng da tự nhiên',
      quantity: 3,
      variant: 'Aloe'
    },
    {
      id: 3,
      name: 'Sữa rửa mặt Cetaphil Gentle Skin Cleanser 500ml',
      original_price: '350000',
      discounted_price: '320000',
      product_code: 'SRM001',
      imageUrl: 'https://via.placeholder.com/80',
      rating: 4.8,
      description: 'Sữa rửa mặt dịu nhẹ',
      quantity: 1,
      variant: 'Không mùi'
    }
  ]

  // Sử dụng mock data nếu initialProducts rỗng
  const [products, setProducts] = useState<Product[]>(
    initialProducts.length > 0
      ? initialProducts.map((product) => ({
          ...product,
          quantity: initialQuantity,
          variant: product.variant || '#13 Eat Dotori'
        }))
      : mockProducts
  )

  // State cho checkbox chọn tất cả
  const [selectAll, setSelectAll] = useState<boolean>(false)
  // State cho danh sách sản phẩm được chọn
  const [selectedProducts, setSelectedProducts] = useState<number[]>([])

  // Xử lý chọn từng sản phẩm
  const handleSelectProduct = (productId: number) => {
    setSelectedProducts((prev) =>
      prev.includes(productId) ? prev.filter((id) => id !== productId) : [...prev, productId]
    )
  }

  // Xử lý chọn tất cả
  const handleSelectAll = () => {
    if (selectAll) {
      setSelectedProducts([])
    } else {
      setSelectedProducts(products.map((product) => product.id))
    }
    setSelectAll(!selectAll)
  }

  // Tính tổng tiền (chỉ tính các sản phẩm được chọn)
  const calculateSubtotal = (): number => {
    return products.reduce(
      (total, product) =>
        selectedProducts.includes(product.id) ? total + Number(product.discounted_price) * product.quantity : total,
      0
    )
  }

  // Tính tổng số lượng sản phẩm được chọn
  const totalSelectedItems = products.reduce(
    (total, product) => (selectedProducts.includes(product.id) ? total + product.quantity : total),
    0
  )

  const shippingFee: number = 0 // Phí vận chuyển (miễn phí)
  const discount: number = selectedProducts.length > 0 ? 44700 : 0 // Giả lập giảm giá
  const total: number = calculateSubtotal() + shippingFee - discount
  const savings: number =
    products.reduce(
      (total, product) =>
        selectedProducts.includes(product.id) ? total + Number(product.original_price) * product.quantity : total,
      0
    ) -
    calculateSubtotal() +
    discount // Tiết kiệm

  // Xử lý tăng số lượng
  const handleIncreaseQuantity = (productId: number) => {
    setProducts((prev) =>
      prev.map((product) => (product.id === productId ? { ...product, quantity: product.quantity + 1 } : product))
    )
  }

  // Xử lý giảm số lượng
  const handleDecreaseQuantity = (productId: number) => {
    setProducts((prev) =>
      prev.map((product) =>
        product.id === productId && product.quantity > 1 ? { ...product, quantity: product.quantity - 1 } : product
      )
    )
  }

  // Xử lý xóa sản phẩm
  const handleDeleteProduct = (productId: number) => {
    setProducts((prev) => prev.filter((product) => product.id !== productId))
    setSelectedProducts((prev) => prev.filter((id) => id !== productId))
  }

  // Xử lý đặt hàng
  const handlePlaceOrder = () => {
    if (selectedProducts.length === 0) {
      alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng!')
      return
    }
    alert('Đặt hàng thành công!')
  }

  return (
    <div className='min-h-screen bg-gray-50 font-sans'>
      <div className='container mx-auto px-4 py-6 lg:py-10 lg:px-20'>
        {/* Header */}
        <div className='hidden lg:flex items-center bg-white p-4 rounded-lg shadow-sm border-b border-gray-200 text-gray-700'>
          <div className='w-1/12'>
            <input
              type='checkbox'
              checked={selectAll}
              onChange={handleSelectAll}
              className='h-5 w-5 text-orange-500 rounded border-gray-300 focus:ring-orange-500'
            />
          </div>
          <div className='w-4/12 font-medium text-sm'>Sản Phẩm</div>
          <div className='w-2/12 font-medium text-sm text-center'>Đơn Giá</div>
          <div className='w-2/12 font-medium text-sm text-center'>Số Lượng</div>
          <div className='w-2/12 font-medium text-sm text-center'>Số Tiền</div>
          <div className='w-1/12 font-medium text-sm text-center'>Thao Tác</div>
        </div>

        {/* Danh sách sản phẩm */}
        {products.length === 0 ? (
          <div className='p-6 text-center text-gray-500 bg-white rounded-lg shadow-sm mt-4'>Giỏ hàng trống</div>
        ) : (
          products.map((product) => (
            <div
              key={product.id}
              className='flex flex-col lg:flex-row items-start lg:items-center p-4 bg-white rounded-lg shadow-sm mt-4 border border-gray-100 hover:shadow-md transition-shadow'
            >
              {/* Checkbox */}
              <div className='w-full lg:w-1/12 flex items-center mb-3 lg:mb-0'>
                <input
                  type='checkbox'
                  checked={selectedProducts.includes(product.id)}
                  onChange={() => handleSelectProduct(product.id)}
                  className='h-5 w-5 text-orange-500 rounded border-gray-300 focus:ring-orange-500'
                />
              </div>

              {/* Sản phẩm (Hình ảnh + Tên + Phân loại) */}
              <div className='w-full lg:w-4/12 flex items-center mb-3 lg:mb-0'>
                <img
                  src={product.imageUrl || 'https://via.placeholder.com/80'}
                  alt={product.name}
                  className='w-16 h-16 lg:w-20 lg:h-20 object-cover rounded-lg mr-4 border border-gray-100'
                />
                <div>
                  <p className='text-sm lg:text-base font-medium text-gray-900'>{product.name}</p>
                  <p className='text-xs text-gray-500 mt-1'>Phân Loại Hàng: {product.variant}</p>
                </div>
              </div>

              {/* Đơn giá */}
              <div className='w-full lg:w-2/12 flex lg:justify-center mb-3 lg:mb-0'>
                <div>
                  <p className='text-sm text-gray-400 line-through lg:text-center'>
                    ₫{Number(product.original_price).toLocaleString('vi-VN')}
                  </p>
                  <p className='text-sm lg:text-base font-medium text-gray-900 lg:text-center'>
                    ₫{Number(product.discounted_price).toLocaleString('vi-VN')}
                  </p>
                </div>
              </div>

              {/* Số lượng */}
              <div className='w-full lg:w-2/12 flex lg:justify-center mb-3 lg:mb-0'>
                <div className='flex items-center border border-gray-200 rounded-lg overflow-hidden'>
                  <button
                    onClick={() => handleDecreaseQuantity(product.id)}
                    className='px-3 py-1 text-gray-600 hover:bg-gray-100 transition'
                  >
                    -
                  </button>
                  <span className='px-4 py-1 text-sm text-gray-900 border-x border-gray-200'>{product.quantity}</span>
                  <button
                    onClick={() => handleIncreaseQuantity(product.id)}
                    className='px-3 py-1 text-gray-600 hover:bg-gray-100 transition'
                  >
                    +
                  </button>
                </div>
              </div>

              {/* Số tiền và Thao tác (nút Xóa) */}
              <div className='w-full lg:w-3/12 flex flex-row justify-between lg:justify-center items-center mb-3 lg:mb-0'>
                {/* Số tiền */}
                <div className='text-orange-500 font-medium text-sm lg:text-base lg:text-center lg:w-2/3'>
                  ₫{(Number(product.discounted_price) * product.quantity).toLocaleString('vi-VN')}
                </div>
                {/* Thao tác */}
                <div className='lg:w-1/3 flex lg:justify-center'>
                  <button
                    onClick={() => handleDeleteProduct(product.id)}
                    className='text-orange-500 hover:text-orange-600 text-sm font-medium transition'
                  >
                    Xóa
                  </button>
                </div>
              </div>
            </div>
          ))
        )}

        {/* Thông tin đơn hàng */}
        <div className='mt-6 p-4 lg:p-6 bg-white rounded-lg shadow-sm border border-gray-100'>
          {/* Địa điểm nhận hàng */}
          <div className='flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4'>
            <div className='flex items-center mb-2 lg:mb-0'>
              <span className='text-sm text-gray-600 font-medium'>Địa điểm nhận hàng:</span>
              <span className='text-sm text-orange-500 ml-2 font-medium'>
                {selectedProducts.length > 0 ? `Đã giảm ₫${discount.toLocaleString('vi-VN')}` : 'Chưa có giảm giá'}
              </span>
            </div>
            <a href='#' className='text-sm text-blue-500 hover:underline font-medium'>
              Xem thêm voucher
            </a>
          </div>

          {/* Phí vận chuyển */}
          <div className='flex items-center mb-4'>
            <svg
              className='w-5 h-5 text-gray-500 mr-2'
              fill='none'
              stroke='currentColor'
              viewBox='0 0 24 24'
              xmlns='http://www.w3.org/2000/svg'
            >
              <path
                strokeLinecap='round'
                strokeLinejoin='round'
                strokeWidth='2'
                d='M20 7h-4V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 5h4v2H8V5zm-4 4h16v10H4V9z'
              />
            </svg>
            <span className='text-sm text-gray-600 font-medium'>Giảm ₫500.000 phí vận chuyển đơn tối thiểu ₫0</span>
          </div>

          {/* Voucher */}
          <div className='flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4'>
            <span className='text-sm text-gray-600 font-medium mb-2 lg:mb-0'>Shopee Voucher</span>
            <button className='text-sm text-orange-500 border border-orange-500 px-3 py-1 rounded-lg hover:bg-orange-50 transition font-medium'>
              Chọn hoặc nhập mã
            </button>
          </div>

          {/* Shopee Xu */}
          <div className='flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4'>
            <div className='flex items-center mb-2 lg:mb-0'>
              <input
                type='checkbox'
                className='h-5 w-5 text-gray-500 mr-2 rounded border-gray-300 focus:ring-orange-500'
                disabled
              />
              <span className='text-sm text-gray-600 font-medium'>Shopee Xu</span>
            </div>
            <span className='text-sm text-gray-600 font-medium'>Bạn chưa có Shopee Xu</span>
          </div>

          {/* Tổng thanh toán */}
          <div className='flex flex-col lg:flex-row justify-between items-start lg:items-center border-t pt-4'>
            <div className='flex flex-wrap items-center mb-4 lg:mb-0 gap-4'>
              <div className='flex items-center'>
                <input
                  type='checkbox'
                  checked={selectAll}
                  onChange={handleSelectAll}
                  className='h-5 w-5 text-orange-500 mr-2 rounded border-gray-300 focus:ring-orange-500'
                />
                <span className='text-sm text-gray-600 font-medium'>Chọn Tất Cả ({products.length})</span>
              </div>
              <button className='text-sm text-orange-500 hover:text-orange-600 font-medium transition'>Xóa</button>
              <span className='text-sm text-gray-600 font-medium'>Lưu vào mục Đã thích...</span>
            </div>
            <div className='text-left lg:text-right w-full lg:w-auto'>
              <p className='text-sm text-gray-600 font-medium'>
                Tổng thanh toán ({totalSelectedItems} Sản phẩm):{' '}
                <span className='text-xl text-orange-500 font-semibold'>₫{total.toLocaleString('vi-VN')}</span>
              </p>
              <p className='text-sm text-gray-600 font-medium'>Tiết kiệm: ₫{savings.toLocaleString('vi-VN')}</p>
              <button
                onClick={handlePlaceOrder}
                className='bg-orange-500 text-white px-6 py-2 rounded-lg mt-3 w-full lg:w-auto hover:bg-orange-600 transition font-medium shadow-sm'
              >
                Mua Hàng
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Checkout
