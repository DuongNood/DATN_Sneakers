import React, { useState, useEffect } from 'react'
import axios from 'axios'
import { useTranslation } from 'react-i18next'
import { Link } from 'react-router-dom'

interface OrderDetail {
  id: number
  product_id: number
  product_name: string
  variant_id: number
  size: string
  quantity: number
  price: number
  total_price: number
  image_url: string
}

interface Order {
  id: number
  order_code: string
  recipient_name: string
  recipient_phone: string
  recipient_address: string
  promotion: number
  shipping_fee: number
  total_price: number
  payment_method: string
  payment_status: string
  status: string
  status_text: string
  total_items: number
  created_at: string
  updated_at: string
  order_details: OrderDetail[]
}

interface ApiResponse {
  data: Order[]
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number
    last_page: number
    path: string
    per_page: number
    to: number
    total: number
  }
}

const OrderHistory: React.FC = () => {
  const { t } = useTranslation()
  const [orders, setOrders] = useState<Order[]>([])
  const [loading, setLoading] = useState<boolean>(true)
  const [error, setError] = useState<string | null>(null)
  const [currentPage, setCurrentPage] = useState<number>(1)
  const [lastPage, setLastPage] = useState<number>(1)

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          throw new Error('No authentication token found')
        }

        const response = await axios.get<ApiResponse>(`/api/orders?page=${currentPage}`, {
          headers: { Authorization: `Bearer ${token}` }
        })

        setOrders(response.data.data)
        setLastPage(response.data.meta.last_page)
        setLoading(false)
      } catch (err: any) {
        setError(err.response?.data?.message || t('fetch_orders_error'))
        setLoading(false)
      }
    }

    fetchOrders()
  }, [currentPage, t])

  const handlePageChange = (page: number) => {
    if (page >= 1 && page <= lastPage) {
      setCurrentPage(page)
      setLoading(true)
    }
  }

  if (loading) {
    return <div className='text-center p-4'>{t('loading')}</div>
  }

  if (error) {
    return <div className='text-center p-4 text-red-500'>{error}</div>
  }

  return (
    <div className='container mx-auto p-4'>
      <h1 className='text-2xl font-bold mb-4'>{t('order_history')}</h1>
      {orders.length === 0 ? (
        <p className='text-gray-500'>{t('no_orders_found')}</p>
      ) : (
        <div className='space-y-6'>
          {orders.map((order) => (
            <div key={order.id} className='border rounded-lg p-4 shadow-sm'>
              <div className='flex justify-between items-center mb-2'>
                <h2 className='text-lg font-semibold'>
                  {t('order')} #{order.order_code}
                </h2>
                <span
                  className={`text-sm px-2 py-1 rounded ${
                    order.status === 'cho_xac_nhan'
                      ? 'bg-yellow-100 text-yellow-800'
                      : order.status === 'da_giao'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-gray-100 text-gray-800'
                  }`}
                >
                  {order.status_text}
                </span>
              </div>
              <p className='text-sm text-gray-600'>
                {t('placed_on')}: {new Date(order.created_at).toLocaleString()}
              </p>
              <p className='text-sm text-gray-600'>
                {t('total_items')}: {order.total_items}
              </p>
              <p className='text-sm text-gray-600'>
                {t('total_price')}: {order.total_price.toLocaleString()} VNĐ
              </p>
              <div className='mt-4'>
                <h3 className='text-md font-medium'>{t('products')}</h3>
                {order.order_details.map((detail) => (
                  <div key={detail.id} className='flex items-center gap-4 mt-2'>
                    <img
                      src={detail.image_url}
                      alt={detail.product_name}
                      className='w-16 h-16 object-cover rounded-md'
                    />
                    <div>
                      <p className='font-medium'>{detail.product_name}</p>
                      <p className='text-sm text-gray-600'>
                        {t('size')}: {detail.size} | {t('quantity')}: {detail.quantity}
                      </p>
                      <p className='text-sm text-gray-600'>
                        {t('unit_price')}: {detail.price.toLocaleString()} VNĐ
                      </p>
                    </div>
                  </div>
                ))}
              </div>
              <div className='mt-4'>
                <Link to={`/orders/${order.id}`} className='text-blue-500 hover:underline'>
                  {t('view_details')}
                </Link>
              </div>
            </div>
          ))}
        </div>
      )}
      {lastPage > 1 && (
        <div className='flex justify-center gap-2 mt-4'>
          <button
            onClick={() => handlePageChange(currentPage - 1)}
            disabled={currentPage === 1}
            className='px-4 py-2 bg-gray-200 rounded disabled:opacity-50'
          >
            {t('previous')}
          </button>
          <span className='px-4 py-2'>
            {currentPage} / {lastPage}
          </span>
          <button
            onClick={() => handlePageChange(currentPage + 1)}
            disabled={currentPage === lastPage}
            className='px-4 py-2 bg-gray-200 rounded disabled:opacity-50'
          >
            {t('next')}
          </button>
        </div>
      )}
    </div>
  )
}

export default OrderHistory
