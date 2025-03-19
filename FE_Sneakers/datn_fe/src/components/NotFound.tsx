import { Link } from 'react-router-dom'

const NotFound = () => {
  return (
    <div className='flex items-center justify-center h-[300px] bg-gray-100'>
      <div className='text-center'>
        <h1 className='text-4xl font-bold text-red-600'>404</h1>
        <p className='text-lg text-gray-700'>Lỗi! Trang bạn tìm kiếm không tồn tại.</p>
        <Link to='/' className='mt-4 inline-block text-blue-500 hover:underline'>
          Về Trang Chủ
        </Link>
      </div>
    </div>
  )
}

export default NotFound
