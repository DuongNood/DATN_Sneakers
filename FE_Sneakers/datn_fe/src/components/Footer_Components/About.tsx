import { useTranslation } from 'react-i18next'

const AboutUs = () => {
  const { t } = useTranslation()

  return (
    <div className='min-h-screen bg-gray-50'>
      {/* Header */}
      <header className='bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg'>
        <div className='max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8'>
          <h1 className='text-4xl font-extrabold text-white tracking-tight text-center'>{t('about_us_title')}</h1>
          <p className='text-lg text-white/80 max-w-2xl mx-auto text-center mt-2'>{t('about_us_subtitle')}</p>
        </div>
      </header>

      {/* Main Content */}
      <main className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12'>
        <section className='grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12'>
          <div className='space-y-4'>
            <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 transition-transform duration-300 hover:translate-x-1'>
              {t('who_we_are')}
            </h2>
            <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('about_us_intro_1')}</p>
            <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('about_us_intro_2')}</p>
          </div>
          <div className='relative'>
            <img
              src='https://tyhisneaker.com/wp-content/uploads/2024/03/giay-adidas-neo-vl-court-20-milk-white-id6016-like-auth-2.jpeg'
              alt={t('about_us_image_alt')}
              className='w-full h-80 object-cover rounded-xl shadow-lg transform hover:scale-105 transition duration-300'
            />
          </div>
        </section>

        <section className='bg-white py-10 px-6 rounded-xl shadow-lg mb-12 transition-all duration-300 hover:shadow-xl'>
          <div className='grid grid-cols-1 md:grid-cols-2 gap-8'>
            <div>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('our_mission')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('mission_description')}</p>
            </div>
            <div>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('our_vision')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('vision_description')}</p>
            </div>
          </div>
        </section>

        <section className='mb-12'>
          <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 text-center mb-6 transition-transform duration-300 hover:translate-x-1'>
            {t('watch_our_story')}
          </h2>
          <div className='flex justify-center'>
            <iframe
              width='100%'
              height='360'
              src='https://www.youtube.com/embed/epcfWIT_Ais?start=35'
              title={t('youtube_video_title')}
              frameBorder='0'
              allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
              allowFullScreen
              className='max-w-3xl w-full rounded-xl shadow-lg'
            ></iframe>
          </div>
        </section>

        <section className='grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12'>
          <div className='relative'>
            <img
              src='https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80'
              alt={t('commitment_image_alt')}
              className='w-full h-80 object-cover rounded-xl shadow-lg transform hover:scale-105 transition duration-300'
            />
          </div>
          <div className='space-y-4'>
            <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 transition-transform duration-300 hover:translate-x-1'>
              {t('our_commitment')}
            </h2>
            <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('commitment_description_1')}</p>
            <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('commitment_description_2')}</p>
          </div>
        </section>

        <section className='text-center'>
          <h3 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
            {t('join_us_title')}
          </h3>
          <p className='text-lg text-gray-600 mb-6 max-w-xl mx-auto'>{t('join_us_description')}</p>
          <a
            href='/'
            className='inline-block bg-blue-600 text-white py-3 px-8 rounded-full font-medium text-lg hover:bg-blue-700 transition duration-300 shadow-lg hover:shadow-xl'
          >
            {t('shop_now')}
          </a>
        </section>
      </main>
    </div>
  )
}

export default AboutUs
