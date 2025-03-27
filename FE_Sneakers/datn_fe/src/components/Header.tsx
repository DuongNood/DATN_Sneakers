import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { FiShoppingCart, FiUser, FiMenu, FiX, FiChevronDown } from 'react-icons/fi'
import { motion } from 'framer-motion'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'
import Search from './Search'

const Header = () => {
  const { t, i18n } = useTranslation()
  const [search, setSearch] = useState('')
  const [menuOpen, setMenuOpen] = useState(false)
  const [mobileProductOpen, setMobileProductOpen] = useState(false)
  const [langOpen, setLangOpen] = useState(false)
  const navigate = useNavigate()
  const isLoggedIn = !!localStorage.getItem('user')
  const user = isLoggedIn ? JSON.parse(localStorage.getItem('user') || '{}') : null

  const changeLanguage = (lng: string) => {
    i18n.changeLanguage(lng).then(() => {
      setLangOpen(false)
      toast.success(`${t('change_language')} ${lng === 'en' ? t('english') : t('vietnamese')}`)
      console.log('Language changed to:', i18n.language)
      console.log('Placeholder:', t('search_placeholder'))
    })
  }

  const handleLogout = () => {
    localStorage.clear()
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
                  src={
                    user?.image_user ||
                    'https://scontent.fhan14-1.fna.fbcdn.net/v/t39.30808-1/467739613_3965334987038469_313956456266942570_n.jpg?stp=dst-jpg_s100x100_tt6&_nc_cat=101&ccb=1-7&_nc_sid=e99d92&_nc_eui2=AeH-hyRvfCgg54fIXSTDPi6H9qdYqycIZez2p1irJwhl7Nm59pSiLrQ4AjKulneabvaMyVT1moxmyKYuCw6W851G&_nc_ohc=j_T3Yc2pZRIQ7kNvgGq3-q2&_nc_oc=Adn5wZkr2hJTI0tMtHglbBrBl_LFbh8SY5_0a3Tu2TikTqDBbpngqIBtIuM_dSv2d5E&_nc_zt=24&_nc_ht=scontent.fhan14-1.fna&_nc_gid=6UqNsEWUCaesObVKorEZtA&oh=00_AYHO5zoCOamqXWV_eItw5UMoAZvXXJzpDk9vMlA5Cs9BEA&oe=67EB20C0'
                  }
                  alt='Avatar'
                  className='w-full h-full rounded-full object-cover'
                />
              </div>
              <div className='absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300'>
                <p className='px-4 py-2 text-gray-700 flex'>
                  {t('greeting', {
                    name: user?.name?.length > 12 ? user.name.slice(0, 12) + '...' : user.name || t('default_name')
                  })}
                </p>
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
