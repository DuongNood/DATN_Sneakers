import React, { useState, useEffect } from 'react'
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

  const { products: initialProducts, quantity: initialQuantity } = (location.state as {
    products: Product[]
    quantity: number
  }) || {
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
  const [loadingUserData, setLoadingUserData] = useState(true)
  const [cartProducts, setCartProducts] = useState<Product[]>(initialProducts)

  const fetchUserData = async () => {
    try {
      setLoadingUserData(true)
      const token = localStorage.getItem('token')
      if (!token) return

      const response = await axios.get('http://localhost:8000/api/user', {
        headers: { Authorization: `Bearer ${token}` }
      })
      const userData = response.data
      setFormData((prev) => ({
        ...prev,
        fullName: userData.name || '',
        address: userData.address || '',
        phone: userData.phone || ''
      }))
    } catch (error) {
      console.error('Error fetching user data:', error)
      toast.error('Không thể lấy thông tin người dùng', { autoClose: 2000 })
    } finally {
      setLoadingUserData(false)
    }
  }

  const fetchCart = async () => {
    try {
      const token = localStorage.getItem('token')
      if (!token) return

      const response = await axios.get('http://localhost:8000/api/carts', {
        headers: { Authorization: `Bearer ${token}` }
      })
      const cartItems = response.data.data.map((item: any) => ({
        id: item.id,
        name: item.name,
        discounted_price: item.discount.toString(),
        imageUrl: item.image,
        quantity: item.quantity,
        variant: item.size,
        product_size_id: item.product_size_id
      }))
      setCartProducts(cartItems)
    } catch (error) {
      console.error('Error fetching cart:', error)
      toast.error('Không thể lấy giỏ hàng', { autoClose: 2000 })
    }
  }

  useEffect(() => {
    fetchUserData()
    if (!initialProducts.length) fetchCart()
  }, [initialProducts])

  const subtotal = cartProducts.reduce((sum, product) => sum + Number(product.discounted_price) * product.quantity, 0)
  const shippingFee = 30000

  const calculateTotal = () => {
    const baseTotal = subtotal + shippingFee
    const finalTotal = baseTotal - discountAmount
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
      const currentDate = new Date('2025-03-28')
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

      if (validPromo) {
        let discount = 0
        if (validPromo.discount_type === 'Giảm số tiền') {
          discount = Number(validPromo.discount_value) || 0
        } else if (validPromo.discount_type === 'Giảm theo %') {
          discount = (subtotal * Number(validPromo.discount_value)) / 100 || 0
          if (validPromo.max_discount_value && discount > Number(validPromo.max_discount_value)) {
            discount = Number(validPromo.max_discount_value) || 0
          }
        }
        setDiscountAmount(discount)
        toast.success(`Áp dụng mã giảm giá thành công! Giảm ${discount.toLocaleString('vi-VN')}đ`, { autoClose: 2000 })
      } else {
        setDiscountAmount(0)
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

      const productName = cartProducts[0].name
      const orderData = {
        quantity: cartProducts[0].quantity,
        product_size_id: cartProducts[0].product_size_id,
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
        navigate('/order-success', { state: { orderSuccess: true } })
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

  if (!cartProducts.length) {
    return (
      <div className='min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200'>
        <p className='text-xl font-medium text-gray-600'>Không có sản phẩm để thanh toán</p>
      </div>
    )
  }

  return (
    <div className='min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 py-5 px-5 md:py-20 md:px-20'>
      <div className='max-w-7xl mx-auto'>
        <h1 className='text-3xl md:text-4xl font-bold text-gray-800 mb-8 text-center tracking-tight'>
          Thanh Toán Đơn Hàng
        </h1>
        <div className='grid grid-cols-1 lg:grid-cols-5 gap-6'>
          <div className='lg:col-span-3'>
            <div className='bg-white rounded-2xl shadow-lg p-6'>
              <h2 className='text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-gray-200'>
                Tóm tắt đơn hàng
              </h2>
              {cartProducts.map((product) => (
                <div key={product.id} className='flex items-center gap-4 py-4 border-b border-gray-100 last:border-b-0'>
                  <img
                    src={product.imageUrl}
                    alt={product.name}
                    className='w-16 h-16 md:w-20 md:h-20 object-cover rounded-lg shadow-sm'
                  />
                  <div className='flex-1'>
                    <h3 className='text-lg font-medium text-gray-800'>{product.name}</h3>
                    <p className='text-sm text-gray-500'>
                      Kích thước: {product.variant} | Số lượng: {product.quantity}
                    </p>
                    <p className='text-lg font-semibold text-indigo-600 mt-1'>
                      {Number(product.discounted_price).toLocaleString('vi-VN')}đ
                    </p>
                  </div>
                </div>
              ))}
              <div className='mt-6 space-y-4'>
                <div className='flex justify-between text-base text-gray-600'>
                  <span>Tạm tính</span>
                  <span>{subtotal.toLocaleString('vi-VN')}đ</span>
                </div>
                <div className='flex justify-between text-base text-gray-600'>
                  <span>Phí vận chuyển</span>
                  <span>{shippingFee.toLocaleString('vi-VN')}đ</span>
                </div>
                {discountAmount > 0 && (
                  <div className='flex justify-between text-base text-green-600'>
                    <span>Giảm giá</span>
                    <span>-{discountAmount.toLocaleString('vi-VN')}đ</span>
                  </div>
                )}
                <div className='flex justify-between text-xl font-bold text-gray-800 border-t pt-4'>
                  <span>Tổng cộng</span>
                  <span>{total.toLocaleString('vi-VN')}đ</span>
                </div>
              </div>
            </div>
          </div>
          <div className='lg:col-span-2'>
            <div className='bg-white rounded-2xl shadow-lg p-6'>
              <h2 className='text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-gray-200'>
                Thông tin thanh toán
              </h2>
              {loadingUserData ? (
                <div className='space-y-4 animate-pulse'>
                  <div className='h-4 bg-gray-300 rounded w-1/4'></div>
                  <div className='h-10 bg-gray-300 rounded'></div>
                  <div className='h-10 bg-gray-300 rounded'></div>
                  <div className='h-10 bg-gray-300 rounded'></div>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className='space-y-8'>
                  <div className='space-y-4'>
                    <h3 className='text-lg font-medium text-gray-700'>Thông tin giao hàng</h3>
                    <div>
                      <label htmlFor='fullName' className='block text-sm font-medium text-gray-600 mb-1'>
                        Họ và tên
                      </label>
                      <input
                        id='fullName'
                        type='text'
                        name='fullName'
                        value={formData.fullName}
                        onChange={handleInputChange}
                        className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'
                        placeholder='Nhập họ và tên'
                        required
                      />
                    </div>
                    <div>
                      <label htmlFor='address' className='block text-sm font-medium text-gray-600 mb-1'>
                        Địa chỉ
                      </label>
                      <input
                        id='address'
                        type='text'
                        name='address'
                        value={formData.address}
                        onChange={handleInputChange}
                        className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'
                        placeholder='Nhập địa chỉ'
                        required
                      />
                    </div>
                    <div>
                      <label htmlFor='phone' className='block text-sm font-medium text-gray-600 mb-1'>
                        Số điện thoại
                      </label>
                      <input
                        id='phone'
                        type='text'
                        name='phone'
                        value={formData.phone}
                        onChange={handleInputChange}
                        className='w-full border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'
                        placeholder='Nhập số điện thoại'
                        required
                      />
                    </div>
                  </div>
                  <div className='space-y-4'>
                    <h3 className='text-lg font-medium text-gray-700'>Mã giảm giá</h3>
                    <div className='flex gap-2'>
                      <input
                        type='text'
                        name='discountCode'
                        value={formData.discountCode}
                        onChange={handleInputChange}
                        className='w-2/3 border border-gray-300 rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition'
                        placeholder='Nhập mã giảm giá'
                      />
                      <button
                        type='button'
                        onClick={applyDiscountCode}
                        className='w-1/3 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition font-medium'
                      >
                        Áp dụng
                      </button>
                    </div>
                  </div>
                  <div className='space-y-4'>
                    <h3 className='text-lg font-medium text-gray-700'>Phương thức thanh toán</h3>
                    <div className='space-y-3'>
                      <label className='flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition'>
                        <input
                          type='radio'
                          name='paymentMethod'
                          value='cod'
                          checked={formData.paymentMethod === 'cod'}
                          onChange={handleInputChange}
                          className='w-5 h-5 text-indigo-600 focus:ring-indigo-500'
                        />
                        <span className='text-sm font-medium text-gray-700'>Thanh toán khi nhận hàng (COD)</span>
                      </label>
                      <label className='flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition'>
                        <input
                          type='radio'
                          name='paymentMethod'
                          value='online'
                          checked={formData.paymentMethod === 'online'}
                          onChange={handleInputChange}
                          className='w-5 h-5 text-indigo-600 focus:ring-indigo-500'
                        />
                        <span className='text-sm font-medium text-gray-700'>Thanh toán online (Đang phát triển)</span>
                      </label>
                    </div>
                  </div>
                  <button
                    type='submit'
                    disabled={isSubmitting}
                    className={`w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition font-semibold shadow-md ${
                      isSubmitting ? 'opacity-50 cursor-not-allowed' : ''
                    }`}
                  >
                    {isSubmitting ? 'Đang xử lý...' : 'Xác nhận thanh toán'}
                  </button>
                </form>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Checkout
