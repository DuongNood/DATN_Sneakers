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
    <>
      <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap' rel='stylesheet' />
      <div className='min-h-screen bg-gradient-to-br from-indigo-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8 font-poppins'>
        <div className='max-w-5xl mx-auto mx-6'>
          <div className='grid grid-cols-1 md:grid-cols-3 gap-8'>
            {/* Sidebar Navigation */}
            <div className='bg-white p-6 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl min-w-[200px]'>
              <nav className='space-y-3'>
                <Link
                  to='/profile'
                  className='flex items-center p-3 text-gray-700 hover:bg-indigo-50 rounded-lg text-base font-medium transition-all duration-200 whitespace-nowrap'
                >
                  <span className='mr-3'>👤</span> {t('my_account')}
                </Link>
                <Link
                  to='/change-password'
                  className='flex items-center p-3 text-blue-600 bg-blue-50 rounded-lg text-lg font-semibold whitespace-nowrap'
                >
                  <span className='mr-3'>🔒</span> {t('change_password')}
                </Link>
                <Link
                  to='/orders'
                  className='flex items-center p-3 text-gray-700 hover:bg-indigo-50 rounded-lg text-base font-medium transition-all duration-200 whitespace-nowrap'
                >
                  <span className='mr-3'>📋</span> {t('orders')}
                </Link>
              </nav>
            </div>

            {/* Change Password Form */}
            <div className='md:col-span-2 bg-white p-8 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl'>
              <h2 className='text-xl font-semibold text-gray-900 mb-8'>{t('change_password')}</h2>

              <form onSubmit={handleSubmit} className='space-y-6'>
                {/* Old Password */}
                <div className='relative'>
                  <label className='block text-sm font-medium text-gray-700 mb-2'>{t('old_password')}</label>
                  <div className='relative'>
                    <input
                      type={showPasswords.oldPassword ? 'text' : 'password'}
                      name='oldPassword'
                      className='w-full p-4 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base font-normal'
                      placeholder={t('old_password_placeholder')}
                      value={formData.oldPassword}
                      onChange={handleInputChange}
                    />
                    <button
                      type='button'
                      className='absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700'
                      onClick={() => togglePasswordVisibility('oldPassword')}
                    >
                      {showPasswords.oldPassword ? <FaEyeSlash size={16} /> : <FaEye size={16} />}
                    </button>
                  </div>
                  {errors.oldPassword && <p className='text-red-500 text-sm mt-2'>{errors.oldPassword}</p>}
                </div>

                {/* New Password */}
                <div className='relative'>
                  <label className='block text-sm font-medium text-gray-700 mb-2'>{t('new_password')}</label>
                  <div className='relative'>
                    <input
                      type={showPasswords.newPassword ? 'text' : 'password'}
                      name='newPassword'
                      className='w-full p-4 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base font-normal'
                      placeholder={t('new_password_placeholder')}
                      value={formData.newPassword}
                      onChange={handleInputChange}
                    />
                    <button
                      type='button'
                      className='absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700'
                      onClick={() => togglePasswordVisibility('newPassword')}
                    >
                      {showPasswords.newPassword ? <FaEyeSlash size={16} /> : <FaEye size={16} />}
                    </button>
                  </div>
                  {errors.newPassword && <p className='text-red-500 text-sm mt-2'>{errors.newPassword}</p>}
                </div>

                {/* Confirm New Password */}
                <div className='relative'>
                  <label className='block text-sm font-medium text-gray-700 mb-2'>{t('confirm_new_password')}</label>
                  <div className='relative'>
                    <input
                      type={showPasswords.confirmNewPassword ? 'text' : 'password'}
                      name='confirmNewPassword'
                      className='w-full p-4 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base font-normal'
                      placeholder={t('confirm_new_password_placeholder')}
                      value={formData.confirmNewPassword}
                      onChange={handleInputChange}
                    />
                    <button
                      type='button'
                      className='absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700'
                      onClick={() => togglePasswordVisibility('confirmNewPassword')}
                    >
                      {showPasswords.confirmNewPassword ? <FaEyeSlash size={16} /> : <FaEye size={16} />}
                    </button>
                  </div>
                  {errors.confirmNewPassword && (
                    <p className='text-red-500 text-sm mt-2'>{errors.confirmNewPassword}</p>
                  )}
                </div>

                {/* Submit Button */}
                <button
                  type='submit'
                  disabled={loading}
                  className={`w-full py-4 rounded-lg text-white font-medium transition-all bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 flex items-center justify-center ${
                    loading ? 'opacity-50 cursor-not-allowed' : ''
                  }`}
                >
                  {loading ? (
                    <>
                      <svg
                        className='w-5 h-5 mr-2 animate-spin'
                        fill='none'
                        viewBox='0 0 24 24'
                        xmlns='http://www.w3.org/2000/svg'
                      >
                        <circle className='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' strokeWidth='4' />
                        <path className='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z' />
                      </svg>
                      {t('saving')}
                    </>
                  ) : (
                    t('save_changes')
                  )}
                </button>

                {/* Forgot Password Link */}
                <div className='text-center mt-4'>
                  <Link to='/forgot-password' className='text-blue-600 hover:underline text-sm font-medium'>
                    {t('forgot_password')}
                  </Link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </>
  )
}

export default ChangePasswordPage
