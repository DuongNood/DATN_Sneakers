import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

const ChangePasswordPage = () => {
  const { t } = useTranslation() 

  return (
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6'>
     
      <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
        <nav className='space-y-2'>
          <Link to='/profile' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>👤</span> {t('my_account')}
          </Link>
          <Link to='/change-password' className='flex items-center p-2 text-blue-600 bg-blue-50 rounded-md'>
            <span className='mr-2'>🔒</span> {t('change_password')}
          </Link>
          <Link to='/orders' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>📋</span> {t('orders')}
          </Link>
        </nav>
      </div>

     
      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <h2 className='text-xl font-semibold mb-6'>{t('change_password')}</h2>

        <div className='space-y-6'>
        
          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('old_password')}</label>
            <input
              type='password'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('old_password_placeholder')}
              value=''
              readOnly
            />
          </div>

      
          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('new_password')}</label>
            <input
              type='password'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('new_password_placeholder')}
              value=''
              readOnly
            />
          </div>

        
          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('confirm_new_password')}</label>
            <input
              type='password'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('confirm_new_password_placeholder')}
              value=''
              readOnly
            />
          </div>

        
          <button className='w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition'>
            {t('save_changes')}
          </button>

         
          <div className='text-center'>
            <Link to='/forgot-password' className='text-blue-600 hover:underline text-sm'>
              {t('forgot_password')}
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ChangePasswordPage
