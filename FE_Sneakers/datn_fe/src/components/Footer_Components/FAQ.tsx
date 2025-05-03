import React from 'react'
import { useTranslation } from 'react-i18next'

const FAQ: React.FC = () => {
  const { t } = useTranslation()

  return (
    <div className='min-h-screen bg-gray-100'>
      {/* Header */}
      <header className='bg-white shadow'>
        <div className='max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8'>
          <h1 className='text-3xl font-bold text-gray-900'>{t('faq_title')}</h1>
        </div>
      </header>

      {/* Main Content */}
      <main className='max-w-7xl mx-auto py-6 sm:px-6 lg:px-8'>
        <div className='bg-white shadow overflow-hidden sm:rounded-lg'>
          <div className='px-4 py-5 sm:p-6'>
            <section className='mb-8'>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('faq_question1')}</h2>
              <p className='text-gray-600'>{t('faq_answer1')}</p>
            </section>

            <section className='mb-8'>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('faq_question2')}</h2>
              <p className='text-gray-600'>{t('faq_answer2')}</p>
            </section>

            <section className='mb-8'>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('faq_question3')}</h2>
              <p className='text-gray-600'>{t('faq_answer3')}</p>
            </section>

            <section className='mb-8'>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('faq_question4')}</h2>
              <p className='text-gray-600'>{t('faq_answer4')}</p>
            </section>

            <section className='mb-8'>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('faq_question5')}</h2>
              <p className='text-gray-600'>{t('faq_answer5')}</p>
            </section>

            <section className='mb-8'>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('faq_question6')}</h2>
              <p className='text-gray-600'>{t('faq_answer6')}</p>
            </section>

            <section>
              <h2 className='text-xl font-semibold text-gray-800 mb-4'>{t('return_policy_section5_title')}</h2>
              <p className='text-gray-600 mb-4'>{t('return_policy_section5_description')}</p>
              <ul className='list-disc pl-5 space-y-2 text-gray-600'>
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
