import { Link } from 'react-router-dom'

const ChangePasswordPage = () => {
  return (
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6'>
      {/* Menu dọc bên trái */}
      <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
        <nav className='space-y-2'>
          <Link to='/profile' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>👤</span> Tài khoản của tôi
          </Link>
          <Link to='/change-password' className='flex items-center p-2 text-blue-600 bg-blue-50 rounded-md'>
            <span className='mr-2'>🔒</span> Đổi mật khẩu
          </Link>
          <Link to='/orders' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>📋</span> Đơn mua
          </Link>
        </nav>
      </div>

      {/* Nội dung bên phải: Đổi mật khẩu */}
      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <h2 className='text-xl font-semibold mb-6'>Đổi mật khẩu</h2>

        <div className='space-y-6'>
          {/* Mật khẩu cũ */}
          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>Mật khẩu cũ</label>
            <input
              type='password'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder='Nhập mật khẩu cũ'
              value=''
              readOnly
            />
          </div>

          {/* Mật khẩu mới */}
          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>Mật khẩu mới</label>
            <input
              type='password'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder='Nhập mật khẩu mới'
              value=''
              readOnly
            />
          </div>

          {/* Xác nhận mật khẩu mới */}
          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>Xác nhận mật khẩu mới</label>
            <input
              type='password'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder='Xác nhận mật khẩu mới'
              value=''
              readOnly
            />
          </div>

          {/* Nút lưu */}
          <button className='w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition'>
            Lưu thay đổi
          </button>

          {/* Quên mật khẩu */}
          <div className='text-center'>
            <Link to='/forgot-password' className='text-blue-600 hover:underline text-sm'>
              Quên mật khẩu?
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}

export default ChangePasswordPage
