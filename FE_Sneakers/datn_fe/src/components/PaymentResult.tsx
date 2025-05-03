import React, { useEffect, useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'
import { useTranslation } from 'react-i18next'
import axios from 'axios'

const PaymentResult: React.FC = () => {
  const { t } = useTranslation()
  const location = useLocation()
  const navigate = useNavigate()
  const [loading, setLoading] = useState(true)
  const [result, setResult] = useState<{ message: string; transaction_status: string } | null>(null)

  useEffect(() => {
    const fetchResult = async () => {
      try {
        const response = await axios.get(`http://localhost:8000/api/vnpay/return${location.search}`)
        setResult(response.data)
        if (response.data.transaction_status === 'success') {
          toast.success(t('payment_success'), { autoClose: 2000 })
        } else {
          toast.error(response.data.message, { autoClose: 2000 })
        }
      } catch (error: any) {
        toast.error(t('payment_failed'), { autoClose: 2000 })
      } finally {
        setLoading(false)
      }
    }

    fetchResult()
  }, [location, t])

  if (loading) {
    return (
      <div className='min-h-screen bg-gradient-to-br from-indigo-50 to-gray-100 flex items-center justify-center'>
        <div className='text-lg font-semibold text-gray-900'>{t('processing')}</div>
      </div>
    )
  }

  return (
    <div className='min-h-screen bg-gradient-to-br from-indigo-50 to-gray-100 flex items-center justify-center'>
      <div className='bg-white p-8 rounded-2xl shadow-xl text-center'>
        <h2 className='text-2xl font-semibold text-gray-900 mb-4'>
          {result?.transaction_status === 'success' ? t('payment_success') : t('payment_failed')}
        </h2>
        <p className='text-gray-600 mb-6'>{result?.message}</p>
        <button
          onClick={() => navigate('/')}
          className='py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700'
        >
          {t('back_to_home')}
        </button>
      </div>
    </div>
  )
}

export default PaymentResult
