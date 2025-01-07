export enum TextFieldType {
    USERNAME = 'username',
    DEFAULT = 'default',
    EMAIL = 'email',
    PASSWORD = 'password'
}

export enum FormValidationType {
    DEFAULT = 'default',
    CREATE = 'create'
}

export interface TextFieldState {
    value?: string
    prompt?: textFieldPrompt
}

export type textFieldPrompt = [string, {[key: string]: any}?];