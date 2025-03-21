// src/i18n.ts
import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'
import LanguageDetector from 'i18next-browser-languagedetector'

// Định nghĩa các bản dịch
const resources = {
  en: {
    translation: {
      welcome: 'Welcome to my app',
      greeting: 'Hello, {{name}}!'
    }
  },
  vi: {
    translation: {
      welcome: 'Chào mừng bạn đến với ứng dụng của tôi',
      greeting: 'Xin chào, {{name}}!'
    }
  }
}

i18n
  .use(LanguageDetector) // Sử dụng detector để phát hiện ngôn ngữ
  .use(initReactI18next) // Kết nối với React
  .init({
    resources,
    fallbackLng: 'vi', // Ngôn ngữ mặc định nếu không phát hiện được
    interpolation: {
      escapeValue: false // React đã tự động escape
    }
  })

export default i18n
