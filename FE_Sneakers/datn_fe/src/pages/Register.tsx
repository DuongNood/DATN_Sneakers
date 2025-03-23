import { Link, useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import * as yup from 'yup'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import axios from 'axios'
import { useTranslation } from 'react-i18next'
import { useState } from 'react'
import { AiOutlineEye, AiOutlineEyeInvisible } from 'react-icons/ai'

const schema = yup.object().shape({
  name: yup.string().required('name_required'),
  email: yup.string().email('email_invalid').required('email_required'),
  password: yup.string().min(8, 'password_min').required('password_required'),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('password')], 'confirm_password_match')
    .required('confirm_password_required')
})

const Register = () => {
  const { t } = useTranslation()
  const navigate = useNavigate()
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm({
    resolver: yupResolver(schema)
  })

  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)

  const onSubmit = async (data: any) => {
    try {
      await axios.post('http://localhost:8000/api/register', {
        name: data.name,
        email: data.email,
        password: data.password,
        password_confirmation: data.confirmPassword
      })

      toast.success(t('register_success'), { autoClose: 1000 })
      setTimeout(() => navigate('/login'), 1000)
    } catch (error: any) {
      if (error.response && error.response.data.errors) {
        Object.values(error.response.data.errors).forEach((message: any) => {
          toast.error(t(message[0]))
        })
      } else {
        toast.error(t('system_error'), { autoClose: 2000 })
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
        <h2 className='text-2xl font-semibold text-gray-800 mb-6 text-center'>{t('register')}</h2>

        <form onSubmit={handleSubmit(onSubmit)} className='space-y-5'>
          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>{t('name')}</label>
            <input
              {...register('name')}
              type='text'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder={t('name_placeholder') || 'Enter your full name'}
            />
            {errors.name && <p className='text-red-500 text-sm'>{t(errors.name.message)}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>{t('email')}</label>
            <input
              {...register('email')}
              type='email'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder={t('email_placeholder')}
            />
            {errors.email && <p className='text-red-500 text-sm'>{t(errors.email.message)}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>{t('password')}</label>
            <div className='relative'>
              <input
                {...register('password')}
                type={showPassword ? 'text' : 'password'}
                className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
                placeholder={t('password_placeholder')}
              />
              <button
                type='button'
                className='absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700'
                onClick={() => setShowPassword(!showPassword)}
              >
                {showPassword ? <AiOutlineEyeInvisible size={20} /> : <AiOutlineEye size={20} />}
              </button>
            </div>
            {errors.password && <p className='text-red-500 text-sm'>{t(errors.password.message)}</p>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>{t('confirm_password')}</label>
            <div className='relative'>
              <input
                {...register('confirmPassword')}
                type={showConfirmPassword ? 'text' : 'password'}
                className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
                placeholder={t('confirm_password_placeholder')}
              />
              <button
                type='button'
                className='absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700'
                onClick={() => setShowConfirmPassword(!showConfirmPassword)}
              >
                {showConfirmPassword ? <AiOutlineEyeInvisible size={20} /> : <AiOutlineEye size={20} />}
              </button>
            </div>
            {errors.confirmPassword && <p className='text-red-500 text-sm'>{t(errors.confirmPassword.message)}</p>}
          </motion.div>

          <motion.button
            whileHover={{ scale: 1.03 }}
            whileTap={{ scale: 0.97 }}
            transition={{ duration: 0.3 }}
            type='submit'
            className='w-full bg-blue-500 text-white py-3 rounded-md font-semibold hover:bg-blue-600 transition'
          >
            {t('register_button')}
          </motion.button>
        </form>

        <p className='text-gray-600 mt-6 text-center text-sm'>
          {t('have_account')}{' '}
          <Link to='/login' className='text-blue-500 hover:underline'>
            {t('login')}
          </Link>
        </p>
      </motion.div>
    </div>
  )
}

export default Register
