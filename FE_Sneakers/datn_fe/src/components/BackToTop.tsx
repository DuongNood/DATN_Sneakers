import { useState, useEffect } from 'react'
import { FaArrowUp } from 'react-icons/fa'
import { motion } from 'framer-motion'

const BackToTop = () => {
  const [isVisible, setIsVisible] = useState(false)

  // Theo dõi sự kiện cuộn để hiển thị/ẩn nút
  useEffect(() => {
    const toggleVisibility = () => {
      if (window.scrollY > 300) {
        setIsVisible(true)
      } else {
        setIsVisible(false)
      }
    }

    window.addEventListener('scroll', toggleVisibility)

    return () => window.removeEventListener('scroll', toggleVisibility)
  }, [])

  // Hàm cuộn mượt thủ công
  const scrollToTop = () => {
    const scrollStep = -window.scrollY / (1000 / 15) // 1000ms = 1 giây, 15ms mỗi khung
    let scrollCount = 0
    const scrollInterval = setInterval(() => {
      if (window.scrollY !== 0) {
        window.scrollBy(0, scrollStep)
        scrollCount += 15
        if (scrollCount >= 1000) {
          // Dừng sau 1 giây
          clearInterval(scrollInterval)
          window.scrollTo(0, 0) // Đảm bảo lên đầu
        }
      } else {
        clearInterval(scrollInterval)
      }
    }, 15) // 15ms mỗi bước để mượt
  }

  // Variants cho hiệu ứng trượt của nút
  const buttonVariants = {
    hidden: {
      y: 100,
      opacity: 0
    },
    visible: {
      y: 0,
      opacity: 1,
      transition: {
        duration: 0.5,
        ease: 'easeInOut'
      }
    }
  }

  return (
    <motion.button
      onClick={scrollToTop}
      className='fixed bottom-20 right-20 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 z-50'
      variants={buttonVariants}
      initial='hidden'
      animate={isVisible ? 'visible' : 'hidden'}
      aria-label='Back to Top'
    >
      <FaArrowUp size={20} />
    </motion.button>
  )
}

export default BackToTop
