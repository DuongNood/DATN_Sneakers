import React, { useState, useEffect } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'
import axios from 'axios'

// Định nghĩa các interface
interface Product {
  id: number
  slug?: string
  name: string
  original_price: string
  discounted_price: string
  product_code: string
  imageUrl: string | null
  rating: number
  description: string
  quantity: number
  variant?: string
  size?: string
  images?: string[]
  sizes?: { size: string; quantity: number; product_size_id: number }[]
  category?: { id: number; category_name: string }
}

interface CheckoutState {
  products: Product[]
}

interface User {
  name: string
  email: string
  phone: string
  address: string
}

interface Coupon {
  code: string
  type: string
  value: number
  max_discount_value?: number
}

const Checkout: React.FC = () => {
  const { t } = useTranslation()
  const location = useLocation()
  const navigate = useNavigate()
  const { products: initialProducts } = (location.state as CheckoutState) || { products: [] }

  const [products, setProducts] = useState<Product[]>(initialProducts)
  const [fullName, setFullName] = useState<string>('')
  const [email, setEmail] = useState<string>('')
  const [phone, setPhone] = useState<string>('')
  const [address, setAddress] = useState<string>('')
  const [loadingUser, setLoadingUser] = useState<boolean>(true)
  const [errorUser, setErrorUser] = useState<string | null>(null)
  const [loadingOrder, setLoadingOrder] = useState<boolean>(false)
  const [errors, setErrors] = useState<{
    fullName?: string
    email?: string
    phone?: string
    address?: string
  }>({})
  const [couponCode, setCouponCode] = useState<string>('')
  const [couponDiscount, setCouponDiscount] = useState<number>(0)
  const [appliedCoupon, setAppliedCoupon] = useState<Coupon | null>(null)

  useEffect(() => {
    console.log('Initial Products in Checkout:', initialProducts)
    setProducts(initialProducts)
  }, [initialProducts])

  const fetchUserData = async () => {
    try {
      setLoadingUser(true)
      setErrorUser(null)

      const token = localStorage.getItem('token')
      if (!token) throw new Error(t('no_token'))

      const response = await axios.get('http://localhost:8000/api/user', {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      })

      const user: User = response.data
      setFullName(user.name || '')
      setEmail(user.email || '')
      setPhone(user.phone || '')
      setAddress(user.address || '')
    } catch (error: any) {
      setErrorUser(error.message || t('fetch_user_error'))
      toast.error(error.message || t('fetch_user_error'), { autoClose: 2000 })
    } finally {
      setLoadingUser(false)
    }
  }

  useEffect(() => {
    fetchUserData()
  }, [t])

  const validateForm = (): boolean => {
    const newErrors: { fullName?: string; email?: string; phone?: string; address?: string } = {}

    if (!fullName.trim()) newErrors.fullName = t('full_name_required')
    else if (fullName.trim().length < 2) newErrors.fullName = t('full_name_min_length')

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!email.trim()) newErrors.email = t('email_required')
    else if (!emailRegex.test(email)) newErrors.email = t('email_invalid')

    const phoneRegex = /^[0-9]{10,11}$/
    if (!phone.trim()) newErrors.phone = t('phone_required')
    else if (!phoneRegex.test(phone)) newErrors.phone = t('phone_invalid')

    if (!address.trim()) newErrors.address = t('address_required')
    else if (address.trim().length < 5) newErrors.address = t('address_min_length')

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const calculateSubtotal = (): number => {
    return products.reduce((total, product) => {
      const price = Number(product.discounted_price) || 0
      return total + price * product.quantity
    }, 0)
  }

  const shippingFee: number = 30000
  const total: number = Math.max(calculateSubtotal() + shippingFee - couponDiscount, 0)

  const applyCoupon = async () => {
    if (!couponCode) {
      toast.error(t('please_enter_coupon'), { autoClose: 2000 })
      return
    }

    try {
      const response = await axios.post('http://localhost:8000/api/promotions', { code: couponCode })
      if (response.data.status === 'success') {
        const coupon: Coupon = response.data.data
        setAppliedCoupon(coupon)

        let discount = 0
        if (coupon.type === 'percentage') {
          discount = calculateSubtotal() * (coupon.value / 100)
          if (coupon.max_discount_value && discount > coupon.max_discount_value) discount = coupon.max_discount_value
        } else if (coupon.type === 'fixed') {
          discount = coupon.value
          if (coupon.max_discount_value && discount > coupon.max_discount_value) discount = coupon.max_discount_value
        }

        setCouponDiscount(discount)
        toast.success(t('coupon_applied', { discount: discount.toLocaleString('vi-VN') }), {
          autoClose: 2000
        })
      } else {
        throw new Error(response.data.message || t('invalid_coupon'))
      }
    } catch (error: any) {
      console.error('Error applying coupon:', error)
      setCouponDiscount(0)
      setAppliedCoupon(null)
      toast.error(error.response?.data?.message || error.message || t('invalid_coupon'), {
        autoClose: 2000
      })
    }
  }

  const handlePlaceOrder = async () => {
    if (!validateForm()) {
      toast.error(t('please_fill_all_fields_correctly'), { autoClose: 2000 })
      return
    }

    setLoadingOrder(true)
    try {
      const token = localStorage.getItem('token')
      if (!token) throw new Error(t('no_token'))

      console.log('Data sent to Payment:', {
        products,
        shippingInfo: { fullName, email, phone, address },
        total,
        couponDiscount,
        shippingFee
      })

      navigate('/payment', {
        state: {
          products,
          shippingInfo: { fullName, email, phone, address },
          total,
          couponDiscount,
          shippingFee
        }
      })
    } catch (error: any) {
      console.error('Error in handlePlaceOrder:', error)
      toast.error(error.message || t('order_prepare_failed'), { autoClose: 2000 })
    } finally {
      setLoadingOrder(false)
    }
  }

  // Giao diện loading
  if (loadingUser) {
    return (
      <div className='min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center'>
        <div className='flex flex-col items-center'>
          <div className='w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin'></div>
        </div>
      </div>
    )
  }

  // Giao diện lỗi
  if (errorUser) {
    return (
      <div className='min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center'>
        <div className='bg-white p-8 rounded-2xl shadow-xl text-red-600 text-center text-lg font-semibold'>
          {errorUser}
        </div>
      </div>
    )
  }

  // Giao diện khi không có sản phẩm
  if (products.length === 0) {
    return (
      <div className='flex items-center justify-center min-h-[300px]'>
        <div className='w-[400px] h-[100px] p-8 rounded-2xl text-red-600 text-center text-lg font-semibold flex items-center justify-center'>
          {t('no_products_to_checkout')}
        </div>
      </div>
    )
  }

  // Giao diện chính
  return (
    <div className='min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8'>
      <div className='max-w-7xl mx-auto'>
        <h1 className='text-3xl font-bold text-gray-800 mb-10 text-center'>{t('checkout')}</h1>
        <div className='grid grid-cols-1 lg:grid-cols-2 gap-8'>
          {/* Tóm tắt đơn hàng */}
          <div className='bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300'>
            <h2 className='text-2xl font-semibold text-gray-800 mb-6'>{t('order_summary')}</h2>
            {products.map((product) => (
              <div key={product.id} className='flex items-center mb-6 pb-4 border-b border-gray-200'>
                <img
                  src={product.imageUrl || 'https://via.placeholder.com/80'}
                  alt={product.name}
                  className='w-20 h-20 object-cover rounded-lg mr-4 shadow-sm'
                />
                <div className='flex-1'>
                  <p className='text-lg font-medium text-gray-800'>{product.name}</p>
                  <p className='text-sm text-gray-600'>
                    {t('size')}: {product.variant || product.size || 'N/A'}
                  </p>
                </div>
                <div className='text-right'>
                  <p className='text-lg font-medium text-gray-800'>
                    {product.quantity} x ₫{Number(product.discounted_price || 0).toLocaleString('vi-VN')}
                  </p>
                  <p className='text-sm text-gray-600'>
                    {t('subtotal')}: ₫
                    {(Number(product.discounted_price || 0) * product.quantity).toLocaleString('vi-VN')}
                  </p>
                </div>
              </div>
            ))}
            <div className='flex items-center mb-6'>
              <input
                type='text'
                value={couponCode}
                onChange={(e) => setCouponCode(e.target.value)}
                placeholder={t('enter_coupon_code')}
                className='flex-1 px-4 py-3 border border-gray-200 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition'
              />
              <button
                onClick={applyCoupon}
                className='bg-green-600 text-white px-6 py-3 rounded-r-lg hover:bg-green-700 transition'
              >
                {t('apply')}
              </button>
            </div>
            <div className='space-y-4'>
              <div className='flex justify-between text-base text-gray-700'>
                <span>{t('subtotal')}</span>
                <span>₫{calculateSubtotal().toLocaleString('vi-VN')}</span>
              </div>
              <div className='flex justify-between text-base text-gray-700'>
                <span>{t('shipping_fee')}</span>
                <span>₫{shippingFee.toLocaleString('vi-VN')}</span>
              </div>
              {couponDiscount > 0 && (
                <div className='flex justify-between text-base text-green-600'>
                  <span>{t('discount')}</span>
                  <span>-₫{couponDiscount.toLocaleString('vi-VN')}</span>
                </div>
              )}
              <div className='flex justify-between text-xl font-semibold text-gray-800 pt-4 border-t border-gray-200'>
                <span>{t('total')}</span>
                <span>₫{total.toLocaleString('vi-VN')}</span>
              </div>
            </div>
          </div>

          {/* Thông tin giao hàng */}
          <div className='bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300'>
            <h2 className='text-2xl font-semibold text-gray-800 mb-6'>{t('shipping_info')}</h2>
            <div className='space-y-6'>
              <div>
                <label className='block text-sm font-medium text-gray-700 mb-2'>{t('full_name')}</label>
                <input
                  type='text'
                  value={fullName}
                  onChange={(e) => {
                    setFullName(e.target.value)
                    setErrors((prev) => ({ ...prev, fullName: undefined }))
                  }}
                  className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition ${
                    errors.fullName ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 focus:ring-blue-400'
                  }`}
                  placeholder={t('enter_full_name')}
                />
                {errors.fullName && <p className='text-red-500 text-xs mt-1'>{errors.fullName}</p>}
              </div>
              <div className='grid grid-cols-1 sm:grid-cols-2 gap-4'>
                <div>
                  <label className='block text-sm font-medium text-gray-700 mb-2'>{t('email')}</label>
                  <input
                    type='email'
                    value={email}
                    onChange={(e) => {
                      setEmail(e.target.value)
                      setErrors((prev) => ({ ...prev, email: undefined }))
                    }}
                    className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition ${
                      errors.email ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 focus:ring-blue-400'
                    }`}
                    placeholder={t('enter_email')}
                  />
                  {errors.email && <p className='text-red-500 text-xs mt-1'>{errors.email}</p>}
                </div>
                <div>
                  <label className='block text-sm font-medium text-gray-700 mb-2'>{t('phone')}</label>
                  <input
                    type='text'
                    value={phone}
                    onChange={(e) => {
                      setPhone(e.target.value)
                      setErrors((prev) => ({ ...prev, phone: undefined }))
                    }}
                    className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition ${
                      errors.phone ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 focus:ring-blue-400'
                    }`}
                    placeholder={t('enter_phone')}
                  />
                  {errors.phone && <p className='text-red-500 text-xs mt-1'>{errors.phone}</p>}
                </div>
              </div>
              <div>
                <label className='block text-sm font-medium text-gray-700 mb-2'>{t('address')}</label>
                <input
                  type='text'
                  value={address}
                  onChange={(e) => {
                    setAddress(e.target.value)
                    setErrors((prev) => ({ ...prev, address: undefined }))
                  }}
                  className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition ${
                    errors.address ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 focus:ring-blue-400'
                  }`}
                  placeholder={t('enter_address')}
                />
                {errors.address && <p className='text-red-500 text-xs mt-1'>{errors.address}</p>}
              </div>
            </div>
            <button
              onClick={handlePlaceOrder}
              disabled={loadingOrder}
              className={`w-full mt-8 py-3 rounded-lg text-white font-semibold transition-all ${
                loadingOrder ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'
              }`}
            >
              {loadingOrder ? t('processing') : t('proceed_to_payment')}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Checkout
