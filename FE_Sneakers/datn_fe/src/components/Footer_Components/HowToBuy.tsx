import React from 'react'
import { useTranslation } from 'react-i18next'

export default function HowToBuy() {
  const { t } = useTranslation()

  return (
    <div className='min-h-screen bg-gray-100 py-6 px-4 sm:px-6'>
      <div className='bg-white rounded-lg shadow-md p-6 sm:p-8 max-w-4xl mx-auto w-full'>
        <h1 className='text-3xl font-bold text-gray-900 mb-6 text-center'>{t('how_to_buy_title')}</h1>
        <p className='text-gray-600 text-lg leading-relaxed text-center mb-8'>{t('how_to_buy_intro')}</p>

        <div className='space-y-6'>
          {[
            {
              title: t('how_to_buy_section1_title'),
              content: t('how_to_buy_section1_content')
            },
            {
              title: t('how_to_buy_section2_title'),
              content: t('how_to_buy_section2_content')
            },
            {
              title: t('how_to_buy_section3_title'),
              content: t('how_to_buy_section3_content')
            },
            {
              title: t('how_to_buy_section4_title'),
              content: t('how_to_buy_section4_content')
            }
          ].map((item, index) => (
            <div key={index} className='p-4 bg-gray-50 rounded-md border border-gray-200'>
              <h2 className='text-xl font-semibold text-gray-900 mb-2'>{item.title}</h2>
              <p className='text-gray-600 text-base' dangerouslySetInnerHTML={{ __html: item.content }} />
            </div>
          ))}
        </div>

        {/* Contact Section */}
        <div className='mt-8 p-4 bg-gray-50 rounded-md border border-gray-200'>
          <h2 className='text-xl font-semibold text-gray-900 mb-4'>{t('return_policy_section5_title')}</h2>
          <p className='text-gray-600 mb-4'>{t('return_policy_section5_description')}</p>
          <ul className='list-disc pl-5 space-y-2 text-gray-600'>
            <li dangerouslySetInnerHTML={{ __html: t('return_policy_section5_item1') }} />
            <li dangerouslySetInnerHTML={{ __html: t('return_policy_section5_item2') }} />
            <li dangerouslySetInnerHTML={{ __html: t('return_policy_section5_item3') }} />
          </ul>
        </div>
      </div>
    </div>
  )
}
