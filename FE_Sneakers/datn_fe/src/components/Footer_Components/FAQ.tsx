import React from 'react'
import { useTranslation } from 'react-i18next'

const FAQ = () => {
  const { t } = useTranslation()

  return (
    <div className='min-h-screen bg-gray-50'>
      {/* Header */}
      <header className='bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg'>
        <div className='max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8'>
          <h1 className='text-4xl font-extrabold text-white tracking-tight'>{t('faq_title')}</h1>
        </div>
      </header>

      {/* Main Content */}
      <main className='max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8'>
        <div className='bg-white shadow-2xl rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl border border-gray-200'>
          <div className='px-6 py-8 sm:p-10'>
            <section className='mb-10'>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('faq_question1')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('faq_answer1')}</p>
            </section>

            <section className='mb-10'>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('faq_question2')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('faq_answer2')}</p>
            </section>

            <section className='mb-10'>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('faq_question3')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('faq_answer3')}</p>
            </section>

            <section className='mb-10'>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('faq_question4')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('faq_answer4')}</p>
            </section>

            <section className='mb-10'>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('faq_question5')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('faq_answer5')}</p>
            </section>

            <section className='mb-10'>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('faq_question6')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed'>{t('faq_answer6')}</p>
            </section>

            <section>
              <h2 className='text-2xl sm:text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('return_policy_section5_title')}
              </h2>
              <p className='text-gray-700 text-lg sm:text-xl leading-relaxed mb-4'>
                {t('return_policy_section5_description')}
              </p>
              <ul className='list-disc pl-6 space-y-3 text-gray-700 text-lg sm:text-xl'>
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section5_item1') }} />
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section5_item2') }} />
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section5_item3') }} />
              </ul>
            </section>
          </div>
        </div>
      </main>
    </div>
  )
}

export default FAQ
