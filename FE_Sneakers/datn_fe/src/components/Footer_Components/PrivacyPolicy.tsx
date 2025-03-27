import { motion } from 'framer-motion'

export default function PrivacyPolicy() {
  return (
    <div className='min-h-screen bg-gradient-to-r from-gray-100 to-gray-200 flex justify-center items-center p-6'>
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.8 }}
        className='bg-white shadow-2xl rounded-3xl p-10 max-w-3xl w-full border border-gray-300'
      >
        <motion.h1
          initial={{ opacity: 0, x: -50 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 0.2, duration: 0.8 }}
          className='text-4xl font-extrabold text-gray-900 mb-6 text-center'
        >
          Chính Sách Bảo Mật
        </motion.h1>
        <motion.p
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ delay: 0.4, duration: 0.8 }}
          className='text-gray-700 text-lg leading-relaxed text-center mb-8'
        >
          Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn và đảm bảo quyền riêng tư khi sử dụng trang web bán giày
          sneaker của chúng tôi.
        </motion.p>

        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ delay: 0.6, duration: 0.8 }}
          className='space-y-6'
        >
          {[
            {
              title: '1. Thu Thập Thông Tin',
              content:
                'Chúng tôi có thể thu thập thông tin cá nhân như tên, email, số điện thoại khi bạn đăng ký tài khoản hoặc đặt hàng.'
            },
            {
              title: '2. Sử Dụng Thông Tin',
              content:
                'Thông tin của bạn được sử dụng để xử lý đơn hàng, cung cấp dịch vụ và nâng cao trải nghiệm người dùng.'
            },
            {
              title: '3. Bảo Mật Dữ Liệu',
              content:
                'Chúng tôi sử dụng các biện pháp bảo mật tiên tiến để bảo vệ dữ liệu cá nhân của bạn khỏi truy cập trái phép.'
            },
            {
              title: '4. Quyền Riêng Tư',
              content: 'Bạn có quyền truy cập, chỉnh sửa hoặc yêu cầu xóa thông tin cá nhân của mình bất kỳ lúc nào.'
            }
          ].map((item, index) => (
            <motion.div
              key={index}
              initial={{ opacity: 0, x: -30 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.2 * index, duration: 0.8 }}
              className='p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-200'
            >
              <h2 className='text-2xl font-semibold text-gray-900'>{item.title}</h2>
              <p className='text-gray-700 mt-2 text-lg'>{item.content}</p>
            </motion.div>
          ))}
        </motion.div>
      </motion.div>
    </div>
  )
}
