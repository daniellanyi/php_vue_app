export default interface RootState {
    allowedModules: string[],
    theme: ColorTheme,
    language: Loc,
    CSRFToken: string | null;
}

export enum Loc {
    'en'=0,
    'fr'=1,
    'de'=2
}

export enum ColorTheme {
    DARK = 0,
    LIGHT = 1,
  }


