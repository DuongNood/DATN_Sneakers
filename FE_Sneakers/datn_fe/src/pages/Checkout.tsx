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

  if (loadingUser) {
    return (
      <div className='min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 flex items-center justify-center'>
        <div role='status'>
          <svg
            aria-hidden='true'
            className='w-12 h-12 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600'
            viewBox='0 0 100 101'
            fill='none'
            xmlns='http://www.w3.org/2000/svg'
          >
            <path
              d='M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z'
              fill='currentColor'
            />
            <path
              d='M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z'
              fill='currentFill'
            />
          </svg>
          <span className='sr-only'>Loading...</span>
        </div>
      </div>
    )
  }

  if (errorUser) {
    return (
      <div className='min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 flex items-center justify-center'>
        <div className='bg-white p-6 rounded-xl shadow-lg text-red-500 text-center'>{errorUser}</div>
      </div>
    )
  }

  if (products.length === 0) {
    return (
      <div className='min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 flex items-center justify-center'>
        <div className='bg-white p-6 rounded-xl shadow-lg text-gray-600 text-center'>
          {t('no_products_to_checkout')}
        </div>
      </div>
    )
  }

  return (
    <div className='min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 font-sans py-12'>
      <div className='container mx-auto px-4 sm:px-10 max-w-7xl'>
        <div className='mx-6'>
          <h1 className='text-3xl font-bold text-gray-800 mb-8 text-center'>{t('checkout')}</h1>
          <div className='flex flex-col lg:flex-row gap-8'>
            {/* Order Summary */}
            <div className='w-full lg:w-1/2 bg-white p-8 rounded-xl shadow-lg transition-all hover:shadow-xl'>
              <h2 className='text-2xl font-semibold text-gray-800 mb-6'>{t('order_summary')}</h2>
              {products.map((product) => (
                <div key={product.id} className='flex items-center mb-6 border-b border-gray-100 pb-4'>
                  <img
                    src={product.imageUrl || 'https://via.placeholder.com/80'}
                    alt={product.name}
                    className='w-20 h-20 object-cover rounded-lg mr-4 shadow-sm'
                  />
                  <div className='flex-1'>
                    <p className='text-lg font-medium text-gray-800'>{product.name}</p>
                    <p className='text-sm text-gray-500'>
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
                  className='flex-1 px-4 py-3 border border-gray-200 rounded-l-lg'
                />
                <button
                  onClick={applyCoupon}
                  className='bg-green-500 text-white px-6 py-3 rounded-r-lg hover:bg-green-600 transition-all'
                >
                  {t('apply')}
                </button>
              </div>
              <div className='space-y-3'>
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

            {/* Shipping Info */}
            <div className='w-full lg:w-1/2 bg-white p-8 rounded-xl shadow-lg transition-all hover:shadow-xl'>
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
                    } bg-gray-50`}
                    placeholder={t('enter_full_name')}
                  />
                  {errors.fullName && <p className='text-red-500 text-xs mt-1'>{errors.fullName}</p>}
                </div>
                <div className='flex gap-4'>
                  <div className='w-1/2'>
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
                      } bg-gray-50`}
                      placeholder={t('enter_email')}
                    />
                    {errors.email && <p className='text-red-500 text-xs mt-1'>{errors.email}</p>}
                  </div>
                  <div className='w-1/2'>
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
                      } bg-gray-50`}
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
                    } bg-gray-50`}
                    placeholder={t('enter_address')}
                  />
                  {errors.address && <p className='text-red-500 text-xs mt-1'>{errors.address}</p>}
                </div>
              </div>
              <button
                onClick={handlePlaceOrder}
                disabled={loadingOrder}
                className={`w-full mt-8 py-3 rounded-lg text-white font-semibold transition-all ${
                  loadingOrder ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'
                }`}
              >
                {loadingOrder ? t('processing') : t('proceed_to_payment')}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Checkout
