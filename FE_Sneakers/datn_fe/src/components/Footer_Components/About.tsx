import { useTranslation } from 'react-i18next'

const AboutUs = () => {
  const { t } = useTranslation()

  return (
    <div className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-gray-50'>
      <section className='text-center mb-16'>
        <h1 className='text-3xl md:text-4xl lg:text-3xl font-extrabold text-gray-900 mb-4'>{t('about_us_title')}</h1>
        <p className='text-lg text-gray-600 max-w-3xl mx-auto'>{t('about_us_subtitle')}</p>
      </section>

      <section className='grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16'>
        <div className='space-y-6'>
          <h2 className='text-2xl md:text-3xl font-semibold text-gray-800'>{t('who_we_are')}</h2>
          <p className='text-gray-700 leading-relaxed'>{t('about_us_intro_1')}</p>
          <p className='text-gray-700 leading-relaxed'>{t('about_us_intro_2')}</p>
        </div>
        <div className='relative'>
          <img
            src='https://tyhisneaker.com/wp-content/uploads/2024/03/giay-adidas-neo-vl-court-20-milk-white-id6016-like-auth-2.jpeg'
            alt={t('about_us_image_alt')}
            className='w-full h-96 object-cover rounded-xl shadow-xl transform hover:scale-105 transition duration-300'
          />
          <div className='absolute inset-0 bg-gradient-to-t from-gray-900/30 to-transparent rounded-xl'></div>
        </div>
      </section>

      <section className='bg-white py-12 px-8 rounded-xl shadow-lg mb-16'>
        <div className='grid grid-cols-1 md:grid-cols-2 gap-10'>
          <div>
            <h2 className='text-2xl md:text-3xl font-semibold text-gray-800 mb-4'>{t('our_mission')}</h2>
            <p className='text-gray-700 leading-relaxed'>{t('mission_description')}</p>
          </div>
          <div>
            <h2 className='text-2xl md:text-3xl font-semibold text-gray-800 mb-4'>{t('our_vision')}</h2>
            <p className='text-gray-700 leading-relaxed'>{t('vision_description')}</p>
          </div>
        </div>
      </section>

      <section className='mb-16'>
        <h2 className='text-2xl md:text-3xl font-semibold text-gray-800 text-center mb-8'>{t('watch_our_story')}</h2>
        <div className='flex justify-center'>
          <iframe
            width='100%'
            height='400'
            src='https://www.youtube.com/embed/epcfWIT_Ais?start=35'
            title={t('youtube_video_title')}
            frameBorder='0'
            allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
            allowFullScreen
            className='max-w-4xl rounded-xl shadow-lg'
          ></iframe>
        </div>
      </section>

      <section className='grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16'>
        <div className='relative'>
          <img
            src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80'
            alt={t('commitment_image_alt')}
            className='w-full h-96 object-cover rounded-xl shadow-xl transform hover:scale-105 transition duration-300'
          />
          <div className='absolute inset-0 bg-gradient-to-t from-gray-900/30 to-transparent rounded-xl'></div>
        </div>
        <div className='space-y-6'>
          <h2 className='text-2xl md:text-3xl font-semibold text-gray-800'>{t('our_commitment')}</h2>
          <p className='text-gray-700 leading-relaxed'>{t('commitment_description_1')}</p>
          <p className='text-gray-700 leading-relaxed'>{t('commitment_description_2')}</p>
        </div>
      </section>

      <section className='text-center'>
        <h3 className='text-2xl md:text-3xl font-semibold text-gray-800 mb-6'>{t('join_us_title')}</h3>
        <p className='text-lg text-gray-600 mb-8 max-w-2xl mx-auto'>{t('join_us_description')}</p>
        <a
          href='/'
          className='inline-block bg-blue-600 text-white py-4 px-10 rounded-full font-semibold text-lg hover:bg-blue-700 transition duration-300 shadow-lg hover:shadow-xl'
        >
          {t('shop_now')}
        </a>
      </section>
    </div>
  )
}

export default AboutUs
