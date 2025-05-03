import React from 'react'
import { useTranslation } from 'react-i18next'

const ReturnPolicy: React.FC = () => {
  const { t } = useTranslation()

  return (
    <div className='min-h-screen bg-gray-100'>
      {/* Header */}
      <header className='bg-white shadow'>
        <div className='max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8'>
          <h1 className='text-3xl font-bold text-gray-900'>{t('return_policy_title')}</h1>
        </div>
      </header>

      {/* Main Content */}
      <main className='max-w-7xl mx-auto py-6 sm:px-6 lg:px-8'>
        <div className='bg-white shadow overflow-hidden sm:rounded-lg'>
          <div className='px-4 py-5 sm:p-6'>
            <section className='mb-8'>
              <h2 className='text-2xl font-semibold text-gray-800 mb-4'>{t('return_policy_section1_title')}</h2>
              <ul className='list-disc pl-5 space-y-2 text-gray-600'>
                <li>{t('return_policy_section1_item1')}</li>
                <li>{t('return_policy_section1_item2')}</li>
                <li>{t('return_policy_section1_item3')}</li>
                <li>{t('return_policy_section1_item4')}</li>
              </ul>
            </section>

            <section className='mb-8'>
              <h2 className='text-2xl font-semibold text-gray-800 mb-4'>{t('return_policy_section2_title')}</h2>
              <ul className='list-disc pl-5 space-y-2 text-gray-600'>
                <li>{t('return_policy_section2_item1')}</li>
                <li>{t('return_policy_section2_item2')}</li>
                <li>{t('return_policy_section2_item3')}</li>
                <li>{t('return_policy_section2_item4')}</li>
              </ul>
            </section>

            <section className='mb-8'>
              <h2 className='text-2xl font-semibold text-gray-800 mb-4'>{t('return_policy_section3_title')}</h2>
              <ol className='list-decimal pl-5 space-y-2 text-gray-600'>
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section3_item1') }} />
                <li>{t('return_policy_section3_item2')}</li>
                <li dangerouslySetInnerHTML={{ __html: t('return_policy_section3_item3') }} />
                <li>{t('return_policy_section3_item4')}</li>
              </ol>
            </section>

            <section className='mb-8'>
              <h2 className='text-2xl font-semibold text-gray-800 mb-4'>{t('return_policy_section4_title')}</h2>
              <ul className='list-disc pl-5 space-y-2 text-gray-600'>
                <li>{t('return_policy_section4_item1')}</li>
                <li>{t('return_policy_section4_item2')}</li>
              </ul>
            </section>

            <section className='mb-8'>
              <h2 className='text-2xl font-semibold text-gray-800 mb-4'>{t('cancel_order')}</h2>
              <p className='text-gray-600 mb-4'>{t('cancellation_warning')}</p>
              <ul className='list-disc pl-5 space-y-2 text-gray-600'>
                <li>{t('cancellation_reason_required')}</li>
                <li>{t('cancellation_reason_min_length')}</li>
                <li>
                  {t('enter_cancellation_reason')}. {t('cancelling')} or {t('pending_cancellation')} will be processed
                  within 1-2 working days.
                </li>
                <li>
                  To {t('confirm_cancel').toLowerCase()}, contact via hotline or email listed in{' '}
                  {t('return_policy_section5_title').toLowerCase()}.
                </li>
              </ul>
            </section>

            <section>
              <h2 className='text-2xl font-semibold text-gray-800 mb-4'>{t('return_policy_section5_title')}</h2>
              <p className='text-gray-600'>{t('return_policy_section5_description')}</p>
              <ul className='list-disc pl-5 space-y-2 text-gray-600 mt-2'>
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
