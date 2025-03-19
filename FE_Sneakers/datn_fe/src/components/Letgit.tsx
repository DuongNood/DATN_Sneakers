import { useLocation } from 'react-router-dom'

const Legit = () => {
  const location = useLocation()

  if (location.pathname !== '/') {
    return null 
  }

  return (
    <div className='bg-gray-100 py-8'>
      <div className='max-w-screen-xl mx-auto px-4'>
        <div className='grid grid-cols-1 sm:grid-cols-3 gap-8'>
          <div className='flex flex-col items-center text-center'>
            <img src='https://img.icons8.com/ios/50/000000/checked.png' alt='Chính Hãng' className='mb-3' />
            <h3 className='font-semibold text-lg text-blue-500 uppercase'> Cam Kết Chính Hãng</h3>
            <p className='text-gray-900 font-bold'>100 % Authentic</p>
            <p className='text-gray-600'>Cam kết sản phẩm chính hãng 100%</p>
          </div>

          <div className='flex flex-col items-center text-center'>
            <img src='https://img.icons8.com/ios/50/000000/shipped.png' alt='Giao hàng hỏa tốc' className='mb-3' />
            <h3 className='font-semibold text-lg text-blue-500 uppercase'>Giao Hàng Hỏa Tốc</h3>
            <p className='text-gray-900 font-bold'>Express delivery</p>
            <p className='text-gray-600'>Nhanh chóng, giao tận tay trong 2h</p>
          </div>

          <div className='flex flex-col items-center text-center'>
            <img src='https://img.icons8.com/ios/50/000000/available-updates.png' alt='Hỗ trợ 24/7' className='mb-3' />
            <h3 className='font-semibold text-lg text-blue-500 uppercase'>Hỗ Trợ 24/7</h3>
            <p className='text-gray-900 font-bold'>Supporting 24/24</p>
            <p className='text-gray-600'>Dịch vụ khách hàng luôn sẵn sàng</p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Legit
