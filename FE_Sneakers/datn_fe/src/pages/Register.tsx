import { Link, useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import * as yup from 'yup'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import axios from 'axios'

const schema = yup.object().shape({
  name: yup.string().required('Vui lòng nhập họ và tên'),
  email: yup.string().email('Email không hợp lệ').required('Vui lòng nhập email'),
  phone: yup
    .string()
    .matches(/^[0-9]{10}$/, 'Số điện thoại phải có 10 chữ số')
    .required('Vui lòng nhập số điện thoại'),
  address: yup.string().required('Vui lòng nhập địa chỉ'),
  password: yup.string().min(6, 'Mật khẩu phải có ít nhất 6 ký tự').required('Vui lòng nhập mật khẩu'),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('password')], 'Mật khẩu không khớp')
    .required('Vui lòng nhập lại mật khẩu')
})

const Register = () => {
  const navigate = useNavigate()
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm({
    resolver: yupResolver(schema)
  })

  const onSubmit = async (data: any) => {
    try {
      await axios.post('http://localhost:8000/api/register', {
        name: data.name,
        email: data.email,
        phone: data.phone,
        address: data.address,
        password: data.password,
        password_confirmation: data.confirmPassword
      })

      toast.success('Đăng ký thành công!', { autoClose: 1000 })
      setTimeout(() => navigate('/login'), 1000)
    } catch (error: any) {
      if (error.response && error.response.data.errors) {
        Object.values(error.response.data.errors).forEach((message: any) => {
          toast.error(message[0])
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
        <h2 className='text-2xl font-semibold text-gray-800 mb-6 text-center'>Đăng Ký</h2>

        <form onSubmit={handleSubmit(onSubmit)} className='space-y-5'>
          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Họ và Tên</label>
            <input
              {...register('name')}
              type='text'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập họ và tên'
            />
            {errors.name && <p className='text-red-500 text-sm'>{errors.name.message}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Email</label>
            <input
              {...register('email')}
              type='email'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập email của bạn'
            />
            {errors.email && <p className='text-red-500 text-sm'>{errors.email.message}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Số điện thoại</label>
            <input
              {...register('phone')}
              type='text'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập số điện thoại (10 số)'
            />
            {errors.phone && <p className='text-red-500 text-sm'>{errors.phone.message}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Địa chỉ</label>
            <input
              {...register('address')}
              type='text'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập địa chỉ của bạn'
            />
            {errors.address && <p className='text-red-500 text-sm'>{errors.address.message}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Mật khẩu</label>
            <input
              {...register('password')}
              type='password'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập mật khẩu'
            />
            {errors.password && <p className='text-red-500 text-sm'>{errors.password.message}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>Nhập lại mật khẩu</label>
            <input
              {...register('confirmPassword')}
              type='password'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder='Nhập lại mật khẩu'
            />
            {errors.confirmPassword && <p className='text-red-500 text-sm'>{errors.confirmPassword.message}</p>}
          </motion.div>

          <motion.button
            whileHover={{ scale: 1.03 }}
            whileTap={{ scale: 0.97 }}
            transition={{ duration: 0.3 }}
            type='submit'
            className='w-full bg-blue-500 text-white py-3 rounded-md font-semibold hover:bg-blue-600 transition'
          >
            Đăng Ký
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

export default Register
