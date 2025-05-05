import { useState, useEffect } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { useLocation } from 'react-router-dom'

interface Banner {
  id: number
  image: string
  title?: string
  status?: number
}

const Banner = () => {
  const location = useLocation()
  const [banners, setBanners] = useState<Banner[]>([])
  const [currentImage, setCurrentImage] = useState(0)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const customImage = 'https://intphcm.com/data/upload/poster-giay-den.jpg'

  if (location.pathname !== '/') {
    return null
  }

  useEffect(() => {
    const fetchBanners = async () => {
      try {
        setLoading(true)
        const response = await fetch('http://localhost:8000/api/banners')
        if (!response.ok) throw new Error('Không thể tải banners')
        const data = await response.json()
        const bannerList = Array.isArray(data) ? data : data.data || []

        const activeBanners = bannerList.filter((banner: Banner) => banner.status === undefined || banner.status === 1)

        const updatedBanners = [
          ...activeBanners,
          {
            id: 9999,
            image: customImage,
            title: 'Custom Banner',
            status: 1
          }
        ]

        console.log('Danh sách banners:', updatedBanners)
        setBanners(updatedBanners)
      } catch (err: any) {
        setError(err.message)
        setBanners([])
      } finally {
        setLoading(false)
      }
    }

    fetchBanners()
  }, [])

  useEffect(() => {
    if (banners.length === 0) return

    const interval = setInterval(() => {
      setCurrentImage((prev) => (prev + 1) % banners.length)
    }, 2000)

    return () => clearInterval(interval)
  }, [banners])

  if (loading) {
    return (
      <div className='relative w-screen h-[70vh] overflow-hidden shadow-lg'>
        <motion.div
          className='absolute top-0 left-0 w-full h-full'
          initial={{ opacity: 0, scale: 1.05 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.8, ease: 'easeOut' }}
        >
          <img src={customImage} alt='Loading Banner' className='w-full h-full object-cover' />
          <div className='absolute inset-0 bg-black bg-opacity-20 animate-pulse' />
        </motion.div>
      </div>
    )
  }

  if (error || banners.length === 0) {
    return (
      <div className='relative w-screen h-[70vh] bg-gray-100 shadow-lg'>
        <div className='absolute inset-0 flex items-center justify-center'>
          <p className='text-red-500 text-lg font-semibold'>{error || 'Không có banner nào để hiển thị'}</p>
        </div>
      </div>
    )
  }

  return (
    <div className='relative w-screen h-[70vh] overflow-hidden shadow-lg'>
      <AnimatePresence>
        <motion.div
          key={currentImage}
          className='absolute top-0 left-0 w-full h-full'
          initial={{ opacity: 0, scale: 1.1 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0, scale: 1.1 }}
          transition={{ duration: 1, ease: 'easeInOut' }}
        >
          <img
            src={banners[currentImage].image}
            alt={banners[currentImage].title || 'Banner'}
            className='w-full h-full object-cover'
          />
          <div className='absolute inset-0 bg-black bg-opacity-10' />
        </motion.div>
      </AnimatePresence>
    </div>
  )
}

export default Banner
