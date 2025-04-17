import React, { useState } from 'react'
import Sidebar from '../components/Sidebar'
import OrderList from '../components/OrderList'
import { useTranslation } from 'react-i18next'

const OrdersPage: React.FC = () => {
  const { t } = useTranslation()
  const [activeTab, setActiveTab] = useState<string>('all')

  const tabs = [
    { key: 'all', label: t('all_orders'), status: '' },
    { key: 'success', label: t('delivered'), status: 'da_giao_hang' },
    { key: 'cancelled', label: t('cancelled'), status: 'huy_don_hang' }
  ]

  console.log('OrdersPage rendered, activeTab:', activeTab)

  return (
    <>
      <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap' rel='stylesheet' />
      <div className='min-h-screen bg-gradient-to-br from-indigo-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8 font-poppins'>
        <div className='max-w-5xl mx-auto mx-6'>
          <div className='grid grid-cols-1 md:grid-cols-3 gap-8'>
            <Sidebar />
            <div className='md:col-span-2 bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl'>
              <h2 className='text-3xl font-semibold text-gray-900 mb-8'>{t('my_orders')}</h2>
              <div className='flex gap-6 border-b mb-8'>
                {tabs.map((tab) => (
                  <button
                    key={tab.key}
                    onClick={() => setActiveTab(tab.key)}
                    className={`px-6 py-3 text-base font-medium transition-all duration-200 ${
                      activeTab === tab.key
                        ? 'border-b-4 border-blue-600 text-blue-600'
                        : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-t-lg'
                    }`}
                  >
                    {tab.label}
                  </button>
                ))}
              </div>
              <OrderList statusFilter={tabs.find((tab) => tab.key === activeTab)?.status || ''} />
            </div>
          </div>
        </div>
      </div>
    </>
  )
}

export default OrdersPage
