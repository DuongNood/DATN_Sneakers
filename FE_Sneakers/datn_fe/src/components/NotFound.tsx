import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'

const NotFound = () => {
  const { t } = useTranslation()

  return (
    <div className='flex items-center justify-center h-[300px] bg-gray-100'>
      <div className='text-center'>
        <h1 className='text-4xl font-bold text-red-600'>404</h1>
        <p className='text-lg text-gray-700'>{t('not_found_message')}</p>
        <Link to='/' className='mt-4 inline-block text-blue-500 hover:underline'>
          {t('back_to_home')}
        </Link>
      </div>
    </div>
  )
}

export default NotFound
