import { createI18n, useI18n } from 'vue-i18n';
import en from './locales/locales.en.json';



export const i18n = createI18n({
    locale: process.env.VUE_APP_DEFAULT_LOCALE,
    legacy: false,
    fallbackLocale: 'en',
    globalInjection: true,
    messages: {
        en,
    },
    runtimeOnly: false,
})

