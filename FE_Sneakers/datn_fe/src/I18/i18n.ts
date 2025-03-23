import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'
import HttpBackend from 'i18next-http-backend'

i18n
  .use(HttpBackend)
  .use(initReactI18next)
  .init({
    lng: 'vi', // Ngôn ngữ mặc định là tiếng Việt
    fallbackLng: 'en', // Dự phòng là tiếng Anh
    backend: {
      loadPath: '/locales/{{lng}}/translation.json'
    },
    interpolation: {
      escapeValue: false
    }
  })

export default i18n
