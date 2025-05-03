import React from 'react'
import { useTranslation } from 'react-i18next'

const ReturnPolicy = () => {
  const { t } = useTranslation()

  return (
    <div className='min-h-screen bg-gray-50'>
      {/* Header */}
      <header className='bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg'>
        <div className='max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8'>
          <h1 className='text-4xl font-extrabold text-white tracking-tight'>{t('return_policy_title')}</h1>
        </div>
      </header>

      {/* Main Content */}
      <main className='max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8'>
        <div className='bg-white shadow-2xl rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl'>
          <div className='px-6 py-8 sm:p-10'>
            <section className='mb-10'>
              <h2 className='text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('return_policy_section1_title')}
              </h2>
              <ul className='list-disc pl-6 space-y-3 text-gray-700 text-lg'>
                <li>{t('return_policy_section1_item1')}</li>
                <li>{t('return_policy_section1_item2')}</li>
                <li>{t('return_policy_section1_item3')}</li>
                <li>{t('return_policy_section1_item4')}</li>
              </ul>
            </section>

            <section className='mb-10'>
              <h2 className='text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('return_policy_section2_title')}
              </h2>
              <ul className='list-disc pl-6 space-y-3 text-gray-700 text-lg'>
                <li>{t('return_policy_section2_item1')}</li>
                <li>{t('return_policy_section2_item2')}</li>
                <li>{t('return_policy_section2_item3')}</li>
                <li>{t('return_policy_section2_item4')}</li>
              </ul>
            </section>

            <section className='mb-10'>
              <h2 className='text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('return_policy_section3_title')}
              </h2>
              <ol className='list-decimal pl-6 space-y-3 text-gray-700 text-lg'>
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section3_item1') }} />
                <li>{t('return_policy_section3_item2')}</li>
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section3_item3') }} />
                <li>{t('return_policy_section3_item4')}</li>
              </ol>
            </section>

            <section className='mb-10'>
              <h2 className='text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('return_policy_section4_title')}
              </h2>
              <ul className='list-disc pl-6 space-y-3 text-gray-700 text-lg'>
                <li>{t('return_policy_section4_item1')}</li>
                <li>{t('return_policy_section4_item2')}</li>
              </ul>
            </section>

            <section className='mb-10'>
              <h2 className='text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('cancel_order')}
              </h2>
              <p className='text-gray-700 text-lg mb-4'>{t('cancellation_warning')}</p>
              <ul className='list-disc pl-6 space-y-3 text-gray-700 text-lg'>
                <li>{t('cancellation_reason_required')}</li>
                <li>{t('cancellation_reason_min_length')}</li>
                <li>
                  {t('enter_cancellation_reason')}. {t('cancelling')} hoặc {t('pending_cancellation')} sẽ được xử lý
                  trong vòng 1-2 ngày làm việc.
                </li>
                <li>
                  Để {t('confirm_cancel').toLowerCase()}, liên hệ qua hotline hoặc email được liệt kê trong{' '}
                  {t('return_policy_section5_title').toLowerCase()}.
                </li>
              </ul>
            </section>

            <section>
              <h2 className='text-3xl font-bold text-gray-900 mb-4 transition-transform duration-300 hover:translate-x-1'>
                {t('return_policy_section5_title')}
              </h2>
              <p className='text-gray-700 text-lg'>{t('return_policy_section5_description')}</p>
              <ul className='list-disc pl-6 space-y-3 text-gray-700 text-lg mt-4'>
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

export default ReturnPolicy
