import { useLocation } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

const Legit = () => {
  const { t } = useTranslation() 
  const location = useLocation()

  if (location.pathname !== '/') {
    return null
  }

  return (
    <div className='bg-gray-100 py-8'>
      <div className='max-w-screen-xl mx-auto px-4'>
        <div className='grid grid-cols-1 sm:grid-cols-3 gap-8'>
          <div className='flex flex-col items-center text-center'>
            <img
              src='https://img.icons8.com/ios/50/000000/checked.png'
              alt={t('authentic_commitment')}
              className='mb-3'
            />
            <h3 className='font-semibold text-lg text-blue-500 uppercase'>{t('authentic_commitment')}</h3>
            <p className='text-gray-900 font-bold'>{t('authentic_100')}</p>
            <p className='text-gray-600'>{t('authentic_description')}</p>
          </div>

          <div className='flex flex-col items-center text-center'>
            <img src='https://img.icons8.com/ios/50/000000/shipped.png' alt={t('express_delivery')} className='mb-3' />
            <h3 className='font-semibold text-lg text-blue-500 uppercase'>{t('express_delivery')}</h3>
            <p className='text-gray-900 font-bold'>{t('delivery_express')}</p>
            <p className='text-gray-600'>{t('delivery_description')}</p>
          </div>

          <div className='flex flex-col items-center text-center'>
            <img
              src='https://img.icons8.com/ios/50/000000/available-updates.png'
              alt={t('support_24_7')}
              className='mb-3'
            />
            <h3 className='font-semibold text-lg text-blue-500 uppercase'>{t('support_24_7')}</h3>
            <p className='text-gray-900 font-bold'>{t('support_24_24')}</p>
            <p className='text-gray-600'>{t('support_description')}</p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Legit
