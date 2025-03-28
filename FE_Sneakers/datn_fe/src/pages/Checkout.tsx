import React, { useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import axios from 'axios'

interface Product {
  id: number
  name: string
  discounted_price: string
  imageUrl: string
  quantity: number
  variant: string
  product_size_id: number
}

const Checkout: React.FC = () => {
  const location = useLocation()
  const navigate = useNavigate()

  const { products, quantity } = (location.state as { products: Product[]; quantity: number }) || {
    products: [],
    quantity: 0
  }

  const [formData, setFormData] = useState({
    fullName: '',
    address: '',
    phone: '',
    discountCode: '',
    paymentMethod: 'cod'
  })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [discountAmount, setDiscountAmount] = useState(0)

  const subtotal = products.reduce((sum, product) => sum + Number(product.discounted_price) * product.quantity, 0)
  const shippingFee = 30000

  const calculateTotal = () => {
    const baseTotal = subtotal + shippingFee
    const finalTotal = baseTotal - discountAmount
    console.log('calculateTotal - baseTotal:', baseTotal, 'discountAmount:', discountAmount, 'finalTotal:', finalTotal)
    return finalTotal
  }

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const applyDiscountCode = async () => {
    if (!formData.discountCode.trim()) {
      toast.error('Vui lòng nhập mã giảm giá', { autoClose: 2000 })
      return
    }

    try {
      const response = await axios.get('http://localhost:8000/api/promotions')
      const promotions = response.data
      console.log('Promotions from API:', promotions)

      const currentDate = new Date('2025-03-28') // Ngày hiện tại
      const validPromo = promotions.find((promo: any) => {
        const startDate = new Date(promo.start_date)
        const endDate = new Date(promo.end_date)
        return (
          promo.promotion_name === formData.discountCode &&
          promo.status === 1 &&
          startDate <= currentDate &&
          currentDate <= endDate
        )
      })
      console.log('Valid Promo:', validPromo)

      if (validPromo) {
        let discount = 0
        if (validPromo.discount_type === 'Giảm số tiền') {
          // Sửa để khớp với database
          discount = Number(validPromo.discount_value) || 0
          console.log('Discount (Giảm số tiền):', discount)
        } else if (validPromo.discount_type === 'Giảm theo %') {
          // Sửa để khớp với database
          discount = (subtotal * Number(validPromo.discount_value)) / 100 || 0
          console.log('Discount (Giảm theo % before max):', discount)
          if (validPromo.max_discount_value && discount > Number(validPromo.max_discount_value)) {
            discount = Number(validPromo.max_discount_value) || 0
            console.log('Discount (Giảm theo % after max):', discount)
          }
        }

        setDiscountAmount(discount)
        console.log('Applied discountAmount:', discount, 'New total:', calculateTotal())
        toast.success(`Áp dụng mã giảm giá thành công! Giảm ${discount.toLocaleString('vi-VN')}đ`, { autoClose: 2000 })
      } else {
        setDiscountAmount(0)
        console.log('No valid promo, reset discountAmount to 0, Total:', calculateTotal())
        toast.error('Mã giảm giá không hợp lệ hoặc đã hết hạn', { autoClose: 2000 })
      }
    } catch (error) {
      console.error('Error fetching promotions:', error)
      toast.error('Lỗi khi kiểm tra mã giảm giá', { autoClose: 2000 })
      setDiscountAmount(0)
    }
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    const { fullName, address, phone } = formData
    if (!fullName.trim() || !address.trim() || !phone.trim()) {
      toast.error('Vui lòng điền đầy đủ thông tin giao hàng', { autoClose: 2000 })
      return
    }

    setIsSubmitting(true)
    try {
      const token = localStorage.getItem('token')
      if (!token) {
        toast.error('Vui lòng đăng nhập trước', { autoClose: 2000 })
        navigate('/login')
        return
      }

      const productName = products[0].name
      const orderData = {
        quantity: products[0].quantity,
        product_size_id: products[0].product_size_id,
        recipient_name: formData.fullName,
        recipient_phone: formData.phone,
        recipient_address: formData.address,
        discount_code: formData.discountCode || null,
        payment_method: formData.paymentMethod === 'cod' ? 'COD' : formData.paymentMethod,
        subtotal: subtotal,
        discount_amount: discountAmount,
        total: calculateTotal()
      }

      const response = await axios.post(
        `http://localhost:8000/api/orders/buy/${encodeURIComponent(productName)}`,
        orderData,
        { headers: { Authorization: `Bearer ${token}` } }
      )

      if (response.data.status === 'success') {
        toast.success('Đặt hàng thành công', { autoClose: 2000 })
        navigate('/order-success')
      } else {
        toast.error(response.data.message || 'Lỗi khi đặt hàng', { autoClose: 2000 })
      }
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Lỗi mạng', { autoClose: 2000 })
    } finally {
      setIsSubmitting(false)
    }
  }

  const total = calculateTotal()

  console.log(
    'Render - subtotal:',
    subtotal,
    'shippingFee:',
    shippingFee,
    'discountAmount:',
    discountAmount,
    'total:',
    total
  )

  if (!products.length) {
    return <p className='text-lg text-center text-gray-600'>Không có sản phẩm để thanh toán</p>
  }

  return (
    <div className='container mx-auto px-[18px] py-[18px] bg-gray-50 min-h-screen'>
      <h1 className='text-4xl font-extrabold text-gray-800 mb-10 text-center'>Thanh toán</h1>
      <div className='grid grid-cols-1 lg:grid-cols-5 gap-8'>
        <div className='lg:col-span-3'>
          <div className='bg-white shadow-xl rounded-xl p-6 border border-gray-100'>
            <h2 className='text-2xl font-semibold text-gray-800 mb-6 border-b pb-2'>Tóm tắt đơn hàng</h2>
            {products.map((product) => (
              <div key={product.id} className='flex items-center gap-4 py-4 border-b border-gray-200'>
                <img
                  src={product.imageUrl}
                  alt={product.name}
                  className='w-20 h-20 object-cover rounded-lg shadow-sm'
                />
                <div className='flex-1'>
                  <h3 className='text-lg font-medium text-gray-800'>{product.name}</h3>
                  <p className='text-sm text-gray-600'>
                    Kích thước: {product.variant} | Số lượng: {product.quantity}
                  </p>
                  <p className='text-lg font-semibold text-red-600 mt-1'>
                    {Number(product.discounted_price).toLocaleString('vi-VN')}đ
                  </p>
                </div>
              </div>
            ))}
            <div className='mt-6 space-y-3'>
              <div className='flex justify-between text-lg text-gray-700'>
                <span>Tạm tính</span>
                <span>{subtotal.toLocaleString('vi-VN')}đ</span>
              </div>
              <div className='flex justify-between text-lg text-gray-700'>
                <span>Phí vận chuyển</span>
                <span>{shippingFee.toLocaleString('vi-VN')}đ</span>
              </div>
              {discountAmount > 0 && (
                <div className='flex justify-between text-lg text-green-600'>
                  <span>Giảm giá</span>
                  <span>-{discountAmount.toLocaleString('vi-VN')}đ</span>
                </div>
              )}
              <div className='flex justify-between text-xl font-bold text-gray-800 border-t pt-2'>
                <span>Tổng cộng</span>
                <span>{total.toLocaleString('vi-VN')}đ</span>
              </div>
            </div>
          </div>
        </div>
        <div className='lg:col-span-2'>
          <div className='bg-white shadow-xl rounded-xl p-6 border border-gray-100'>
            <h2 className='text-2xl font-semibold text-gray-800 mb-6 border-b pb-2'>Thông tin thanh toán</h2>
            <form onSubmit={handleSubmit} className='space-y-8'>
              <div className='space-y-4'>
                <h3 className='text-lg font-medium text-gray-700'>Thông tin giao hàng</h3>
                <div className='relative'>
                  <label htmlFor='fullName' className='block text-sm font-medium text-gray-600 mb-1'>
                    Họ và tên
                  </label>
                  <input
                    id='fullName'
                    type='text'
                    name='fullName'
                    value={formData.fullName}
                    onChange={handleInputChange}
                    className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition shadow-sm hover:shadow-md'
                    placeholder='Nhập họ và tên'
                    required
                  />
                </div>
                <div className='relative'>
                  <label htmlFor='address' className='block text-sm font-medium text-gray-600 mb-1'>
                    Địa chỉ
                  </label>
                  <input
                    id='address'
                    type='text'
                    name='address'
                    value={formData.address}
                    onChange={handleInputChange}
                    className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition shadow-sm hover:shadow-md'
                    placeholder='Nhập địa chỉ'
                    required
                  />
                </div>
                <div className='relative'>
                  <label htmlFor='phone' className='block text-sm font-medium text-gray-600 mb-1'>
                    Số điện thoại
                  </label>
                  <input
                    id='phone'
                    type='text'
                    name='phone'
                    value={formData.phone}
                    onChange={handleInputChange}
                    className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition shadow-sm hover:shadow-md'
                    placeholder='Nhập số điện thoại'
                    required
                  />
                </div>
              </div>
              <div className='space-y-4'>
                <h3 className='text-lg font-medium text-gray-700'>Mã giảm giá</h3>
                <div className='relative flex gap-2'>
                  <input
                    type='text'
                    name='discountCode'
                    value={formData.discountCode}
                    onChange={handleInputChange}
                    className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition shadow-sm hover:shadow-md'
                    placeholder='Nhập mã giảm giá (nếu có)'
                  />
                  <button
                    type='button'
                    onClick={applyDiscountCode}
                    className='bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition'
                  >
                    Áp dụng
                  </button>
                </div>
              </div>
              <div className='space-y-4'>
                <h3 className='text-lg font-medium text-gray-700'>Phương thức thanh toán</h3>
                <div className='space-y-3'>
                  <label className='flex items-center gap-3 p-3 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 transition'>
                    <input
                      type='radio'
                      name='paymentMethod'
                      value='cod'
                      checked={formData.paymentMethod === 'cod'}
                      onChange={handleInputChange}
                      className='w-5 h-5 text-blue-600 focus:ring-blue-500'
                    />
                    <span className='text-sm font-medium text-gray-700'>Thanh toán khi nhận hàng (COD)</span>
                  </label>
                  <label className='flex items-center gap-3 p-3 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 transition'>
                    <input
                      type='radio'
                      name='paymentMethod'
                      value='online'
                      checked={formData.paymentMethod === 'online'}
                      onChange={handleInputChange}
                      className='w-5 h-5 text-blue-600 focus:ring-blue-500'
                    />
                    <span className='text-sm font-medium text-gray-700'>Thanh toán online (Đang phát triển)</span>
                  </label>
                </div>
              </div>
              <button
                type='submit'
                disabled={isSubmitting}
                className={`w-full bg-gradient-to-r from-blue-500 to-blue-700 text-white py-3 rounded-lg hover:from-blue-600 hover:to-blue-800 transition font-semibold shadow-md ${
                  isSubmitting ? 'opacity-50 cursor-not-allowed' : ''
                }`}
              >
                {isSubmitting ? 'Đang xử lý...' : 'Xác nhận thanh toán'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Checkout
