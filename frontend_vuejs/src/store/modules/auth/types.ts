export interface AuthState {
    loading: boolean;
    userId: string | null;
    username: string | null;
    userEmail: string | null;
    sessionExpiry: number | null;
    
}

export interface EmailPayload {
    email: string;
}

export interface LoginPayload {
    email: string;
    password: string;
}


export interface emailVerificationPayload {
    code: string;
}


export interface completeSignUpPayload {
    email: string;
    password: string;
}

export interface passwordPayload {
    password: string;
}

export interface testPayload {
    email: string;
    username: string;
    password: string;
}