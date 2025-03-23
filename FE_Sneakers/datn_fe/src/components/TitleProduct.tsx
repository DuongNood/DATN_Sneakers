const TitleWithEffect = () => {
  return (
    <div className='container mx-auto px-4 sm:px-8 md:px-16'>
      <div className='relative flex items-center my-12'>
        <h2 className='text-5xl sm:text-6xl md:text-5xl font-bold text-gray-200 absolute z-10 opacity-70 uppercase animate-blink-glow max-w-full'>
          Product
        </h2>

        <h2 className='text-2xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-2xl font-bold text-gray-900 bg-clip-text relative z-20 max-w-full uppercase'>
          -Sản Phẩm Mới
        </h2>
      </div>
    </div>
  )
}

export default TitleWithEffect
