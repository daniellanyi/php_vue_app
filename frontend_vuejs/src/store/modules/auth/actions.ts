import { LoginPayload, emailVerificationPayload, completeSignUpPayload, EmailPayload, passwordPayload, testPayload } from "./types";
import { ActionContext } from "vuex";
import { AuthState } from "./types";
import { FetchService, HTTPError } from "@/services/fetchService";
import { CONSTANTS } from "@/constants";
import RootState from "@/store/types";
import router from "@/router";




let timer: number | undefined;
export default {

    async login(context: ActionContext<AuthState, RootState>, payload: LoginPayload) {
        const response = await FetchService.post(CONSTANTS.APIRoutes.authRoutes.login ,payload);
        
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 401:
                    userMessage = 'server.error_invalid_credentials';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            const error = new Error(userMessage);
            throw error;
        } else {
            const responseData = await response.json();
            context.dispatch('setCSRFToken', responseData.CSRFToken, {root: true});
            context.dispatch('auth', responseData);
            
        }   
    },
    async signUp(context: ActionContext<AuthState, RootState>, payload: EmailPayload) {
        const response = await FetchService.post(CONSTANTS.APIRoutes.authRoutes.signup ,payload);
        
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 409:
                    userMessage = 'server.error_email_conflict';
                    break;
                case 500:
                    userMessage = 'server.error_email_internal';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            throw Error(userMessage);
        } else {
            const responseData = await response.json();
            context.commit('setUser', {
                userId: null,
                username: null,
                userEmail: responseData.email,
                sessionExpiry: null,
            })
        }
    },
    async checkAuthenticationState(context: ActionContext<AuthState, RootState>) {
        const response = await FetchService.get(CONSTANTS.APIRoutes.authRoutes.authenticate);
        
        if (!response.ok) {
            const message = await response.text();
            context.commit('endLoading');
            console.log(message || 'Failed to authenticate');
        } else {
            const responseData = await response.json();
            context.dispatch('setCSRFToken', responseData.CSRFToken, {root: true});
            context.dispatch('auth', responseData);
        }
        

    },


    async auth(context: ActionContext<AuthState, RootState>, payload: AuthState) {

        const expiresAt = +payload.sessionExpiry!*1000;
        context.commit('setUser', {
            userId: payload.userId!,
            username: payload.username!,
            userEmail: payload.userEmail!,
            sessionExpiry: expiresAt
        });
        context.commit('endLoading');
        const expiresIn = expiresAt - Date.now();
        
        console.log(`Session expires in ${expiresIn} miliseconds`);
        timer = setTimeout(function() {
            context.dispatch('logout');
        }, expiresIn);
    },

    async logout(context: ActionContext<AuthState, RootState>) {
        clearTimeout(timer);
        context.commit('setUser', {
            userId: null,
            username: null,
            userEmail: null,
            sessionExpiry: null,
        });
        FetchService.post(CONSTANTS.APIRoutes.authRoutes.logout, {});
        router.push('/');
    },

    async verifyEmail(context: ActionContext<AuthState, RootState>, payload: emailVerificationPayload) {
        const response = await FetchService.post(CONSTANTS.APIRoutes.authRoutes.emailVerification ,payload);
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 403:
                    userMessage = 'server.error_email_verification_no_details';
                    break;
                case 401:
                    userMessage = 'server.error_email_verification_code_invalid';
                    break;
                case 410:
                    userMessage = 'server.error_email_verification_code_expired';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            throw new HTTPError(userMessage, response.status);
        } 
    },

    async resendVerificationCode() {
        const response = await FetchService.get(CONSTANTS.APIRoutes.authRoutes.emailVerificationResendCode);
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 403:
                    userMessage = 'server.error_email_verification_no_details';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            throw new Error(userMessage);
        }
    },

    async completeSignUp(context: ActionContext<AuthState, RootState>, payload: completeSignUpPayload) {
        const response = await FetchService.post(CONSTANTS.APIRoutes.authRoutes.completeSignUp, payload);
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 401:
                    userMessage = 'server.error_email_not_verified';
                    break;
                case 410:
                    userMessage = 'server.error_email_conflict';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            throw new Error(userMessage);
        } else {
            const responseData = await response.json();
            context.dispatch('setCSRFToken', responseData.CSRFToken, {root: true});
            context.dispatch('auth', responseData);
        }
    },

    

    async forgotPassword(context: ActionContext<AuthState, RootState>, payload: EmailPayload) {
        const response = await FetchService.post(CONSTANTS.APIRoutes.authRoutes.forgotPassword, payload);
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 409:
                    userMessage = 'server.error_email_not_found';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            throw new Error(userMessage);
        } else {
            const responseData = await response.json();
            context.commit('setUser', {
                userId: null,
                username: null,
                userEmail: responseData.email,
                sessionExpiry: null,
            })
        }
    },

    async changePassword(context: ActionContext<AuthState, RootState>, payload: passwordPayload) {
        const response = await FetchService.post(CONSTANTS.APIRoutes.authRoutes.changePassword, payload);
        
        if (!response.ok) {
            let userMessage: any;
            switch (response.status) {
                case 401:
                    userMessage = 'server.error_email_not_verified';
                    break;
                default:
                    userMessage = 'server.error_generic';
            }
            throw new Error(userMessage);
        } else {
            const responseData = await response.json();
            context.dispatch('setCSRFToken', responseData.CSRFToken, {root: true});
            context.dispatch('auth', responseData);
        }
    },

    


   

};