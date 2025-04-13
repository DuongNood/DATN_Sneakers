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
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6'>
      <Sidebar />
      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <h2 className='text-xl font-semibold mb-6'>{t('my_orders')}</h2>
        <div className='flex gap-4 border-b mb-6'>
          {tabs.map((tab) => (
            <button
              key={tab.key}
              onClick={() => setActiveTab(tab.key)}
              className={`px-4 py-2 font-medium ${
                activeTab === tab.key ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>
        <OrderList statusFilter={tabs.find((tab) => tab.key === activeTab)?.status || ''} />
      </div>
    </div>
  )
}

export default OrdersPage
