import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import * as yup from 'yup'
import { useTranslation } from 'react-i18next'

const schema = yup.object().shape({
  name: yup.string().required('name_required'),
  address: yup.string().required('address_required'),
  phone: yup
    .string()
    .matches(/^[0-9]{10}$/, 'phone_invalid')
    .required('phone_required')
})

const ProfilePage = () => {
  const { t } = useTranslation() // Thêm useTranslation để dịch
  const [userData, setUserData] = useState(null)
  const [loading, setLoading] = useState(true)
  const [editField, setEditField] = useState({ name: false, address: false, phone: false })
  const [formData, setFormData] = useState({ name: '', address: '', phone: '' })
  const [errors, setErrors] = useState({ name: '', address: '', phone: '' })

  useEffect(() => {
    const fetchUserData = async () => {
      try {
        const token = localStorage.getItem('token')
        const response = await fetch('http://127.0.0.1:8000/api/user', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })

        if (!response.ok) {
          throw new Error(t('fetch_error'))
        }

        const data = await response.json()
        setUserData(data)
        setFormData({
          name: data.name || '',
          address: data.address || '',
          phone: data.phone || ''
        })
        setLoading(false)
      } catch (error) {
        console.error('Error fetching user data:', error)
        setLoading(false)
      }
    }

    fetchUserData()
  }, [t])

  const handleEditToggle = (field) => {
    setEditField((prev) => ({ ...prev, [field]: !prev[field] }))
    setErrors((prev) => ({ ...prev, [field]: '' }))
  }

  const handleInputChange = (e, field) => {
    setFormData((prev) => ({ ...prev, [field]: e.target.value }))
  }

  const handleSaveChanges = async () => {
    try {
      await schema.validate(formData, { abortEarly: false })

      const token = localStorage.getItem('token')
      const response = await fetch('http://127.0.0.1:8000/api/users/{$id}', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify(formData)
      })

      if (!response.ok) {
        throw new Error(t('update_error'))
      }

      const updatedData = await response.json()
      setUserData(updatedData)
      setEditField({ name: false, address: false, phone: false })
      toast.success(t('update_success'), { autoClose: 1000 })
    } catch (error) {
      if (error instanceof yup.ValidationError) {
        const newErrors = { name: '', address: '', phone: '' }
        error.inner.forEach((err) => {
          newErrors[err.path] = t(err.message) // Dịch lỗi từ schema
        })
        setErrors(newErrors)
      } else {
        console.error('Error updating profile:', error)
        toast.error(t('update_error'))
      }
    }
  }

  // Skeleton loading khớp với layout thực tế
  const SkeletonLoading = () => (
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6 animate-pulse'>
      <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
        <div className='space-y-2'>
          <div className='h-10 w-full bg-gray-300 rounded-md'></div>
          <div className='h-10 w-full bg-gray-300 rounded-md'></div>
          <div className='h-10 w-full bg-gray-300 rounded-md'></div>
        </div>
      </div>
      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <div className='h-6 w-1/4 bg-gray-300 rounded mb-6'></div>
        <div className='space-y-6'>
          <div className='flex flex-col items-center'>
            <div className='w-32 h-32 bg-gray-300 rounded-full mb-4'></div>
            <div className='h-10 w-40 bg-gray-300 rounded'></div>
          </div>
          <div>
            <div className='h-4 w-20 bg-gray-300 rounded mb-1'></div>
            <div className='relative'>
              <div className='w-full h-12 bg-gray-300 rounded-md'></div>
            </div>
          </div>
          <div>
            <div className='h-4 w-20 bg-gray-300 rounded mb-1'></div>
            <div className='relative'>
              <div className='w-full h-12 bg-gray-300 rounded-md'></div>
            </div>
          </div>
          <div>
            <div className='h-4 w-20 bg-gray-300 rounded mb-1'></div>
            <div className='relative'>
              <div className='w-full h-12 bg-gray-300 rounded-md'></div>
            </div>
          </div>
          <div>
            <div className='h-4 w-20 bg-gray-300 rounded mb-1'></div>
            <div className='relative'>
              <div className='w-full h-12 bg-gray-300 rounded-md'></div>
            </div>
          </div>
          <div className='w-full h-12 bg-gray-300 rounded-lg'></div>
        </div>
      </div>
    </div>
  )

  if (loading) {
    return <SkeletonLoading />
  }

  if (!userData) {
    return <p className='text-center text-lg'>{t('no_user_data')}</p>
  }

  return (
    <div className='max-w-5xl mx-auto p-6 mt-6 flex flex-col md:flex-row gap-6'>
      <div className='w-full md:w-1/4 bg-white shadow-md rounded-lg p-4'>
        <nav className='space-y-2'>
          <Link to='/profile' className='flex items-center p-2 text-blue-600 bg-blue-50 rounded-md'>
            <span className='mr-2'>👤</span> {t('my_account')}
          </Link>
          <Link to='/change-password' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>🔒</span> {t('change_password')}
          </Link>
          <Link to='/orders' className='flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-md'>
            <span className='mr-2'>📋</span> {t('orders')}
          </Link>
        </nav>
      </div>

      <div className='w-full md:w-3/4 bg-white shadow-md rounded-lg p-6'>
        <h2 className='text-xl font-semibold mb-6'>{t('my_account')}</h2>

        <div className='space-y-6'>
          <div className='flex flex-col items-center'>
            <img
              src={
                userData.avatar ||
                'https://gentlenobra.net/wp-content/uploads/2024/02/hinh-anh-gai-xinh-nude-3.jpg.webp'
              }
              alt='Avatar'
              className='w-32 h-32 rounded-full object-cover mb-4 border-2 border-gray-300'
            />
            <input
              type='file'
              accept='.jpg,.jpeg,.png'
              className='text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100'
            />
          </div>

          <div className='relative'>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('name')}</label>
            <div className='relative'>
              <input
                type='text'
                className='w-full p-3 pr-20 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
                placeholder={t('name_placeholder')}
                value={formData.name}
                onChange={(e) => handleInputChange(e, 'name')}
                readOnly={!editField.name}
              />
              <button
                onClick={() => handleEditToggle('name')}
                className='absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-600 hover:underline text-sm'
              >
                {editField.name ? t('cancel') : t('edit')}
              </button>
            </div>
            {errors.name && <p className='text-red-500 text-sm mt-1'>{errors.name}</p>}
          </div>

          <div>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('email')}</label>
            <input
              type='email'
              className='w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
              placeholder={t('email_placeholder')}
              value={userData.email || ''}
              readOnly
            />
          </div>

          <div className='relative'>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('address')}</label>
            <div className='relative'>
              <input
                type='text'
                className='w-full p-3 pr-20 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
                placeholder={t('address_placeholder')}
                value={formData.address}
                onChange={(e) => handleInputChange(e, 'address')}
                readOnly={!editField.address}
              />
              <button
                onClick={() => handleEditToggle('address')}
                className='absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-600 hover:underline text-sm'
              >
                {editField.address ? t('cancel') : t('edit')}
              </button>
            </div>
            {errors.address && <p className='text-red-500 text-sm mt-1'>{errors.address}</p>}
          </div>

          <div className='relative'>
            <label className='block text-sm font-medium text-gray-700 mb-1'>{t('phone')}</label>
            <div className='relative'>
              <input
                type='text'
                className='w-full p-3 pr-20 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
                placeholder={t('phone_placeholder')}
                value={formData.phone}
                onChange={(e) => handleInputChange(e, 'phone')}
                readOnly={!editField.phone}
              />
              <button
                onClick={() => handleEditToggle('phone')}
                className='absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-600 hover:underline text-sm'
              >
                {editField.phone ? t('cancel') : t('edit')}
              </button>
            </div>
            {errors.phone && <p className='text-red-500 text-sm mt-1'>{errors.phone}</p>}
          </div>

          <button
            onClick={handleSaveChanges}
            className='w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition'
          >
            {t('save_changes')}
          </button>
        </div>
      </div>
    </div>
  )
}

export default ProfilePage
