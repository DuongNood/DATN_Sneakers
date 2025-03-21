import { Link, useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import * as yup from 'yup'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import axios from 'axios'

const schema = yup.object().shape({
  email: yup.string().email('Email không hợp lệ').required('Vui lòng nhập email')
})

const ForgotPassword = () => {
  const navigate = useNavigate()
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm({
    resolver: yupResolver(schema)
  })

  const onSubmit = async (data: { email: string }) => {
    try {
      await axios.post('http://127.0.0.1:8000/api/forgot-password', { email: data.email })
      toast.success('Vui lòng kiểm tra email để đặt lại mật khẩu.', { autoClose: 2000 })
      setTimeout(() => navigate('/login'), 2000)
    } catch (error) {
      toast.error('Lỗi, vui lòng thử lại.')
    }
  }

  return (
    <div className='flex justify-center items-center min-h-screen bg-gray-100 px-4'>
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.8, ease: 'easeInOut' }}
        className='w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white shadow-lg rounded-lg p-6 sm:p-8'
      >
        <h2 className='text-2xl font-semibold text-gray-800 mb-6 text-center'>Quên Mật Khẩu</h2>

        <form onSubmit={handleSubmit(onSubmit)} className='space-y-5'>
          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Email</label>
            <input
              type='email'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập email của bạn'
              {...register('email')}
            />
            {errors.email && <span className='text-red-500 text-sm'>{errors.email.message}</span>}
          </motion.div>

          <motion.button
            whileHover={{ scale: 1.03 }}
            whileTap={{ scale: 0.97 }}
            transition={{ duration: 0.3 }}
            className='w-full bg-blue-500 text-white py-3 rounded-md font-semibold hover:bg-blue-600 transition'
          >
            Gửi Yêu Cầu
          </motion.button>
        </form>

        <p className='text-gray-600 mt-6 text-center text-sm'>
          Đã có tài khoản?{' '}
          <Link to='/login' className='text-blue-500 hover:underline'>
            Đăng nhập
          </Link>
        </p>
      </motion.div>
    </div>
  )
}

export default ForgotPassword
