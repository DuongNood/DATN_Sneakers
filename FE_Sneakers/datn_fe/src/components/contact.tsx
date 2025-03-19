import { motion } from 'framer-motion'
import { useState } from 'react'

const ContactPage = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    message: ''
  })

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value })
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    alert('Gửi thành công!')
  }

  return (
    <div className='min-h-screen flex items-center justify-center bg-gray-50 p-6'>
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.8, ease: 'easeInOut' }}
        className='flex flex-col md:flex-row bg-white shadow-lg rounded-xl overflow-hidden w-full max-w-5xl'
      >
        <div className='w-full md:w-1/2 p-8 flex flex-col justify-center'>
          <h2 className='text-3xl font-bold text-gray-900 mb-6 text-center'>Liên hệ với các bố</h2>

          <form onSubmit={handleSubmit} className='space-y-5'>
            <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
              <input
                type='text'
                name='name'
                value={formData.name}
                onChange={handleChange}
                placeholder='Họ và tên'
                className='w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition'
                required
              />
            </motion.div>

            <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
              <input
                type='email'
                name='email'
                value={formData.email}
                onChange={handleChange}
                placeholder='Email của bạn'
                className='w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition'
                required
              />
            </motion.div>

            <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
              <textarea
                name='message'
                value={formData.message}
                onChange={handleChange}
                placeholder='Nội dung tin nhắn'
                rows={4}
                className='w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition'
                required
              />
            </motion.div>

            <motion.button
              whileHover={{ scale: 1.03 }}
              whileTap={{ scale: 0.97 }}
              transition={{ duration: 0.3 }}
              className='w-full py-3 bg-blue-600 text-white text-lg rounded-lg font-semibold hover:bg-blue-700 transition'
            >
              Gửi tin nhắn
            </motion.button>
          </form>
        </div>

        {/* Google Map */}
        <motion.div
          initial={{ opacity: 0, x: 50 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.8, ease: 'easeInOut', delay: 0.2 }}
          className='w-full md:w-1/2 h-80 md:h-auto'
        >
          <iframe
            title='Google Map'
            src='https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7447.727851395522!2d105.747262!3d21.03813!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1svi!2sus!4v1742361480290!5m2!1svi!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade'
            width='100%'
            height='100%'
            className='h-full'
            style={{ border: 0 }}
            allowFullScreen
            loading='lazy'
          ></iframe>
        </motion.div>
      </motion.div>
    </div>
  )
}

export default ContactPage
