import { Link } from 'react-router-dom'
import { toast } from 'react-toastify'

const Footer = () => {
  const handleSubmit = (event) => {
    event.preventDefault()
    const email = event.target.elements.email.value

    const formData = new URLSearchParams()
    formData.append('entry.873095472', email)

    fetch('https://docs.google.com/forms/d/e/1FAIpQLScPO7qu7vfekGcxPL2J3hgwU7XB3QQIfKW7y0hj0rPBbzG2Cw/formResponse', {
      method: 'POST',
      body: formData,
      mode: 'no-cors'
    })
      .then(() => {
        toast.success('Đăng ký nhận tin thành công!', {
          autoClose: 1000
        })
      })
      .catch((error) => {
        console.error('Lỗi gửi dữ liệu:', error)
      })

    event.target.reset()
  }
  return (
    <footer className='bg-gray-900 text-gray-300 py-12'>
      <div className='container mx-auto px-6 md:px-12'>
        <div className='grid grid-cols-1 md:grid-cols-4 gap-8'>
          <div>
            <h2 className='text-2xl font-bold text-white'>Sneakers</h2>
            <p className='mt-3 text-sm'>Chuyên cung cấp giày sneakers chính hãng, mẫu mã đa dạng.</p>
          </div>

          <div>
            <h3 className='text-lg font-semibold text-white mb-4'>Về Chúng Tôi</h3>
            <ul className='space-y-3'>
              <li>
                <Link to='/about' className='hover:text-white transition'>
                  Giới thiệu
                </Link>
              </li>
              <li>
                <Link to='/contact' className='hover:text-white transition'>
                  Liên hệ
                </Link>
              </li>
              <li>
                <Link to='/privacy-policy' className='hover:text-white transition'>
                  Chính sách bảo mật
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className='text-lg font-semibold text-white mb-4'>Hỗ Trợ</h3>
            <ul className='space-y-3'>
              <li>
                <Link to='/faq' className='hover:text-white transition'>
                  Câu hỏi thường gặp
                </Link>
              </li>
              <li>
                <Link to='/return-policy' className='hover:text-white transition'>
                  Chính sách đổi trả
                </Link>
              </li>
              <li>
                <Link to='/how-to-buy' className='hover:text-white transition'>
                  Hướng dẫn mua hàng
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className='text-lg font-semibold text-white mb-4'>Đăng ký nhận tin</h3>
            <p className='text-sm mb-3'>Nhận thông tin mới nhất về sản phẩm & khuyến mãi.</p>
            <div className='flex'>
              <form className='flex' onSubmit={handleSubmit}>
                <input
                  type='email'
                  name='email'
                  required
                  placeholder='Nhập email...'
                  className='w-full p-2 text-gray-900 rounded-l-md focus:outline-none'
                />
                <button
                  type='submit'
                  className='bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-r-md transition'
                >
                  Gửi
                </button>
              </form>
            </div>
          </div>
        </div>

        <div className='border-t border-gray-700 my-6'></div>

        <div className='flex flex-col md:flex-row items-center justify-between'>
          <p className='text-sm'>© {new Date().getFullYear()} Sneakers. All rights reserved.</p>
          <div className='flex space-x-4 mt-4 md:mt-0'>
            <a
              href='https://www.facebook.com'
              target='_blank'
              rel='noopener noreferrer'
              className='hover:text-white transition'
            >
              Facebook
            </a>
            <a
              href='https://www.instagram.com'
              target='_blank'
              rel='noopener noreferrer'
              className='hover:text-white transition'
            >
              Instagram
            </a>
            <a
              href='https://www.twitter.com'
              target='_blank'
              rel='noopener noreferrer'
              className='hover:text-white transition'
            >
              Twitter
            </a>
          </div>
        </div>
      </div>
    </footer>
  )
}

export default Footer
