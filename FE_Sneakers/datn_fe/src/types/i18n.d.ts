declare module 'i18next' {
  interface CustomTypeOptions {
    defaultNS: 'translation'
    resources: {
      translation: typeof import('../../public/locales/en/translation.json')
    }
  }
}
