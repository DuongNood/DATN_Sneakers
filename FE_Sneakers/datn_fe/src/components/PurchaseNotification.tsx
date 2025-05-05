import { useState, useEffect } from 'react'

interface Notification {
  id: number
  buyerName: string
  productName: string
  productCode: string
  imageUrl: string
  timeAgo: string
}

const PurchaseNotification = () => {
  const notifications: Notification[] = [
    {
      id: 1,
      buyerName: 'Bùi Lê Hoàng Anh',
      productName: "Giày Onitsuka Tiger Runspark 'White Silver'",
      productCode: '1183B480-104',
      imageUrl: 'https://instandeebinhthanh.com/wp-content/uploads/2024/11/hinh-anh-meme-878x1024.jpg', // Thay bằng URL ảnh thực tế
      timeAgo: '20 phút trước'
    },
    {
      id: 2,
      buyerName: 'Bùi Lê Hoàng Em',
      productName: 'Giày Nike Air Max 270',
      productCode: 'AH8050-005',
      imageUrl:
        'https://image.dienthoaivui.com.vn/x,webp,q90/https://dashboard.dienthoaivui.com.vn/uploads/dashboard/editor_upload/anh-meme-15.jpg',
      timeAgo: '15 phút trước'
    },
    {
      id: 3,
      buyerName: 'Hoàng Anh Bùi Lê',
      productName: 'Giày Adidas Ultraboost 21',
      productCode: 'FY0378',
      imageUrl:
        'https://cdn-i.vtcnews.vn/resize/th/upload/2024/11/01/4651573671221205678945064353380396882975175287n-14171688.png',
      timeAgo: '10 phút trước'
    },
    {
      id: 4,
      buyerName: 'Hoàng Em Bùi Lê',
      productName: 'Giày Puma RS-X3',
      productCode: '374665-01',
      imageUrl: 'https://img7.thuthuatphanmem.vn/uploads/2023/08/26/meme-em-be-han-quoc-sieu-hai-huoc_023718487.jpg',
      timeAgo: '5 phút trước'
    }
  ]

  const [currentNotificationIndex, setCurrentNotificationIndex] = useState(0)
  const [isVisible, setIsVisible] = useState(true)

  useEffect(() => {
    const interval = setInterval(() => {
      setIsVisible(false)
      setTimeout(() => {
        setCurrentNotificationIndex((prevIndex) => (prevIndex + 1) % notifications.length)
        setIsVisible(true)
      }, 500)
    }, 3000)

    return () => clearInterval(interval)
  }, [notifications.length])

  const handleClose = () => {
    setIsVisible(false)
    setTimeout(() => {
      setCurrentNotificationIndex((prevIndex) => (prevIndex + 1) % notifications.length)
      setIsVisible(true)
    }, 500)
  }

  const currentNotification = notifications[currentNotificationIndex]

  return (
    <div className='fixed bottom-4 left-4 z-50'>
      {isVisible && (
        <div className='flex items-center bg-white shadow-lg rounded-lg p-3 max-w-sm animate-slide-up'>
          <img
            src={currentNotification.imageUrl}
            alt={currentNotification.productName}
            className='w-16 h-16 object-cover rounded-md mr-3'
          />

          <div className='flex-1'>
            <p className='text-sm font-semibold text-gray-800'>{currentNotification.buyerName}</p>
            <p className='text-xs text-gray-500'>Đã mua</p>
            <p className='text-sm font-semibold text-gray-800'>
              {currentNotification.productName} {currentNotification.productCode}
            </p>
            <p className='text-xs text-gray-500'>{currentNotification.timeAgo}</p>
          </div>

          <button onClick={handleClose} className='ml-3 text-gray-500 hover:text-gray-700'>
            <svg
              className='w-4 h-4'
              fill='none'
              stroke='currentColor'
              viewBox='0 0 24 24'
              xmlns='http://www.w3.org/2000/svg'
            >
              <path strokeLinecap='round' strokeLinejoin='round' strokeWidth='2' d='M6 18L18 6M6 6l12 12' />
            </svg>
          </button>
        </div>
      )}
    </div>
  )
}

export default PurchaseNotification
