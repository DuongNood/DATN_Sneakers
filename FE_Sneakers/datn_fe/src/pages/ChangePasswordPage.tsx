import React, { useState } from 'react'
import { Link } from 'react-router-dom'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import * as yup from 'yup'
import { useTranslation } from 'react-i18next'
import { FaEye, FaEyeSlash } from 'react-icons/fa'

const schema = yup.object().shape({
  oldPassword: yup.string().required('old_password_required'),
  newPassword: yup.string().min(8, 'password_min_length').required('new_password_required'),
  confirmNewPassword: yup
    .string()
    .oneOf([yup.ref('newPassword'), null], 'passwords_must_match')
    .required('confirm_new_password_required')
})

const ChangePasswordPage = () => {
  const { t } = useTranslation()
  const [formData, setFormData] = useState({
    oldPassword: '',
    newPassword: '',
    confirmNewPassword: ''
  })
  const [errors, setErrors] = useState({
    oldPassword: '',
    newPassword: '',
    confirmNewPassword: ''
  })
  const [loading, setLoading] = useState(false)
  const [showPasswords, setShowPasswords] = useState({
    oldPassword: false,
    newPassword: false,
    confirmNewPassword: false
  })

  const handleInputChange = (e) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
    setErrors((prev) => ({ ...prev, [name]: '' }))
  }

  const togglePasswordVisibility = (field) => {
    setShowPasswords((prev) => ({ ...prev, [field]: !prev[field] }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)

    try {
      await schema.validate(formData, { abortEarly: false })

      const token = localStorage.getItem('token')
      if (!token) {
        throw new Error(t('no_token_found'))
      }

      const response = await fetch('http://127.0.0.1:8000/api/change-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({
          current_password: formData.oldPassword,
          new_password: formData.newPassword,
          new_password_confirmation: formData.confirmNewPassword
        })
      })

      if (!response.ok) {
        const errorData = await response.json()
        console.error('Change password error:', errorData)

        if (errorData.message === 'Current password is incorrect') {
          throw new Error(t('incorrect_old_password'))
        }
        throw new Error(errorData.message || t('change_password_error'))
      }

      setFormData({
        oldPassword: '',
        newPassword: '',
        confirmNewPassword: ''
      })
      toast.success(t('change_password_success'), { autoClose: 1000 })
    } catch (error) {
      if (error instanceof yup.ValidationError) {
        const newErrors = { oldPassword: '', newPassword: '', confirmNewPassword: '' }
        error.inner.forEach((err) => {
          newErrors[err.path] = t(err.message)
        })
        setErrors(newErrors)
      } else {
        console.error('Error changing password:', error.message)
        toast.error(error.message, { autoClose: 1000 })
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6'>
      <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
        <nav className='space-y-2'>
          <Link to='/profile' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>👤</span> {t('my_account')}
          </Link>
          <Link to='/change-password' className='flex items-center p-2 text-blue-600 bg-blue-50 rounded-md'>
            <span className='mr-2'>🔒</span> {t('change_password')}
          </Link>
          <Link to='/orders' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>📋</span> {t('orders')}
          </Link>
        </nav>
      </div>

      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <h2 className='text-xl font-semibold mb-6'>{t('change_password')}</h2>

        <form onSubmit={handleSubmit} className='space-y-6'>
          <div className='relative'>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('old_password')}</label>
            <input
              type={showPasswords.oldPassword ? 'text' : 'password'}
              name='oldPassword'
              className='w-full p-3 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('old_password_placeholder')}
              value={formData.oldPassword}
              onChange={handleInputChange}
            />
            <button
              type='button'
              className='absolute right-3 top-10 text-gray-500 hover:text-gray-700'
              onClick={() => togglePasswordVisibility('oldPassword')}
            >
              {showPasswords.oldPassword ? <FaEyeSlash /> : <FaEye />}
            </button>
            {errors.oldPassword && <p className='text-red-500 text-sm mt-1'>{errors.oldPassword}</p>}
          </div>

          <div className='relative'>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('new_password')}</label>
            <input
              type={showPasswords.newPassword ? 'text' : 'password'}
              name='newPassword'
              className='w-full p-3 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('new_password_placeholder')}
              value={formData.newPassword}
              onChange={handleInputChange}
            />
            <button
              type='button'
              className='absolute right-3 top-10 text-gray-500 hover:text-gray-700'
              onClick={() => togglePasswordVisibility('newPassword')}
            >
              {showPasswords.newPassword ? <FaEyeSlash /> : <FaEye />}
            </button>
            {errors.newPassword && <p className='text-red-500 text-sm mt-1'>{errors.newPassword}</p>}
          </div>

          <div className='relative'>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('confirm_new_password')}</label>
            <input
              type={showPasswords.confirmNewPassword ? 'text' : 'password'}
              name='confirmNewPassword'
              className='w-full p-3 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('confirm_new_password_placeholder')}
              value={formData.confirmNewPassword}
              onChange={handleInputChange}
            />
            <button
              type='button'
              className='absolute right-3 top-10 text-gray-500 hover:text-gray-700'
              onClick={() => togglePasswordVisibility('confirmNewPassword')}
            >
              {showPasswords.confirmNewPassword ? <FaEyeSlash /> : <FaEye />}
            </button>
            {errors.confirmNewPassword && <p className='text-red-500 text-sm mt-1'>{errors.confirmNewPassword}</p>}
          </div>

          <button
            type='submit'
            disabled={loading}
            className={`w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition ${
              loading ? 'opacity-50 cursor-not-allowed' : ''
            }`}
          >
            {loading ? t('saving') : t('save_changes')}
          </button>

          <div className='text-center'>
            <Link to='/forgot-password' className='text-blue-600 hover:underline text-sm'>
              {t('forgot_password')}
            </Link>
          </div>
        </form>
      </div>
    </div>
  )
}

export default ChangePasswordPage
