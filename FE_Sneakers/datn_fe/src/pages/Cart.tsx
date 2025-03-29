import { useState, useEffect } from 'react'
import { FaTrash, FaMinus, FaPlus } from 'react-icons/fa'
import { Link, useNavigate } from 'react-router-dom'
import toast, { Toaster } from 'react-hot-toast'
import { useTranslation } from 'react-i18next'

const CartPage = () => {
  const { t } = useTranslation() 
  const [cart, setCart] = useState([
    {
      id: 1,
      name: 'Phạm Ngọc Thi',
      price: 2000000,
      discount: 1500000,
      quantity: 1,
      image:
        'https://scontent.fhan14-3.fna.fbcdn.net/v/t39.30808-6/482032633_1688198495386207_8034474104949924030_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeEHSZ4c3uZtwA9bKev0gDCIirsaIka0kIOKuxoiRrSQg4DbWJWLFBP1N1SN2vI9l9Z8R55CPzXlrM7mc5Os0FmG&_nc_ohc=6ery4nlHW2wQ7kNvgFQVYlC&_nc_oc=AdlYvlzOLhkDb9DlKNMLKO9a9hvWwoc_guYf38YjquwhY0ZZpBs2yjsS8_RJtcleamI&_nc_zt=23&_nc_ht=scontent.fhan14-3.fna&_nc_gid=uLJp0ANoqJVqdBdvlUTmcw&oh=00_AYGOzfRzSqZ1lZMJ_mCSSGL6Ky2RDXT5zJSVDFMsyKu1NA&oe=67E04F8E',
      selected: false
    },
    {
      id: 2,
      name: 'Thi Ngọc Phạm',
      price: 2200000,
      discount: 1800000,
      quantity: 1,
      image:
        'https://scontent.fhan14-3.fna.fbcdn.net/v/t39.30808-6/482345942_1688198892052834_3992485807903702783_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=833d8c&_nc_eui2=AeHuvQ7S2p718O3FiLG2I0sMOsOqkQSgEso6w6qRBKASyujUSKdl_uW605T2iGzApe-SKaPgPRpljs71pNEXDH59&_nc_ohc=SQWUACzhBv4Q7kNvgEbfqeF&_nc_oc=AdlzfSgBdNvgunmQNR_MIKmuWEQyFLdN5aBVdQhBnK7yeVxZ3dgn01PL1OroWz_YJMo&_nc_zt=23&_nc_ht=scontent.fhan14-3.fna&_nc_gid=fgzRdhcAY_TcjrfWnYSAsw&oh=00_AYGQtilvaXpvQPt1Yccp3g3e-Lik00vE09JMYUCryxhT1w&oe=67E046F1',
      selected: false
    }
  ])
  const [discountCode, setDiscountCode] = useState('')
  const [discountAmount, setDiscountAmount] = useState(0)
  const [discountValid, setDiscountValid] = useState(true)

  const navigate = useNavigate()

  const isLoggedIn = !!localStorage.getItem('user')

  const increaseQuantity = (id: number) => {
    setCart(cart.map((item) => (item.id === id ? { ...item, quantity: item.quantity + 1 } : item)))
  }

  const decreaseQuantity = (id: number) => {
    setCart(cart.map((item) => (item.id === id && item.quantity > 1 ? { ...item, quantity: item.quantity - 1 } : item)))
  }

  const toggleSelect = (id: number) => {
    setCart(cart.map((item) => (item.id === id ? { ...item, selected: !item.selected } : item)))
  }

  const removeItem = (id: number) => {
    setCart(cart.filter((item) => item.id !== id))
    toast.success(t('item_removed'), { position: 'top-right' })
  }

  const totalOriginalPrice = cart.reduce((acc, item) => (item.selected ? acc + item.price * item.quantity : acc), 0)
  const totalDiscountPrice = cart.reduce((acc, item) => (item.selected ? acc + item.discount * item.quantity : acc), 0)
  const totalSavings = totalOriginalPrice - totalDiscountPrice

  useEffect(() => {
    toast.dismiss()
    if (discountCode === 'thicho') {
      setDiscountAmount(0.2 * totalDiscountPrice)
      setDiscountValid(true)
      toast.success(t('discount_applied'), { position: 'top-right' })
    } else if (discountCode) {
      setDiscountAmount(0)
      setDiscountValid(false)
      toast.error(t('discount_invalid_message'), { position: 'top-right' })
    } else {
      setDiscountAmount(0)
      setDiscountValid(true)
    }
  }, [discountCode, totalDiscountPrice, t]) 

  const handlePayment = () => {
    const finalAmount = totalDiscountPrice - discountAmount
    toast.success(t('payment_success', { amount: finalAmount.toLocaleString() }), { position: 'top-right' })
  }

  if (!isLoggedIn) {
    return (
      <div className='max-w-5xl mx-auto p-4 bg-white shadow-md rounded-lg mt-6 text-center'>
        <Toaster />
        <p className='text-gray-500 mb-4 text-red-500'>{t('not_logged_in')}</p>
        <Link
          to='/login'
          className='inline-block bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition'
        >
          {t('login_now')}
        </Link>
      </div>
    )
  }

  return (
    <div className='max-w-5xl mx-auto p-4 bg-white shadow-md rounded-lg mt-6'>
      <Toaster />
      <h2 className='text-2xl font-semibold mb-4 text-center'>{t('your_cart')}</h2>

      {cart.length === 0 ? (
        <p className='text-gray-500 text-center'>{t('cart_empty')}</p>
      ) : (
        <div className='space-y-4'>
          {cart.map((item) => (
            <div
              key={item.id}
              className='flex flex-col md:flex-row items-center justify-between bg-gray-100 p-3 rounded-lg'
            >
              <div className='flex items-center w-full md:w-auto'>
                <input
                  type='checkbox'
                  checked={item.selected}
                  onChange={() => toggleSelect(item.id)}
                  className='mr-3 transform scale-150 transition-all duration-200 ease-in-out hover:scale-160'
                />
                <img src={item.image} alt={item.name} className='w-20 h-20 object-cover rounded-lg' />
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
                  onClick={() => removeItem(item.id)}
                  className='p-2 text-red-500 hover:text-red-600 text-sm md:text-lg'
                >
                  <FaTrash />
                </button>
              </div>
            </div>
          ))}

          <div className='mt-6 p-5 bg-gray-200 rounded-lg text-sm md:text-lg'>
            <div className='flex justify-between font-medium'>
              <span>{t('total_price')}:</span>
              <span className='text-gray-700'>{totalOriginalPrice.toLocaleString()}đ</span>
            </div>
            <div className='flex justify-between font-medium text-green-600'>
              <span>{t('savings')}:</span>
              <span>-{totalSavings.toLocaleString()}đ</span>
            </div>
            <div className='flex justify-between text-base md:text-xl font-semibold mt-2'>
              <span>{t('subtotal')}:</span>
              <span className='text-red-500'>{totalDiscountPrice.toLocaleString()}đ</span>
            </div>

           
            <div className='mt-4'>
              <input
                type='text'
                placeholder={t('discount_code_placeholder')}
                value={discountCode}
                onChange={(e) => setDiscountCode(e.target.value)}
                className={`w-full p-3 border ${discountValid ? 'border-gray-300' : 'border-red-500'} rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200`}
              />
              {!discountValid && <p className='text-red-500 text-sm mt-2'>{t('discount_invalid')}</p>}
            </div>

          
            <div className='flex justify-between text-lg font-semibold mt-2'>
              <span>{t('discount')}:</span>
              <span className='text-green-600'>- {discountAmount.toLocaleString()}đ</span>
            </div>
            <div className='flex justify-between text-xl font-semibold mt-2'>
              <span>{t('final_total')}:</span>
              <span className='text-red-500'>{(totalDiscountPrice - discountAmount).toLocaleString()}đ</span>
            </div>

            <button
              onClick={handlePayment}
              className='w-full mt-4 bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition'
            >
              {t('checkout')}
            </button>
          </div>
        </div>
      )}
    </div>
  )
}

export default CartPage
