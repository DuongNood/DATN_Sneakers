import React from 'react'
import { useNavigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { FaCheckCircle } from 'react-icons/fa'

const OrderSuccess: React.FC = () => {
  const { t } = useTranslation()
  const navigate = useNavigate()

  const handleBackToHome = () => {
    navigate('/')
  }

  return (
    <div className=' h-[400px] flex items-center justify-center'>
      <div className='bg-white p-6 rounded-lg  max-w-md w-full text-center'>
        <FaCheckCircle className='text-green-500 text-5xl mx-auto mb-4' />
        <h1 className='text-2xl font-semibold text-gray-800 mb-4'>{t('order_success_title')}</h1>
        <p className='text-gray-600 mb-6'>{t('order_success_message')}</p>
        <button
          onClick={handleBackToHome}
          className='w-full py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition'
        >
          {t('back_to_home')}
        </button>
      </div>
    </div>
  )
}

export default OrderSuccess
