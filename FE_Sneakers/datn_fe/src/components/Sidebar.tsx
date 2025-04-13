import React from 'react'
import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

const Sidebar: React.FC = () => {
  const { t } = useTranslation()

  console.log('Sidebar rendered')
  return (
    <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
      <nav className='space-y-2'>
        <Link
          to='/profile'
          className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md data-[active=true]:text-blue-600 data-[active=true]:bg-blue-50'
          data-active={location.pathname === '/profile'}
        >
          <span className='mr-2'>👤</span> {t('my_account')}
        </Link>
        <Link
          to='/change-password'
          className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md data-[active=true]:text-blue-600 data-[active=true]:bg-blue-50'
          data-active={location.pathname === '/change-password'}
        >
          <span className='mr-2'>🔒</span> {t('change_password')}
        </Link>
        <Link
          to='/orders'
          className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md data-[active=true]:text-blue-600 data-[active=true]:bg-blue-50'
          data-active={location.pathname === '/orders'}
        >
          <span className='mr-2'>📋</span> {t('orders')}
        </Link>
      </nav>
    </div>
  )
}

export default Sidebar
