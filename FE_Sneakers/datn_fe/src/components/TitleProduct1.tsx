import { useLocation } from 'react-router-dom'

const TitleWithEffect1 = () => {
  const location = useLocation()

  if (location.pathname !== '/') {
    return null
  }

  return (
    <div className='container mx-auto px-4 sm:px-8 md:px-16'>
      <div className='relative flex items-center my-12'>
        <h2 className='text-5xl sm:text-6xl md:text-7xl font-bold text-gray-200 absolute z-10 opacity-70 uppercase animate-blink-glow max-w-full'>
          Hot hit
        </h2>

        <h2 className='text-2xl sm:text-2xl md:text-5xl lg:text-4xl xl:text-3xl font-bold text-gray-900 bg-clip-text relative z-20 max-w-full uppercase'>
          -Sản Phẩm Nổi Bật
        </h2>
      </div>
    </div>
  )
}

export default TitleWithEffect1
