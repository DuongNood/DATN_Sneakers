// import { useForm } from 'react-hook-form'
// import { useTranslation } from 'react-i18next'

// const EmailSignup = () => {
//   const { t } = useTranslation()
//   const { register, handleSubmit } = useForm()

//   const onSubmit = (data) => {
//     console.log('Email:', data.email)
//     // Gọi API để gửi email
//   }

//   return (
//     <div className='bg-gray-800 py-6 px-4 sm:px-6 md:px-8 flex flex-col md:flex-row items-center justify-center gap-6 w-full'>
//       <form onSubmit={handleSubmit(onSubmit)} className='flex flex-col md:flex-row items-center gap-4 w-full max-w-4xl'>
//         <div className='flex items-center gap-2 text-white'>
//           <FaEnvelope className='text-lg' />
//           <h3 className='text-sm md:text-base font-semibold uppercase'>{t('signup_title')}</h3>
//         </div>
//         <input
//           type='email'
//           placeholder={t('email_placeholder')}
//           className='w-full md:w-96 px-4 py-2 bg-gray-600 text-white placeholder-gray-400 rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400'
//           {...register('email', { required: true })}
//         />
//         <button className='w-full md:w-auto px-6 py-2 bg-blue-500 text-gray-200 rounded-full hover:bg-yellow-500 transition'>
//           {t('signup_button')}
//         </button>
//       </form>
//       <p className='text-white text-sm text-center md:text-right uppercase'>
//         <span
//           dangerouslySetInnerHTML={{
//             __html: t('promotion_text', {
//               1: (chunk) => `<span class='font-semibold'>${chunk}</span>`
//             })
//           }}
//         />
//       </p>
//     </div>
//   )
// }
// export default EmailSignup
