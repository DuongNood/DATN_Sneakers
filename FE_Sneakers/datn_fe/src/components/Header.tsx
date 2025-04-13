// src/components/Header.tsx
import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { FiShoppingCart, FiUser, FiMenu, FiX, FiChevronDown } from 'react-icons/fi'
import { motion } from 'framer-motion'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'
import axios from 'axios'
import Search from './Search'
import { useCart } from '../contexts/CartContext'

const Header = () => {
  const { t, i18n } = useTranslation()
  const [search, setSearch] = useState('')
  const [menuOpen, setMenuOpen] = useState(false)
  const [mobileProductOpen, setMobileProductOpen] = useState(false)
  const { cartCount, updateCartCount } = useCart() // Sử dụng context
  const navigate = useNavigate()
  const isLoggedIn = !!localStorage.getItem('user')
  const user = isLoggedIn ? JSON.parse(localStorage.getItem('user') || '{}') : null

  // Hàm lấy số lượng giỏ hàng từ API
  const fetchCartCount = async () => {
    if (!isLoggedIn) {
      updateCartCount(0)
      return
    }

    try {
      const response = await axios.get('http://localhost:8000/api/carts/list', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      })
      const items = response.data.items || []
      const totalItems = items.reduce((sum: number, item: any) => sum + item.quantity, 0)
      updateCartCount(totalItems)
    } catch (error: any) {
      console.error('Error fetching cart count:', error)
      updateCartCount(0)
    }
  }

  // Gọi fetchCartCount khi component mount hoặc khi isLoggedIn thay đổi
  useEffect(() => {
    fetchCartCount()
  }, [isLoggedIn])

  const handleLogout = () => {
    localStorage.clear()
    updateCartCount(0) // Reset cartCount khi logout
    toast.success(t('logout_success'), { autoClose: 1000 })
    navigate('/login')
    setMenuOpen(false)
  }

  const handleLinkClick = () => {
    setMenuOpen(false)
    setMobileProductOpen(false)
  }

  return (
    <header className='bg-white shadow-md sticky top-0 z-50 p-2'>
      <div className='container mx-auto px-6 md:px-20 py-4 flex justify-between items-center'>
        <Link to='/' className='text-3xl font-bold text-gray-800 tracking-wide'>
          Pole
        </Link>

        <nav className='hidden md:flex space-x-8'>
          <Link to='/' className='nav-link'>
            {t('home')}
          </Link>
          <div className='relative group hidden md:block'>
            <button className='nav-link'>{t('products')}</button>
            <div className='absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300'>
              <Link to='/giay-nam' className='dropdown-link'>
                {t('men_shoes')}
              </Link>
              <Link to='/giay-nu' className='dropdown-link'>
                {t('women_shoes')}
              </Link>
            </div>
          </div>
          <Link to='/product-sale' className='nav-link'>
            {t('sale')}
          </Link>
          <Link to='/news' className='nav-link'>
            {t('news')}
          </Link>
          <Link to='/contact' className='nav-link'>
            {t('contact')}
          </Link>
        </nav>

        <div className='flex items-center space-x-4 md:space-x-6'>
          <div className='relative hidden md:block'>
            <Search value={search} onChange={(e) => setSearch(e.target.value)} />
          </div>

          {!isLoggedIn ? (
            <Link to='/login' className='icon-link'>
              <FiUser />
            </Link>
          ) : (
            <div className='relative group'>
              <div className='w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center cursor-pointer'>
                <img
                  src={user?.image_user || 'https://m.yodycdn.com/blog/meme-ech-xanh-yody-vn-55.jpg'}
                  alt='Avatar'
                  className='w-full h-full rounded-full object-cover'
                />
              </div>
              <div className='absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300'>
                <p className='px-4 py-2 bg-gray-300 text-blue-600 '>{t('greeting', {})}</p>
                <Link to='/profile' className='dropdown-link'>
                  {t('profile')}
                </Link>
                <Link to='/orders' className='dropdown-link'>
                  {t('orders')}
                </Link>
                <button onClick={handleLogout} className='w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100'>
                  {t('logout')}
                </button>
              </div>
            </div>
          )}

          <Link to='/cart' className='relative icon-link'>
            <FiShoppingCart />
            {cartCount > 0 && (
              <span className='cart-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center'>
                {cartCount}
              </span>
            )}
          </Link>

          <button onClick={() => setMenuOpen(!menuOpen)} className='md:hidden text-gray-600 text-2xl'>
            {menuOpen ? <FiX /> : <FiMenu />}
          </button>
        </div>
      </div>

      {menuOpen && (
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          exit={{ opacity: 0, y: -20 }}
          className='md:hidden absolute top-16 left-0 w-full bg-white shadow-md py-4'
        >
          <nav className='flex flex-col space-y-4 px-6'>
            <Link to='/' className='mobile-nav-link' onClick={handleLinkClick}>
              {t('home')}
            </Link>
            <div>
              <button
                onClick={() => setMobileProductOpen(!mobileProductOpen)}
                className='mobile-nav-link flex justify-between w-full'
              >
                {t('products')}
                <FiChevronDown
                  className={`transition-transform duration-300 ${mobileProductOpen ? 'rotate-180' : ''}`}
                />
              </button>
              {mobileProductOpen && (
                <div className='pl-4 space-y-2'>
                  <Link to='/giay-nam' className='dropdown-link' onClick={handleLinkClick}>
                    {t('men_shoes')}
                  </Link>
                  <Link to='/giay-nu' className='dropdown-link' onClick={handleLinkClick}>
                    {t('women_shoes')}
                  </Link>
                </div>
              )}
            </div>
            <Link to='/product-sale' className='mobile-nav-link' onClick={handleLinkClick}>
              {t('sale')}
            </Link>
            <Link to='/news' className='mobile-nav-link' onClick={handleLinkClick}>
              {t('news')}
            </Link>
            <Link to='/contact' className='mobile-nav-link' onClick={handleLinkClick}>
              {t('contact')}
            </Link>
            {isLoggedIn && (
              <>
                <Link to='/profile' className='mobile-nav-link' onClick={handleLinkClick}>
                  {t('profile')}
                </Link>
                <Link to='/orders' className='mobile-nav-link' onClick={handleLinkClick}>
                  {t('orders')}
                </Link>
                <button onClick={handleLogout} className='mobile-nav-link text-left'>
                  {t('logout')}
                </button>
              </>
            )}
            <input
              key={i18n.language}
              type='text'
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className='w-full px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-300'
              placeholder={t('search_placeholder')}
            />
          </nav>
        </motion.div>
      )}
    </header>
  )
}

export default Header
