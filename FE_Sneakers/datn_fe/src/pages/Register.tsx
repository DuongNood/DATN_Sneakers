import { Link } from 'react-router-dom'

const Register = () => {
  return (
    <div className='flex justify-center items-center min-h-screen bg-gray-100 px-4'>
      <div className='w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white shadow-lg rounded-lg p-6 sm:p-8'>
        <h2 className='text-2xl font-semibold text-gray-800 mb-6 text-center'>Đăng Ký</h2>

        <form className='space-y-5'>
          <div>
            <label className='block text-sm font-medium text-gray-700'>Họ và Tên</label>
            <input
              type='text'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none'
              placeholder='Nhập họ và tên'
            />
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700'>Email</label>
            <input
              type='email'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none'
              placeholder='Nhập email của bạn'
            />
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700'>Mật khẩu</label>
            <input
              type='password'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none'
              placeholder='Nhập mật khẩu'
            />
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700'>Nhập lại mật khẩu</label>
            <input
              type='password'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none'
              placeholder='Nhập lại mật khẩu'
            />
          </div>

          <button
            type='submit'
            className='w-full bg-blue-500 text-white py-3 rounded-md font-semibold hover:bg-blue-600 transition'
          >
            Đăng Ký
          </button>
        </form>

        <p className='text-gray-600 mt-6 text-center text-sm '>
          Đã có tài khoản?{' '}
          <Link to='/login' className='text-blue-500 hover:underline'>
            Đăng nhập
          </Link>
        </p>
      </div>
    </div>
  )
}

export default Register
