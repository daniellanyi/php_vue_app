import { nextTick } from "vue";
import { i18n } from ".";
import { StorageService, StorageType } from "@/services/storageService";
import { NavigationGuardNext, RouteLocationNormalized } from "vue-router";
import store from "@/store";

export const Trans = {
    get getSupportedLocales(): string[] {
        return process.env.VUE_APP_SUPPORTED_LOCALES.split(',');
    },

    get defaultLocale() {
        return process.env.VUE_APP_DEFAULT_LOCALE;
    },

    get currentLocale() {
        return i18n.global.locale.value
    },
    set currentLocale(newLocale) {
        i18n.global.locale.value = newLocale;
    },

    isLocaleSupported(locale: string) {
        return Trans.getSupportedLocales.includes(locale)
    },


    getUserLocale() {
        const locale: any = window.navigator.language ||
            Trans.defaultLocale
        
        return {
            locale: locale,
            localeNoRegion: locale.split('-')[0]
        }
    },

    getPersistedLocale() {
        const persistedLocale = StorageService.getUserSettings('user-locale', StorageType.LOCAL);
        if (!persistedLocale) return null;
        if(Trans.isLocaleSupported(persistedLocale)) {
            return persistedLocale
        } else {
            return null;
        }
    },

    guessDefaultLocale() {
        const userPersistedLocale = Trans.getPersistedLocale()
        if (userPersistedLocale){
            return userPersistedLocale;
        }
        const userPreferredLocale = Trans.getUserLocale() 

        if (Trans.isLocaleSupported(userPreferredLocale.locale)) return userPreferredLocale.locale;
        if (Trans.isLocaleSupported(userPreferredLocale.localeNoRegion)) return userPreferredLocale.localeNoRegion;
        return Trans.defaultLocale;
    },

    async switchLanguage(newLocale: any) {
        await Trans.loadLocaleMessages(newLocale);
        Trans.currentLocale = newLocale;
        document.querySelector("html")!.setAttribute("lang", newLocale);
        StorageService.saveUserSettings('user-locale', newLocale, StorageType.LOCAL);
        store.dispatch('syncLocale', newLocale);
    },

    async loadLocaleMessages(locale: string) {
        if (!i18n.global.availableLocales.includes(locale)) {
            const messages = await import(`@/i18n/locales/locales.${locale}.json`);
            i18n.global.setLocaleMessage(locale, messages.default);
        }

        return nextTick();
    },

    async routeMiddleware(to:RouteLocationNormalized, _from:RouteLocationNormalized, next:NavigationGuardNext) {
        const paramLocale = to.params.locale as string;
        if (!paramLocale){
            await Trans.switchLanguage(Trans.guessDefaultLocale());
            return next();
        }
        if (!Trans.isLocaleSupported(paramLocale)) {
            
            const newPath = to.fullPath.replace(`/${paramLocale}`, `/${Trans.guessDefaultLocale()}`)
            return (next(newPath))
        }
        await Trans.switchLanguage(paramLocale);
        
        return next();
    },

    i18nRoute(to: RouteLocationNormalized) {
        return {
          ...to,
          params: {
            locale: Trans.currentLocale,
            ...to.params
          }
        }
      }
}