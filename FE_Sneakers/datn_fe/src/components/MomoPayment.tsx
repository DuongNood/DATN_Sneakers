import React, { useState } from 'react'
import axios from 'axios'

interface MomoResponse {
  payUrl?: string
  [key: string]: any
}

const MomoPayment: React.FC = () => {
  const [amount, setAmount] = useState<string>('')

  const handlePayment = async (e: React.FormEvent) => {
    e.preventDefault()
    try {
      const response = await axios.post<MomoResponse>('http://localhost:8000/api/momo/create', { amount })
      if (response.data.payUrl) {
        window.location.href = response.data.payUrl
      } else {
        console.error('No payUrl received')
      }
    } catch (error) {
      console.error('Payment error:', error)
    }
  }

  return (
    <div className='flex justify-center items-center h-screen bg-gray-100'>
      <form onSubmit={handlePayment} className='bg-white p-6 rounded shadow-md w-96'>
        <h2 className='text-2xl font-bold mb-4 text-center'>Thanh toán MoMo</h2>
        <div className='mb-4'>
          <label className='block text-gray-700'>Số tiền (VND)</label>
          <input
            type='number'
            value={amount}
            onChange={(e) => setAmount(e.target.value)}
            className='w-full p-2 border rounded'
            required
          />
        </div>
        <button type='submit' className='w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600'>
          Thanh toán
        </button>
      </form>
    </div>
  )
}

export default MomoPayment
