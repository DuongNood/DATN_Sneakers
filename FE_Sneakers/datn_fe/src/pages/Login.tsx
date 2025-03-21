import { Link, useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import * as yup from 'yup'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import { SignedIn, SignedOut, SignInButton, UserButton } from '@clerk/clerk-react'
import axios from 'axios'

const schema = yup.object().shape({
  email: yup.string().email('Email không hợp lệ').required('Vui lòng nhập email'),
  password: yup.string().min(8, 'Mật khẩu phải có ít nhất 8 ký tự').required('Vui lòng nhập mật khẩu')
})

const Login = () => {
  const navigate = useNavigate()
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors }
  } = useForm({
    resolver: yupResolver(schema)
  })

  const onSubmit = async (data: { email: string; password: string }) => {
    try {
      const response = await axios.post('http://127.0.0.1:8000/api/login', data)

      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))

      toast.success('Đăng nhập thành công!', { autoClose: 1000 })
      setTimeout(() => navigate('/'), 100)
    } catch (error: any) {
      if (error.response && error.response.status === 401) {
        setError('password', {
          type: 'manual',
          message: 'Sai tài khoản hoặc mật khẩu. Vui lòng thử lại!'
        })
      } else {
        toast.error('Hệ thống đang bảo trì, vui lòng quay lại sau!', { autoClose: 2000 })
      }
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
        <h2 className='text-2xl font-semibold text-gray-800 mb-6 text-center'>Đăng Nhập</h2>

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

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Mật khẩu</label>
            <input
              type='password'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập mật khẩu'
              {...register('password')}
            />
            {errors.password && <span className='text-red-500 text-sm'>{errors.password.message}</span>}
          </motion.div>

          <motion.button
            whileHover={{ scale: 1.03 }}
            whileTap={{ scale: 0.97 }}
            transition={{ duration: 0.3 }}
            className='w-full bg-blue-500 text-white py-3 rounded-md font-semibold hover:bg-blue-600 transition'
          >
            Đăng Nhập
          </motion.button>
        </form>

        <div className='mt-4 flex flex-col items-center space-y-3'>
          <SignedOut>
            <SignInButton>
              <motion.button
                whileHover={{ scale: 1.03 }}
                whileTap={{ scale: 0.97 }}
                transition={{ duration: 0.3 }}
                className='w-full bg-gray-200 text-gray-800 py-3 rounded-md font-semibold hover:bg-gray-300 transition'
              >
                Đăng nhập với tài khoản khác
              </motion.button>
            </SignInButton>
          </SignedOut>

          <SignedIn>
            <UserButton />
          </SignedIn>
        </div>

        <p className='text-gray-600 mt-6 text-center text-sm flex justify-between'>
          <span>
            Chưa có tài khoản?{' '}
            <Link to='/register' className='text-blue-500 hover:underline'>
              Đăng ký
            </Link>
          </span>
          <Link to='/forgot-password' className='text-blue-500 hover:underline'>
            Quên mật khẩu?
          </Link>
        </p>
      </motion.div>
    </div>
  )
}

export default Login
