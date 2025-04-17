import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

const Sidebar: React.FC = () => {
  const { t } = useTranslation()
  const location = useLocation()

  console.log('Sidebar rendered, pathname:', location.pathname)

  return (
    <div className='bg-white p-6 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl min-w-[200px]'>
      <nav className='space-y-3'>
        <Link
          to='/profile'
          className={`flex items-center p-3 rounded-lg transition-all duration-200 whitespace-nowrap ${
            location.pathname === '/profile'
              ? 'text-blue-600 bg-blue-50 text-lg font-semibold'
              : 'text-gray-700 hover:bg-indigo-50 text-base font-medium'
          }`}
        >
          <span className='mr-3'>👤</span> {t('my_account')}
        </Link>
        <Link
          to='/change-password'
          className={`flex items-center p-3 rounded-lg transition-all duration-200 whitespace-nowrap ${
            location.pathname === '/change-password'
              ? 'text-blue-600 bg-blue-50 text-lg font-semibold'
              : 'text-gray-700 hover:bg-indigo-50 text-base font-medium'
          }`}
        >
          <span className='mr-3'>🔒</span> {t('change_password')}
        </Link>
        <Link
          to='/orders'
          className={`flex items-center p-3 rounded-lg transition-all duration-200 whitespace-nowrap ${
            location.pathname === '/orders'
              ? 'text-blue-600 bg-blue-50 text-lg font-semibold'
              : 'text-gray-700 hover:bg-indigo-50 text-base font-medium'
          }`}
        >
          <span className='mr-3'>📋</span> {t('orders')}
        </Link>
      </nav>
    </div>
  )
}

export default Sidebar
