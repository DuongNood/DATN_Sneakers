import React, { useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'
import axios from 'axios'

interface Product {
  id: number
  name: string
  discounted_price: string
  imageUrl: string | null
  quantity: number
  variant?: string
  size?: string
  sizes?: { size: string; quantity: number; product_size_id: number }[]
}

interface ShippingInfo {
  fullName: string
  email: string
  phone: string
  address: string
}

interface PaymentState {
  products: Product[]
  shippingInfo: ShippingInfo
  total: number
  couponDiscount: number
  shippingFee: number
}

const Payment: React.FC = () => {
  const { t } = useTranslation()
  const location = useLocation()
  const navigate = useNavigate()
  const { products, shippingInfo, total, couponDiscount, shippingFee } = (location.state as PaymentState) || {}

  const [paymentMethod, setPaymentMethod] = useState<'cod' | 'momo' | null>(null)
  const [loading, setLoading] = useState<boolean>(false)

  console.log('Payment Data from Checkout:', {
    products,
    shippingInfo,
    total,
    couponDiscount,
    shippingFee
  })

  const handlePayment = async () => {
    if (!paymentMethod) {
      toast.error(t('please_select_payment_method'), { autoClose: 2000 })
      return
    }

    setLoading(true)
    try {
      const token = localStorage.getItem('token')
      if (!token) throw new Error(t('no_token'))

      // Tạo đơn hàng
      const orderPromises = products.map(async (product) => {
        const selectedSizeObj = product.sizes?.find((s) => s.size === (product.variant || product.size))
        if (!selectedSizeObj)
          throw new Error(`Size ${product.variant || product.size} not found for product ${product.name}`)

        const response = await axios.post(
          `http://localhost:8000/api/orders/buy/${encodeURIComponent(product.name)}`,
          {
            shipping_info: shippingInfo,
            quantity: product.quantity,
            product_size_id: selectedSizeObj.product_size_id,
            payment_method: paymentMethod,
            status: paymentMethod === 'cod' ? 'confirmed' : 'pending'
          },
          {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          }
        )
        return response.data
      })

      await Promise.all(orderPromises)

      if (paymentMethod === 'cod') {
        navigate('/order-success')
        toast.success(t('order_confirmed_successfully'), { autoClose: 2000 })
      } else if (paymentMethod === 'momo') {
        // Gọi API MoMo để lấy payUrl
        const momoResponse = await axios.post(
          'http://localhost:8000/api/momo/create',
          { amount: total },
          {
            headers: {
              'Content-Type': 'application/json'
            }
          }
        )

        const { payUrl } = momoResponse.data
        if (payUrl) {
          window.location.href = payUrl
        } else {
          throw new Error('Lỗi, vui lòng nạp thêm tiền cho anh Hoàng Anh nhé')
        }
      }
    } catch (error: any) {
      console.error('Payment error:', error)
      toast.error(error.response?.data?.message || t('payment_failed'), {
        autoClose: 2000
      })
    } finally {
      setLoading(false)
    }
  }

  if (!products || products.length === 0 || !shippingInfo || !total) {
    return (
      <div className='min-h-screen bg-gradient-to-br from-indigo-50 to-gray-100 flex items-center justify-center'>
        <div className='bg-white p-8 rounded-2xl shadow-xl text-red-600 text-center text-lg font-semibold'>
          {t('invalid_order_data')}
        </div>
      </div>
    )
  }

  return (
    <div className='min-h-screen bg-gradient-to-br from-indigo-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8 font-sans'>
      <div className='max-w-7xl mx-auto'>
        <h1 className='text-4xl font-bold text-gray-900 mb-12 text-center tracking-tight'>{t('payment')}</h1>
        <div className='grid grid-cols-1 lg:grid-cols-2 gap-8'>
          {/* Tóm tắt đơn hàng và địa chỉ nhận hàng */}
          <div className='bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl'>
            <h2 className='text-2xl font-semibold text-gray-900 mb-6'>{t('order_summary')}</h2>
            {products.map((product) => (
              <div
                key={product.id}
                className='flex items-center mb-6 pb-4 border-b border-gray-100 transition-all duration-200 hover:bg-gray-50 rounded-lg'
              >
                <img
                  src={product.imageUrl || 'https://via.placeholder.com/80'}
                  alt={product.name}
                  className='w-20 h-20 object-cover rounded-lg mr-4 shadow-sm'
                />
                <div className='flex-1'>
                  <p className='text-lg font-medium text-gray-900'>{product.name}</p>
                  <p className='text-sm text-gray-500'>
                    {t('size')}: {product.variant || product.size || 'N/A'}
                  </p>
                </div>
                <div className='text-right'>
                  <p className='text-lg font-medium text-gray-900'>
                    {product.quantity} x ₫{Number(product.discounted_price).toLocaleString('vi-VN')}
                  </p>
                  <p className='text-sm text-gray-500'>
                    {t('subtotal')}: ₫{(Number(product.discounted_price) * product.quantity).toLocaleString('vi-VN')}
                  </p>
                </div>
              </div>
            ))}
            <div className='space-y-4'>
              <div className='flex justify-between text-base text-gray-700'>
                <span>{t('subtotal')}</span>
                <span>₫{(total - shippingFee + couponDiscount).toLocaleString('vi-VN')}</span>
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
              <div className='flex justify-between text-xl font-semibold text-gray-900 pt-4 border-t border-gray-200'>
                <span>{t('total')}</span>
                <span>₫{total.toLocaleString('vi-VN')}</span>
              </div>
            </div>

            {/* Địa chỉ nhận hàng */}
            <div className='mt-8 pt-6 border-t border-gray-200'>
              <h2 className='text-2xl font-semibold text-gray-900 mb-4'>{t('shipping_address')}</h2>
              <div className='bg-indigo-50 p-5 rounded-xl transition-all duration-200 hover:bg-indigo-100'>
                <p className='text-base font-medium text-gray-900'> Họ và Tên: {shippingInfo.fullName}</p>
                <p className='text-sm text-gray-600 mt-1'> Địa chỉ: {shippingInfo.address}</p>
                <p className='text-sm text-gray-600 mt-1'>
                  {t('phone')}: {shippingInfo.phone}
                </p>
                <p className='text-sm text-gray-600 mt-1'>
                  {t('email')}: {shippingInfo.email}
                </p>
              </div>
            </div>
          </div>

          {/* Phương thức thanh toán */}
          <div className='bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl'>
            <h2 className='text-2xl font-semibold text-gray-900 mb-6'>{t('payment_method')}</h2>
            <div className='space-y-4'>
              {/* Thanh toán COD */}
              <div
                className={`p-5 border rounded-xl cursor-pointer transition-all duration-200 flex items-center ${
                  paymentMethod === 'cod'
                    ? 'border-indigo-500 bg-indigo-50 shadow-md'
                    : 'border-gray-200 hover:bg-gray-50'
                }`}
                onClick={() => setPaymentMethod('cod')}
              >
                <input
                  type='radio'
                  name='paymentMethod'
                  checked={paymentMethod === 'cod'}
                  onChange={() => setPaymentMethod('cod')}
                  className='mr-3 h-5 w-5 text-indigo-600 focus:ring-indigo-400'
                />
                <div className='flex-1'>
                  <p className='font-medium text-gray-900'>{t('cash_on_delivery')}</p>
                  <p className='text-sm text-gray-500'>{t('cod_description')}</p>
                </div>
                <svg
                  className='w-6 h-6 text-gray-400'
                  fill='none'
                  stroke='currentColor'
                  viewBox='0 0 24 24'
                  xmlns='http://www.w3.org/2000/svg'
                >
                  <path
                    strokeLinecap='round'
                    strokeLinejoin='round'
                    strokeWidth='2'
                    d='M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                  />
                </svg>
              </div>

              {/* Thanh toán MoMo */}
              <div
                className={`p-5 border rounded-xl cursor-pointer transition-all duration-200 flex items-center ${
                  paymentMethod === 'momo'
                    ? 'border-indigo-500 bg-indigo-50 shadow-md'
                    : 'border-gray-200 hover:bg-gray-50'
                }`}
                onClick={() => setPaymentMethod('momo')}
              >
                <input
                  type='radio'
                  name='paymentMethod'
                  checked={paymentMethod === 'momo'}
                  onChange={() => setPaymentMethod('momo')}
                  className='mr-3 h-5 w-5 text-indigo-600 focus:ring-indigo-400'
                />
                <div className='flex-1'>
                  <p className='font-medium text-gray-900'>{t('momo_payment')}</p>
                  <p className='text-sm text-gray-500'>{t('momo_payment_description')}</p>
                </div>
                <svg
                  className='w-6 h-6 text-gray-400'
                  fill='none'
                  stroke='currentColor'
                  viewBox='0 0 24 24'
                  xmlns='http://www.w3.org/2000/svg'
                >
                  <path
                    strokeLinecap='round'
                    strokeLinejoin='round'
                    strokeWidth='2'
                    d='M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
                  />
                </svg>
              </div>
            </div>

            <button
              onClick={handlePayment}
              disabled={loading}
              className={`w-full mt-8 py-3 rounded-lg text-white font-semibold transition-all flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 ${
                loading ? 'opacity-70 cursor-not-allowed' : ''
              }`}
            >
              {loading ? (
                <>
                  <div className='w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2'></div>
                  {t('processing')}
                </>
              ) : (
                t('confirm_payment')
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Payment
