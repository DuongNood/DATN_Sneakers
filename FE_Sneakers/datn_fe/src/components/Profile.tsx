import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'

const ProfilePage = () => {
  const [userData, setUserData] = useState(null)

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const response = await fetch('http://127.0.0.1:8000/api/login')
        const data = await response.json()
        setUserData(data)
      } catch (error) {
        console.error('Lỗi khi lấy dữ liệu người dùng:', error)
      }
    }

    fetchUserData()
  }, [])

  return (
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6'>
      <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
        <nav className='space-y-2'>
          <Link to='/profile' className='flex items-center p-2 text-blue-600 bg-blue-50 rounded-md'>
            <span className='mr-2'>👤</span> Tài khoản của tôi
          </Link>
          <Link to='/change-password' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>🔒</span> Đổi mật khẩu
          </Link>
          <Link to='/orders' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>📋</span> Đơn mua
          </Link>
        </nav>
      </div>

      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <h2 className='text-xl font-semibold mb-6'>Tài khoản của tôi</h2>

        <div className='space-y-6'>
          <div className='flex flex-col items-center'>
            <img src='' alt='Avatar' className='w-32 h-32 rounded-full object-cover mb-4 border-2 border-gray-300' />
            <input
              type='file'
              accept='.jpg,.jpeg,.png'
              className='text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100'
            />

            <div className='mt-3 text-gray-400'>Dung lượng tối đa là 1Mb</div>
            <div className='mt-2 text-gray-400'>Định dạng: .JPG,.JPEG,.PNG</div>
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>Họ tên</label>

            <input
              type='text'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder='Nhập họ tên'
              value=''
              readOnly
            />
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>Địa chỉ</label>
            <input
              type='text'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder='Nhập quê quán'
              value='' 
              readOnly
            />
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>Số điện thoại</label>
            <input
              type='text'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder='Nhập số điện thoại'
              value='' 
              readOnly
            />
          </div>

         
          <button className='w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition'>
            Lưu thay đổi
          </button>
        </div>
      </div>
    </div>
  )
}

export default ProfilePage
