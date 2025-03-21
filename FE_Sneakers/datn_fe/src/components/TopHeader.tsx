import { useState } from 'react'

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false)

  const toggleDropdown = () => {
    setIsOpen(!isOpen)
  }

  const changeLanguage = (lang) => {
    if (lang === 'vi') {
      alert('Đã chuyển sang Tiếng Việt!')
    } else if (lang === 'en') {
      alert('Switched to English!')
    }
    setIsOpen(false)
  }

  return (
    <div className='flex justify-end items-center bg-white-100 p-2 border-b border-gray-300 relative z-[1000]'>
      <div className='flex items-center mr-6'>
        <span className='text-lg mr-2'>👑</span>
        <span className='text-sm text-gray-700 '>Pole Sneakers</span>
      </div>

      <div className='relative mr-6'>
        <button
          onClick={toggleDropdown}
          className='bg-blue-600 text-white px-4 py-1 rounded-md text-sm hover:bg-blue-700 focus:outline-none flex items-center'
        >
          <span className='mr-2'>🌐</span>
          <span>Ngôn ngữ</span>
        </button>
        {isOpen && (
          <div className='absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg z-[2000]'>
            <button
              onClick={() => changeLanguage('vi')}
              className='block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100'
            >
              Tiếng Việt
            </button>
            <button
              onClick={() => changeLanguage('en')}
              className='block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100'
            >
              Tiếng Anh
            </button>
          </div>
        )}
      </div>

      <div className='flex items-center'>
        <span className='text-lg mr-2'>📞</span>
        <span className='text-sm text-gray-700'>Liên Hệ: 0399922999</span>
      </div>
    </div>
  )
}

export default Navbar
