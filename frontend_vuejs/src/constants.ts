export class CONSTANTS {

    public static APIRoutes: any = {
        authRoutes: {
            authenticate:'/api/auth/session',
            signup: `/api/auth/signup`,
            completeSignUp: '/api/auth/complete-signup',
            login: `/api/auth/login`,
            logout: '/api/auth/logout',
            forgotPassword: '/api/auth/forgot-password',
            changePassword: '/api/auth/change-password',
            emailVerification: '/api/auth/verify-email',
            emailVerificationResendCode: '/api/auth/resend-verification-code'
        },
        syncLocale: '/api/locale'
        
        
    }
}


