import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { FiShoppingCart, FiUser, FiMenu, FiX, FiChevronDown } from 'react-icons/fi'
import { motion } from 'framer-motion'
import { toast } from 'react-toastify'
import Search from './Search'

const Header = () => {
  const [search, setSearch] = useState('')
  const [menuOpen, setMenuOpen] = useState(false)
  const [mobileProductOpen, setMobileProductOpen] = useState(false)
  const navigate = useNavigate()
  const isLoggedIn = !!localStorage.getItem('user')
  const user = isLoggedIn ? JSON.parse(localStorage.getItem('user')) : null
  // Hàm đăng xuất
  const handleLogout = () => {
    localStorage.clear()
    toast.success('Đăng xuất thành công.'), { autoclose: 1000 }
    navigate('/login')
  }

  return (
    <header className='bg-white shadow-md sticky top-0 z-50 p-2'>
      <div className='container mx-auto px-6 md:px-20 py-4 flex justify-between items-center'>
        <Link to='/' className='text-3xl font-bold text-gray-800 tracking-wide'>
          Pole
        </Link>

        <nav className='hidden md:flex space-x-8'>
          <Link to='/' className='nav-link'>
            Trang Chủ
          </Link>
          <div className='relative group hidden md:block'>
            <button className='nav-link'>Sản phẩm</button>
            <div className='absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300'>
              <Link to='/giay-nam' className='dropdown-link'>
                Giày Nam
              </Link>
              <Link to='/giay-nu' className='dropdown-link'>
                Giày Nữ
              </Link>
            </div>
          </div>
          <Link to='/product-sale' className='nav-link'>
            Giảm Giá
          </Link>
          <Link to='/ho-tro' className='nav-link'>
            Tin Tức
          </Link>
          <Link to='/contact' className='nav-link'>
            Liên Hệ
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
                  src={
                    user?.avatar ||
                    'https://gentlenobra.net/wp-content/uploads/2024/02/hinh-anh-gai-xinh-nude-3.jpg.webp'
                  }
                  alt='Avatar'
                  className='w-full h-full rounded-full object-cover'
                />
              </div>
              <div className='absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300'>
                <p className='px-4 py-2 text-gray-700 flex'>
                  Hi,
                  <div className='text-blue-600'>
                    {(user?.name && user.name.length > 12 ? user.name.slice(0, 12) + '...' : user.name) || 'đại ka'}
                  </div>{' '}
                </p>
                <Link to='/profile' className='dropdown-link'>
                  Tài khoản của tôi
                </Link>
                <Link to='/orders' className='dropdown-link'>
                  Đơn hàng
                </Link>
                <button onClick={handleLogout} className='w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100'>
                  Đăng xuất
                </button>
              </div>
            </div>
          )}

          <Link to='/cart' className='relative icon-link'>
            <FiShoppingCart />
            <span className='cart-badge'>3</span>
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
            <Link to='/' className='mobile-nav-link'>
              Trang chủ
            </Link>
            <div>
              <button
                onClick={() => setMobileProductOpen(!mobileProductOpen)}
                className='mobile-nav-link flex justify-between w-full'
              >
                Sản phẩm
                <FiChevronDown
                  className={`transition-transform duration-300 ${mobileProductOpen ? 'rotate-180' : ''}`}
                />
              </button>
              {mobileProductOpen && (
                <div className='pl-4 space-y-2'>
                  <Link to='/giay-nam' className='dropdown-link'>
                    Giày Nam
                  </Link>
                  <Link to='/giay-nu' className='dropdown-link'>
                    Giày Nữ
                  </Link>
                </div>
              )}
            </div>
            <Link to='/product-sale' className='mobile-nav-link'>
              Sale
            </Link>
            <Link to='/ho-tro' className='mobile-nav-link'>
              Tin tức
            </Link>
            <Link to='/contact' className='mobile-nav-link'>
              Liên hệ
            </Link>
            {isLoggedIn && (
              <>
                <Link to='/profile' className='mobile-nav-link'>
                  Quản lý thông tin
                </Link>
                <Link to='/orders' className='mobile-nav-link'>
                  Quản lý đơn hàng
                </Link>
                <button onClick={handleLogout} className='mobile-nav-link text-left'>
                  Đăng xuất
                </button>
              </>
            )}
            <input
              type='text'
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className='w-full px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-full 
              focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-300'
              placeholder='Tìm kiếm...'
            />
          </nav>
        </motion.div>
      )}
    </header>
  )
}

export default Header
