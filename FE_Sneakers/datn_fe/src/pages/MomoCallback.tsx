import React, { useEffect } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'
import axios from 'axios'

const MomoCallback: React.FC = () => {
  const { t } = useTranslation()
  const location = useLocation()
  const navigate = useNavigate()

  useEffect(() => {
    const handleCallback = async () => {
      // Lấy query parameters từ URL
      const query = new URLSearchParams(location.search)
      const resultCode = query.get('resultCode')
      const orderId = query.get('orderId')

      if (resultCode === '0') {
        // Thanh toán thành công
        try {
          // Lấy thông tin đơn hàng từ localStorage
          const pendingOrder = JSON.parse(localStorage.getItem('pendingOrder') || '{}')
          if (!pendingOrder.orderId || pendingOrder.orderId !== orderId) {
            throw new Error('Invalid order data')
          }

          // Chuẩn bị dữ liệu để lưu đơn hàng
          const orderData = {
            user_id: 1, // Giả định user_id
            products: pendingOrder.products.map((product: any) => ({
              product_id: product.id,
              quantity: product.quantity,
              size: product.size,
              price: product.discounted_price
            })),
            shipping_info: {
              full_name: pendingOrder.shippingInfo.fullName,
              email: pendingOrder.shippingInfo.email,
              phone: pendingOrder.shippingInfo.phone,
              address: pendingOrder.shippingInfo.address
            },
            total: pendingOrder.total,
            coupon_discount: pendingOrder.couponDiscount,
            shipping_fee: pendingOrder.shippingFee,
            payment_method: 'momo',
            status: 'paid' // Thanh toán thành công
          }

          const token = localStorage.getItem('token')
          if (!token) {
            throw new Error(t('no_token'))
          }

          // Gửi dữ liệu lên API để lưu đơn hàng
          const response = await axios.post('http://localhost:8000/api/orders', orderData, {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: 'application/json'
            }
          })

          if (response.data.status === 'success') {
            toast.success(t('order_placed_successfully_momo'), { autoClose: 2000 })
            localStorage.removeItem('pendingOrder') // Xóa dữ liệu tạm
            navigate('/') // Chuyển hướng về trang chủ
          } else {
            throw new Error(response.data.message || t('order_failed'))
          }
        } catch (error: any) {
          console.error('Error saving order after MoMo payment:', error)
          toast.error(error.response?.data?.message || error.message || t('order_failed'), {
            autoClose: 2000
          })
          navigate('/payment', { state: JSON.parse(localStorage.getItem('pendingOrder') || '{}') })
        }
      } else {
        // Thanh toán thất bại
        toast.error(t('momo_payment_failed'), { autoClose: 2000 })
        navigate('/payment', { state: JSON.parse(localStorage.getItem('pendingOrder') || '{}') })
      }
    }

    handleCallback()
  }, [location, navigate, t])

  return (
    <div className='min-h-screen bg-gray-100 font-sans flex items-center justify-center'>
      <p className='text-gray-600'>{t('processing_payment')}</p>
    </div>
  )
}

export default MomoCallback
