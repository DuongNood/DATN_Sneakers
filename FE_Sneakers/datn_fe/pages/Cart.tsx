import React, { useState, useEffect } from 'react'
import axios from 'axios'
import { useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'

interface CartItem {
  id: number
  name: string
  price: number
  discount: number
  quantity: number
  image: string
  size: string
  selected: boolean
}

const Cart: React.FC = () => {
  const { t } = useTranslation()
  const navigate = useNavigate()
  const [cartItems, setCartItems] = useState<CartItem[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    const fetchCart = async () => {
      try {
        const token = localStorage.getItem('token')
        if (!token) {
          toast.error(t('please_login_first'), { autoClose: 2000 })
          navigate('/login')
          return
        }

        const response = await axios.get('http://localhost:8000/api/carts/list', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        if (response.data.status === 'success') {
          setCartItems(response.data.data) // Giả sử API trả về mảng cart items
        } else {
          throw new Error(response.data.message || t('error_fetching_cart'))
        }
      } catch (err: any) {
        setError(err.message || t('error_fetching_cart'))
        toast.error(err.message || t('error_fetching_cart'), { autoClose: 2000 })
      } finally {
        setIsLoading(false)
      }
    }

    fetchCart()
  }, [navigate, t])

  if (isLoading) {
    return <div className='container mx-auto py-10 text-center'>{t('loading')}</div>
  }

  if (error) {
    return <div className='container mx-auto py-10 text-center text-red-600'>{error}</div>
  }

  return (
    <div className='container mx-auto px-4 py-10'>
      <h1 className='text-2xl font-bold mb-6'>{t('cart')}</h1>
      {cartItems.length === 0 ? (
        <p className='text-gray-600'>{t('cart_empty')}</p>
      ) : (
        <div className='grid gap-4'>
          {cartItems.map((item) => (
            <div key={item.id} className='flex items-center border p-4 rounded-lg shadow-sm'>
              <img src={item.image} alt={item.name} className='w-20 h-20 object-cover rounded-md mr-4' />
              <div className='flex-1'>
                <h3 className='text-lg font-semibold'>{item.name}</h3>
                <p className='text-sm text-gray-600'>
                  {t('size')}: {item.size}
                </p>
                <p className='text-sm text-gray-600'>
                  {t('quantity')}: {item.quantity}
                </p>
                <div className='flex items-center gap-2'>
                  {item.price > item.discount && (
                    <p className='text-sm text-gray-500 line-through'>{item.price.toLocaleString('vi-VN')}đ</p>
                  )}
                  <p className='text-lg font-bold text-red-500'>{item.discount.toLocaleString('vi-VN')}đ</p>
                </div>
              </div>
            </div>
          ))}
          <button
            onClick={() => navigate('/checkout')}
            className='mt-6 bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition'
          >
            {t('proceed_to_checkout')}
          </button>
        </div>
      )}
    </div>
  )
}

export default Cart
