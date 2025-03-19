import { useState } from 'react'
import { FaTrash, FaMinus, FaPlus } from 'react-icons/fa'
import toast, { Toaster } from 'react-hot-toast'

const CartPage = () => {
  const [cart, setCart] = useState([
    { id: 1, name: 'Nike Air Max', price: 2000000, discount: 1500000, quantity: 1, image: '/images/shoe1.jpg' },
    { id: 2, name: 'Adidas Ultraboost', price: 2200000, discount: 1800000, quantity: 1, image: '/images/shoe2.jpg' }
  ])

  const increaseQuantity = (id: number) => {
    setCart(cart.map((item) => (item.id === id ? { ...item, quantity: item.quantity + 1 } : item)))
  }

  const decreaseQuantity = (id: number) => {
    setCart(cart.map((item) => (item.id === id && item.quantity > 1 ? { ...item, quantity: item.quantity - 1 } : item)))
  }

  const confirmRemove = (id: number) => {
    const removedItem = cart.find((item) => item.id === id)

    toast(
      (t) => (
        <div className='flex flex-col gap-2'>
          <p className='text-sm'>
            Bạn có chắc muốn xóa <strong>{removedItem?.name}</strong>?
          </p>
          <div className='flex justify-end gap-2'>
            <button className='px-3 py-1 text-gray-600 bg-gray-200 rounded-md' onClick={() => toast.dismiss(t.id)}>
              Hủy
            </button>
            <button
              className='px-3 py-1 text-white bg-red-500 rounded-md hover:bg-red-600'
              onClick={() => removeItem(id, t.id)}
            >
              Xóa
            </button>
          </div>
        </div>
      ),
      { duration: 5000 }
    )
  }

  const removeItem = (id: number, toastId: string) => {
    setCart(cart.filter((item) => item.id !== id))
    toast.dismiss(toastId)
    toast.success('Đã xóa sản phẩm khỏi giỏ hàng!', { position: 'top-right' })
  }

  const totalOriginalPrice = cart.reduce((acc, item) => acc + item.price * item.quantity, 0)
  const totalDiscountPrice = cart.reduce((acc, item) => acc + item.discount * item.quantity, 0)
  const totalSavings = totalOriginalPrice - totalDiscountPrice

  return (
    <div className='max-w-5xl mx-auto p-4 bg-white shadow-md rounded-lg mt-6'>
      <Toaster />
      <h2 className='text-2xl font-semibold mb-4 text-center'>Giỏ hàng của bạn</h2>

      {cart.length === 0 ? (
        <p className='text-gray-500 text-center'>Giỏ hàng của bạn đang trống, mua hàng ngay</p>
      ) : (
        <div className='space-y-4'>
          {cart.map((item) => (
            <div
              key={item.id}
              className='flex flex-col md:flex-row items-center justify-between bg-gray-100 p-3 rounded-lg'
            >
              <div className='flex items-center w-full md:w-auto'>
                <img
                  src='https://kingshoes.vn/data/upload/media/SNEAKER-315122-111-AIR-FORCE-1-07-NIKE-KINGSHOES.VN-TPHCM-TANBINH-14.jpg'
                  alt={item.name}
                  className='w-20 h-20 object-cover rounded-lg'
                />
                <div className='ml-3'>
                  <h3 className='text-sm md:text-lg font-medium'>{item.name}</h3>
                  <div className='flex items-center space-x-2 text-xs md:text-sm'>
                    <span className='text-gray-500 line-through'>{item.price.toLocaleString()}đ</span>
                    <span className='text-red-500 font-semibold'>{item.discount.toLocaleString()}đ</span>
                  </div>
                </div>
              </div>

              <div className='flex w-full justify-between md:w-auto md:space-x-4 mt-3 md:mt-0'>
                <div className='flex items-center space-x-2'>
                  <button onClick={() => decreaseQuantity(item.id)} className='p-1 bg-gray-300 rounded-md'>
                    <FaMinus className='text-gray-600 text-xs md:text-sm' />
                  </button>
                  <span className='px-3 py-1 bg-gray-200 rounded-md text-xs md:text-sm'>{item.quantity}</span>
                  <button onClick={() => increaseQuantity(item.id)} className='p-1 bg-gray-300 rounded-md'>
                    <FaPlus className='text-gray-600 text-xs md:text-sm' />
                  </button>
                </div>

                <button
                  onClick={() => confirmRemove(item.id)}
                  className='p-2 text-red-500 hover:text-red-600 text-sm md:text-lg'
                >
                  <FaTrash />
                </button>
              </div>
            </div>
          ))}

          <div className='mt-6 p-5 bg-gray-200 rounded-lg text-sm md:text-lg'>
            <div className='flex justify-between font-medium'>
              <span>Tổng tiền:</span>
              <span className='text-gray-700'>{totalOriginalPrice.toLocaleString()}đ</span>
            </div>
            <div className='flex justify-between font-medium text-green-600'>
              <span>Tiết kiệm:</span>
              <span>-{totalSavings.toLocaleString()}đ</span>
            </div>
            <div className='flex justify-between text-base md:text-xl font-semibold mt-2'>
              <span>Thành tiền:</span>
              <span className='text-red-500'>{totalDiscountPrice.toLocaleString()}đ</span>
            </div>
            <button className='w-full mt-4 bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition'>
              Thanh toán
            </button>
          </div>
        </div>
      )}
    </div>
  )
}

export default CartPage
