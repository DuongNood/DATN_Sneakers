import { Link, useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import * as yup from 'yup'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import { SignedIn, SignedOut, SignInButton, UserButton } from '@clerk/clerk-react'
import axios from 'axios'
import { useTranslation } from 'react-i18next'

const schema = yup.object().shape({
  email: yup.string().email('email_invalid').required('email_required'),
  password: yup.string().min(8, 'password_min').required('password_required')
})

const Login = () => {
  const { t } = useTranslation() 
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

      toast.success(t('login_success'), { autoClose: 1000 })
      setTimeout(() => navigate('/'), 100)
    } catch (error: any) {
      if (error.response && error.response.status === 401) {
        setError('password', {
          type: 'manual',
          message: t('login_failed')
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
        <h2 className='text-2xl font-semibold text-gray-800 mb-6 text-center'>{t('login')}</h2>

        <form onSubmit={handleSubmit(onSubmit)} className='space-y-5'>
          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>{t('email')}</label>
            <input
              type='email'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder={t('email_placeholder')}
              {...register('email')}
            />
            {errors.email && <span className='text-red-500 text-sm'>{t(errors.email.message)}</span>}
          </motion.div>

          <motion.div whileHover={{ scale: 1.015 }} transition={{ duration: 0.3 }}>
            <label className='block text-sm font-medium text-gray-700'>{t('password')}</label>
            <input
              type='password'
              className='w-full mt-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none transition'
              placeholder={t('password_placeholder')}
              {...register('password')}
            />
            {errors.password && <span className='text-red-500 text-sm'>{t(errors.password.message)}</span>}
          </motion.div>

          <motion.button
            whileHover={{ scale: 1.03 }}
            whileTap={{ scale: 0.97 }}
            transition={{ duration: 0.3 }}
            className='w-full bg-blue-500 text-white py-3 rounded-md font-semibold hover:bg-blue-600 transition'
          >
            {t('login_button')}
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
                {t('login_with_other')}
              </motion.button>
            </SignInButton>
          </SignedOut>

          <SignedIn>
            <UserButton />
          </SignedIn>
        </div>

        <p className='text-gray-600 mt-6 text-center text-sm flex justify-between'>
          <span>
            {t('no_account')}{' '}
            <Link to='/register' className='text-blue-500 hover:underline'>
              {t('register')}
            </Link>
          </span>
          <Link to='/forgot-password' className='text-blue-500 hover:underline'>
            {t('forgot_password')}
          </Link>
        </p>
      </motion.div>
    </div>
  )
}

export default Login
