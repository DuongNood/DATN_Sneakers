import { useState } from 'react'
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
  products: Product[] // Thay đổi thành mảng
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

  const handlePayment = async () => {
    const amount = Math.floor(total + shippingFee - couponDiscount); // Đảm bảo amount là số nguyên

    const orderId = `ORDER_${Date.now()}`;

    const paymentData = {
      order_id: orderId,
      amount: amount,
      order_info: `Thanh toán đơn hàng ${orderId}`,
      redirect_url: "http://localhost:3000/momo-success", // Đảm bảo điều này chính xác
      ipn_url: "http://localhost:8000/api/momo/callback", // Đảm bảo điều này chính xác
      lang: "vi",
      products,
      shippingInfo,
      couponDiscount,
      shippingFee,
    };

    if (!paymentMethod) {
      toast.error(t('please_select_payment_method'), { autoClose: 2000 })
      return
    }

    setLoading(true)
    try {
      const token = localStorage.getItem('token')
      if (!token) throw new Error(t('no_token'))

      if (paymentMethod === 'cod') {
        // Xử lý thanh toán COD
        const orderPromises = products.map(async (product) => {
          const selectedSizeObj = product.sizes?.find((s) => s.size === (product.variant || product.size))
          if (!selectedSizeObj)
            throw new Error(`Kích thước ${product.variant || product.size} không tìm thấy cho sản phẩm ${product.name}`)

          return axios.post(
            `http://localhost:8000/api/orders/buy/${encodeURIComponent(product.name)}`,
            paymentData,
            {
              headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json'
              }
            }
          )
        })

        await Promise.all(orderPromises)
        navigate('/order-success')
        toast.success(t('order_confirmed_successfully'), { autoClose: 2000 })
      } else if (paymentMethod === 'momo') {
        // Xử lý thanh toán MoMo
        const orderPromises = products.map(async (product) => {
          const selectedSizeObj = product.sizes?.find((s) => s.size === (product.variant || product.size))
          if (!selectedSizeObj) {
            throw new Error(`Kích thước ${product.variant || product.size} không tìm thấy cho sản phẩm ${product.name}`)
          }

          return axios.post(
            `http://localhost:8000/api/orders/buy/${encodeURIComponent(product.name)}`,
            paymentData,
            {
              headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json'
              }
            }
          )
        })

        const orderResults = await Promise.all(orderPromises)

        const requestId = `REQ_${Date.now()}`

        const pendingOrder = {
          orderId,
          orderResults: orderResults.map(result => result.data),
          products,
          shippingInfo,
          total,
          couponDiscount,
          shippingFee
        }
        localStorage.setItem('pendingOrder', JSON.stringify(pendingOrder))

        const response = await axios.post(
          'http://localhost:8000/api/momo/payment',
          {
            orderId,
            orderResults: orderResults.map(result => result.data),
            products,
            shippingInfo,
            total,
            couponDiscount,
            shippingFee,
            amount, // Sử dụng amount đã tính toán
            orderInfo: `Thanh toán đơn hàng ${orderId}`,
            requestId,
            redirectUrl: `${window.location.origin}/momo-callback`,
            ipnUrl: 'http://localhost:8000/api/momo/callback',
            extraData: btoa(unescape(encodeURIComponent(JSON.stringify({
              orderCode: orderId,
              orderIds: orderResults.map(result => result.data.order_id)
            })))),
            lang: 'vi',
            requestType: 'captureWallet'
          },
          {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'application/json'
            }
          }
        )

        if (response.data && response.data.payUrl) {
          localStorage.setItem('currentMomoPayment', JSON.stringify({
            orderId,
            amount,
            orderIds: orderResults.map(result => result.data.order_id)
          }))
          window.location.href = response.data.payUrl
        } else {
          throw new Error(response.data?.message || t('payment_failed'))
        }
      }
    } catch (error: any) {
      console.error('Lỗi thanh toán:', error)
      toast.error(error.response?.data?.message || t('payment_failed'), { autoClose: 2000 })
    } finally {
      setLoading(false)
    }
  }

  if (!products || products.length === 0 || !shippingInfo || !total) {
    return (
      <div className='min-h-screen bg-gray-100 font-sans flex items-center justify-center'>
        <p className='text-red-500'>{t('invalid_order_data')}</p>
      </div>
    )
  }

  return (
    <div className='min-h-screen bg-gray-100 font-sans'>
      <div className='container mx-auto px-4 py-6 lg:py-10 lg:px-8'>
        <div className='flex flex-col lg:flex-row gap-6'>
          {/* Tóm tắt đơn hàng */}
          <div className='w-full lg:w-1/2 bg-white p-6 rounded-lg shadow-sm'>
            <h2 className='text-lg font-semibold text-gray-800 mb-4'>{t('order_summary')}</h2>
            {products.map((product) => (
              <div key={product.id} className='flex items-center mb-4'>
                <img
                  src={product.imageUrl || 'https://via.placeholder.com/80'}
                  alt={product.name}
                  className='w-16 h-16 object-cover rounded-md mr-4'
                />
                <div className='flex-1'>
                  <p className='text-sm font-medium text-gray-800'>{product.name}</p>
                  <p className='text-xs text-gray-500'>
                    {t('size')}: {product.variant || product.size || 'N/A'}
                  </p>
                </div>
                <div className='text-right'>
                  <p className='text-sm font-medium text-gray-800'>
                    {product.quantity} x ₫{Number(product.discounted_price).toLocaleString('vi-VN')}
                  </p>
                  <p className='text-xs text-gray-600'>
                    {t('subtotal')}: ₫{(Number(product.discounted_price) * product.quantity).toLocaleString('vi-VN')}
                  </p>
                </div>
              </div>
            ))}
            <hr className='my-4' />
            <div className='space-y-2'>
              <div className='flex justify-between text-sm text-gray-700'>
                <span>{t('subtotal')}</span>
                <span>₫{(total - shippingFee + couponDiscount).toLocaleString('vi-VN')}</span>
              </div>
              <div className='flex justify-between text-sm text-gray-700'>
                <span>{t('shipping_fee')}</span>
                <span>₫{shippingFee.toLocaleString('vi-VN')}</span>
              </div>
              {couponDiscount > 0 && (
                <div className='flex justify-between text-sm text-green-600'>
                  <span>{t('discount')}</span>
                  <span>-₫{couponDiscount.toLocaleString('vi-VN')}</span>
                </div>
              )}
              <div className='flex justify-between text-lg font-semibold text-gray-800'>
                <span>{t('total')}</span>
                <span>₫{total.toLocaleString('vi-VN')}</span>
              </div>
            </div>
          </div>

          {/* Phương thức thanh toán */}
          <div className='w-full lg:w-1/2 bg-white p-6 rounded-lg shadow-sm'>
            <h2 className='text-lg font-semibold text-gray-800 mb-4'>{t('payment_method')}</h2>
            <div className='space-y-4'>
              {/* Tùy chọn COD */}
              <div
                className={`p-4 border rounded-md cursor-pointer transition-colors ${paymentMethod === 'cod' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                  }`}
                onClick={() => setPaymentMethod('cod')}
              >
                <div className='flex items-center'>
                  <input
                    type='radio'
                    name='paymentMethod'
                    checked={paymentMethod === 'cod'}
                    onChange={() => setPaymentMethod('cod')}
                    className='mr-2'
                  />
                  <div>
                    <p className='font-medium text-gray-800'>{t('cash_on_delivery')}</p>
                    <p className='text-sm text-gray-600'>{t('cod_description')}</p>
                  </div>
                </div>
              </div>

              {/* Tùy chọn MoMo */}
              <div
                className={`p-4 border rounded-md cursor-pointer transition-colors ${paymentMethod === 'momo' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'
                  }`}
                onClick={() => setPaymentMethod('momo')}
              >
                <div className='flex items-center'>
                  <input
                    type='radio'
                    name='paymentMethod'
                    checked={paymentMethod === 'momo'}
                    onChange={() => setPaymentMethod('momo')}
                    className='mr-2'
                  />
                  <div>
                    <p className='font-medium text-gray-800'>{t('momo_payment')}</p>
                    <p className='text-sm text-gray-600'>{t('momo_payment_description')}</p>
                  </div>
                </div>
              </div>
            </div>

            <button
              onClick={handlePayment}
              disabled={loading}
              className={`w-full mt-6 py-2 rounded-md text-white transition ${loading ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'
                }`}
            >
              {loading ? t('processing') : t('confirm_payment')}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Payment