import React, { useState, useEffect } from 'react'
import { toast } from 'react-toastify'
import OrderItem from './OrderItem'
import { getOrders, cancelOrder } from '../services/api'
import { Order } from '../types/order'
import { useTranslation } from 'react-i18next'

interface OrderListProps {
  statusFilter: string
}

const OrderList: React.FC<OrderListProps> = ({ statusFilter }) => {
  const { t } = useTranslation()
  const [orders, setOrders] = useState<Order[]>([])
  const [currentPage, setCurrentPage] = useState<number>(1)
  const [lastPage, setLastPage] = useState<number>(1)
  const [loading, setLoading] = useState<boolean>(false)
  const [error, setError] = useState<string>('')

  const fetchOrders = async () => {
    setLoading(true)
    setError('')
    try {
      console.log('Fetching orders, page:', currentPage, 'status:', statusFilter)
      const data = await getOrders(currentPage, statusFilter, t)
      console.log('Orders received:', data.data)
      setOrders(data.data || [])
      setLastPage(data.meta?.last_page || 1)
    } catch (err: any) {
      console.error('Fetch orders failed:', err)
      setError(err.message || t('fetch_error'))
    } finally {
      setLoading(false)
    }
  }

  const handleCancelOrder = async (orderId: number, reason: string) => {
    try {
      console.log('Cancel order:', orderId, 'Reason:', reason)
      const response = await cancelOrder(orderId, reason, t)
      toast.success(response.message || t('cancel_success'), { autoClose: 1000 })
      fetchOrders()
    } catch (err: any) {
      console.error('Cancel order failed:', err)
      // toast.error đã xử lý trong api.ts
    }
  }

  useEffect(() => {
    console.log('OrderList useEffect, statusFilter:', statusFilter, 'reset page')
    setCurrentPage(1)
    fetchOrders()
  }, [statusFilter])

  useEffect(() => {
    console.log('OrderList useEffect, page:', currentPage)
    fetchOrders()
  }, [currentPage])

  const SkeletonLoading = () => (
    <div className='space-y-4 animate-pulse'>
      {[...Array(3)].map((_, i) => (
        <div key={i} className='flex flex-col border border-gray-300 rounded-lg p-4 bg-white'>
          <div className='flex items-center gap-4'>
            <div className='flex-1 space-y-2'>
              <div className='h-4 bg-gray-300 rounded w-1/4'></div>
              <div className='h-4 bg-gray-300 rounded w-1/2'></div>
              <div className='h-4 bg-gray-300 rounded w-1/3'></div>
              <div className='h-4 bg-gray-300 rounded w-1/5'></div>
            </div>
            <div className='flex gap-2'>
              <div className='h-8 w-16 bg-gray-300 rounded-md'></div>
              <div className='h-8 w-16 bg-gray-300 rounded-md'></div>
            </div>
          </div>
        </div>
      ))}
    </div>
  )

  console.log('OrderList rendered, orders:', orders.length, 'error:', error)
  return (
    <div className='flex-1 p-6'>
      {loading && <SkeletonLoading />}
      {!loading && error && (
        <div className='text-center'>
          <p className='text-red-500 text-lg'>{error}</p>
          <button onClick={fetchOrders} className='mt-4 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700'>
            {t('retry')}
          </button>
        </div>
      )}
      {!loading && !error && (
        <div className='space-y-4'>
          {orders.length > 0 ? (
            orders.map((order) => <OrderItem key={order.id} order={order} onCancel={handleCancelOrder} />)
          ) : (
            <p className='text-gray-500 text-center'>{t('no_orders')}</p>
          )}
        </div>
      )}
      {!loading && lastPage > 1 && (
        <div className='flex justify-center gap-2 mt-6'>
          <button
            onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}
            disabled={currentPage === 1}
            className='px-4 py-2 bg-gray-200 rounded-md disabled:opacity-50 hover:bg-gray-300'
          >
            {t('previous')}
          </button>
          <span className='px-4 py-2'>
            {t('page')} {currentPage} / {lastPage}
          </span>
          <button
            onClick={() => setCurrentPage((prev) => Math.min(prev + 1, lastPage))}
            disabled={currentPage === lastPage}
            className='px-4 py-2 bg-gray-200 rounded-md disabled:opacity-50 hover:bg-gray-300'
          >
            {t('next')}
          </button>
        </div>
      )}
    </div>
  )
}

export default OrderList
