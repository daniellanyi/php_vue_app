
import RootState, { ColorTheme, Loc } from "./types";




export default {
    changeColorTheme(state: RootState, newTheme: ColorTheme) {
        state.theme = newTheme;
    },
    changeLanguage(state: RootState, language: Loc) {
        state.language = language;
    },
    setCSRFToken(state: RootState, CSRFToken: string) {
        state.CSRFToken = CSRFToken;
    }
}