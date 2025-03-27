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
      imageUrl:
        'https://scontent.fhan14-3.fna.fbcdn.net/v/t39.30808-6/482022875_629774439804638_5144918694120527923_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=a5f93a&_nc_eui2=AeEvOhuKWRVHXFN9JBWxcJTh1_t8k_MqJWfX-3yT8yolZ0MhiCSvnh2xKfyVFnpxwCjGJ1vpUDP2xU7BrcFY_zEG&_nc_ohc=OEweYEiGH6gQ7kNvgHGs6-1&_nc_oc=AdnyEch06YIhKDiC_5CWQHlMhoX_QIdqN4evR2HeMPx1B1qJokhGl63JfvqaxyZBLXA&_nc_zt=23&_nc_ht=scontent.fhan14-3.fna&_nc_gid=HmTgGI0ynMJ2N2n43Qf3GQ&oh=00_AYHR3dBrkwAsyD0QRhRRkxV8yqB487YeHqotmno4SZtO_Q&oe=67E78322', // Thay bằng URL ảnh thực tế
      timeAgo: '20 phút trước'
    },
    {
      id: 2,
      buyerName: 'Bùi Lê Hoàng Em',
      productName: 'Giày Nike Air Max 270',
      productCode: 'AH8050-005',
      imageUrl:
        'https://scontent.fhan14-1.fna.fbcdn.net/v/t39.30808-6/476955548_615781651203917_6796695433879999204_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=a5f93a&_nc_eui2=AeHCsxSQXslfWkdxHIAhN27mnFRBU7yCBgmcVEFTvIIGCS8MSBNZw7r1rjaiYHuGbsQsDniyVnc2W58AUCORoegI&_nc_ohc=AktlTqRQeOAQ7kNvgH_oh1h&_nc_oc=Adl7g6NQjRLnRt6VO0FhkAeNcdulUfe5y6czZo4CDyb5mwYj8ca35RNnrFF5tFaow7A&_nc_zt=23&_nc_ht=scontent.fhan14-1.fna&_nc_gid=CWhd5Vzwk9SHqsZo9w8t0A&oh=00_AYH3dX_MiiLy7nOKue5CCWJkcNVzKMcwNs5qVG21-YPQqQ&oe=67E75E4D',
      timeAgo: '15 phút trước'
    },
    {
      id: 3,
      buyerName: 'Hoàng Anh Bùi Lê',
      productName: 'Giày Adidas Ultraboost 21',
      productCode: 'FY0378',
      imageUrl:
        'https://scontent.fhan14-2.fna.fbcdn.net/v/t39.30808-6/480919772_618912754224140_4071766667382725914_n.jpg?_nc_cat=100&ccb=1-7&_nc_sid=a5f93a&_nc_eui2=AeFpQaFjRTytxlQh__ExW3HtWJ00j9iGteJYnTSP2Ia14gr_z7T0jPnT7jsxNm6oJpRCu9_6cL0_1FLB7MerEeIk&_nc_ohc=HoOgiLkx-YsQ7kNvgGazfW5&_nc_oc=AdmfLaIxO8iqGZEuEuxWawr-upP9387NeBCJpYP7Pq8wtDVdDE5rMxVHeUUsbPf_lzw&_nc_zt=23&_nc_ht=scontent.fhan14-2.fna&_nc_gid=15ogJAsnjKRLEUCXxQFfDQ&oh=00_AYG7RDswh0O7N7D6l1P-WgHZd68EkcFJ9m5bE6tTgbTSXw&oe=67E77F9C',
      timeAgo: '10 phút trước'
    },
    {
      id: 4,
      buyerName: 'Hoàng Em Bùi LÊ',
      productName: 'Giày Puma RS-X3',
      productCode: '374665-01',
      imageUrl:
        'https://scontent.fhan14-4.fna.fbcdn.net/v/t39.30808-6/477518241_613257114789704_3063306880538992678_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=a5f93a&_nc_eui2=AeEKKNGtUTYDEEdvTW8cc4TNgfW9JQVLh8KB9b0lBUuHwj0tgP7zSiUTXrDYnt2bweswI9UwG0d9qRbXLh0I6kxv&_nc_ohc=YKptNYWTUc4Q7kNvgH69vxy&_nc_oc=AdnGUKhqNAbYPmSp2iZT0TPq8HtgevpHCrdvp5yPj7lyta0BFKATgVrVWHY-OijFJZc&_nc_zt=23&_nc_ht=scontent.fhan14-4.fna&_nc_gid=x4Ws0a9acFatqCb6MDhxRQ&oh=00_AYHFQXNv1sLOuS_nWgnDoWEinMHkYh4autKVFBoZz85OaA&oe=67E78952',
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
            <p className='text-xs text-gray-500'>vừa mua</p>
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
