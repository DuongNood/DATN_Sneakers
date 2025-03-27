import { useTranslation } from 'react-i18next'

const AboutUs = () => {
  const { t } = useTranslation()

  return (
    <div className='max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-gray-50'>
      <section className='text-center mb-12'>
        <h1 className='text-2xl md:text-3xl font-bold text-gray-900 mb-3'>{t('about_us_title')}</h1>
        <p className='text-base text-gray-600 max-w-2xl mx-auto'>{t('about_us_subtitle')}</p>
      </section>

      <section className='grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12'>
        <div className='space-y-4'>
          <h2 className='text-xl md:text-2xl font-semibold text-gray-800'>{t('who_we_are')}</h2>
          <p className='text-gray-700 leading-relaxed'>{t('about_us_intro_1')}</p>
          <p className='text-gray-700 leading-relaxed'>{t('about_us_intro_2')}</p>
        </div>
        <div className='relative'>
          <img
            src='https://tyhisneaker.com/wp-content/uploads/2024/03/giay-adidas-neo-vl-court-20-milk-white-id6016-like-auth-2.jpeg'
            alt={t('about_us_image_alt')}
            className='w-full h-80 object-cover rounded-lg shadow-md transform hover:scale-105 transition duration-300'
          />
        </div>
      </section>

      <section className='bg-white py-10 px-6 rounded-lg shadow-md mb-12'>
        <div className='grid grid-cols-1 md:grid-cols-2 gap-8'>
          <div>
            <h2 className='text-xl md:text-2xl font-semibold text-gray-800 mb-3'>{t('our_mission')}</h2>
            <p className='text-gray-700 leading-relaxed'>{t('mission_description')}</p>
          </div>
          <div>
            <h2 className='text-xl md:text-2xl font-semibold text-gray-800 mb-3'>{t('our_vision')}</h2>
            <p className='text-gray-700 leading-relaxed'>{t('vision_description')}</p>
          </div>
        </div>
      </section>

      <section className='mb-12'>
        <h2 className='text-xl md:text-2xl font-semibold text-gray-800 text-center mb-6'>{t('watch_our_story')}</h2>
        <div className='flex justify-center'>
          <iframe
            width='100%'
            height='360'
            src='https://www.youtube.com/embed/epcfWIT_Ais?start=35'
            title={t('youtube_video_title')}
            frameBorder='0'
            allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
            allowFullScreen
            className='max-w-3xl rounded-lg shadow-md'
          ></iframe>
        </div>
      </section>

      <section className='grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12'>
        <div className='relative'>
          <img
            src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80'
            alt={t('commitment_image_alt')}
            className='w-full h-80 object-cover rounded-lg shadow-md transform hover:scale-105 transition duration-300'
          />
        </div>
        <div className='space-y-4'>
          <h2 className='text-xl md:text-2xl font-semibold text-gray-800'>{t('our_commitment')}</h2>
          <p className='text-gray-700 leading-relaxed'>{t('commitment_description_1')}</p>
          <p className='text-gray-700 leading-relaxed'>{t('commitment_description_2')}</p>
        </div>
      </section>

      <section className='text-center'>
        <h3 className='text-xl md:text-2xl font-semibold text-gray-800 mb-4'>{t('join_us_title')}</h3>
        <p className='text-base text-gray-600 mb-6 max-w-xl mx-auto'>{t('join_us_description')}</p>
        <a
          href='/'
          className='inline-block bg-blue-600 text-white py-3 px-8 rounded-full font-medium text-base hover:bg-blue-700 transition duration-300 shadow-md'
        >
          {t('shop_now')}
        </a>
      </section>
    </div>
  )
}

export default AboutUs
