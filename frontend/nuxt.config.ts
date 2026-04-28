export default defineNuxtConfig({
    compatibilityDate: '2025-04-01',
    devtools: { enabled: false },
  
    runtimeConfig: {
      apiBase: 'http://nginx/api',
      public: {
        apiBase: '/api',
      },
    },

    imports: {
      dirs: ['composables/**'],
    },
  
    typescript: {
      strict: true,
      shim: false,
    },
  
    nitro: {
      preset: 'node-server',
    },
  
    app: {
      head: {
        title: 'Сервис управления вакансиями',
        meta: [
          { charset: 'utf-8' },
          { name: 'viewport', content: 'width=device-width, initial-scale=1' },
          { name: 'robots', content: 'noindex, nofollow' },
        ],
      },
    },
  })
  