import RootState, { Loc } from "./types";

export default {

   
    getColorTheme(state: RootState) {
        return state.theme;
    },
    getCurrentLanguage(state: RootState) {
        console.log(Loc[state.language]);
        return Loc[state.language];
    },
    getCSRFToken(state: RootState): string | null{
            return state.CSRFToken || null;
    },
}