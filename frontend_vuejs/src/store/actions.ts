import { ActionContext } from "vuex";
import RootState, { ColorTheme, Loc } from "./types";
import { StorageService, StorageType } from "@/services/storageService";
import { Trans } from "@/i18n/translations";
import router from "@/router";
import { FetchService } from "@/services/fetchService";
import { CONSTANTS } from "@/constants";




export default {

    async changeLanguage(context: ActionContext<RootState, RootState>, payload: number) {
        const newLocale = Trans.getSupportedLocales[payload]
        Trans.switchLanguage(newLocale);
        try {
            await router.replace({ params: { locale: newLocale } })
          } catch(e) {
            console.log(e)
            router.push("/")
          }
    },

    async syncLocale(context: ActionContext<RootState, RootState>, locale: string) {
        FetchService.post(CONSTANTS.APIRoutes.syncLocale, {
            locale: locale
        })
    },

    async changeColorTheme(context: ActionContext<RootState, RootState>, payload: ColorTheme) {
        context.commit('changeColorTheme' ,payload);
        StorageService.saveUserSettings('colorTheme', payload, StorageType.SESSION);
    },

    async getUserSettings(context: ActionContext<RootState, RootState>) {
        console.log("hello");
        try {
            const userSettings = sessionStorage.getItem('userSettings');
            if(userSettings) {
                const userSettingsJSON = JSON.parse(userSettings);
                context.commit('changeColorTheme' ,userSettingsJSON.colorTheme);
            }

        } catch (error) {
            console.log('Error retrieving user settings from session Storage');
        }
    },

    async setCSRFToken(context: ActionContext<RootState, RootState>, CSRFToken: string) {
        context.commit('setCSRFToken', CSRFToken);
    }
    


}