import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { FiShoppingCart, FiUser, FiMenu, FiX, FiChevronDown } from 'react-icons/fi'
import { motion } from 'framer-motion'
import { useAuth, UserButton, useUser } from '@clerk/clerk-react'

const Header = () => {
  const [search, setSearch] = useState('')
  const [menuOpen, setMenuOpen] = useState(false)
  const [mobileProductOpen, setMobileProductOpen] = useState(false)
  const { isSignedIn } = useAuth()
  const { user } = useUser() // Lấy thông tin user từ Clerk
  const navigate = useNavigate()

  // Chuyển hướng về trang chủ ngay sau khi đăng nhập thành công
  useEffect(() => {
    if (isSignedIn && user) {
      // Lưu user vào localStorage
      localStorage.setItem(
        'user',
        JSON.stringify({
          id: user.id,
          name: user.fullName,
          email: user.primaryEmailAddress?.emailAddress,
          avatar: user.imageUrl
        })
      )
      navigate('/') // Chuyển về trang chủ
    }
  }, [isSignedIn, user, navigate])

  return (
    <header className='bg-white shadow-md sticky top-0 z-50'>
      <div className='container mx-auto px-6 md:px-20 py-4 flex justify-between items-center'>
        {/* Logo */}
        <Link to='/' className='text-3xl font-bold text-gray-800 tracking-wide'>
          Pole
        </Link>

        {/* Menu điều hướng */}
        <nav className='hidden md:flex space-x-8'>
          <Link to='/' className='nav-link'>
            Trang chủ
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
            Sale
          </Link>
          <Link to='/ho-tro' className='nav-link'>
            Tin tức
          </Link>
          <Link to='/contact' className='nav-link'>
            Liên hệ
          </Link>
        </nav>

        {/* Phần bên phải (Tìm kiếm, User, Giỏ hàng) */}
        <div className='flex items-center space-x-4 md:space-x-6'>
          {/* Ô tìm kiếm */}
          <div className='relative hidden md:block'>
            <input
              type='text'
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className='w-64 px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-full 
              focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-300'
              placeholder='Tìm kiếm sản phẩm...'
            />
          </div>

          {/* Hiển thị Avatar nếu đăng nhập, nếu không thì hiện icon user */}
          {isSignedIn ? (
            <UserButton afterSignOutUrl='/' />
          ) : (
            <Link to='/login' className='icon-link'>
              <FiUser />
            </Link>
          )}

          {/* Giỏ hàng */}
          <Link to='/cart' className='relative icon-link'>
            <FiShoppingCart />
            <span className='cart-badge'>3</span>
          </Link>

          {/* Nút menu cho mobile */}
          <button onClick={() => setMenuOpen(!menuOpen)} className='md:hidden text-gray-600 text-2xl'>
            {menuOpen ? <FiX /> : <FiMenu />}
          </button>
        </div>
      </div>

      {/* Menu Mobile */}
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

            {/* Dropdown sản phẩm trên mobile */}
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

            {/* Ô tìm kiếm trên mobile */}
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
