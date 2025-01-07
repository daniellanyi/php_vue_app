import { AuthState } from "./types";



export default {

    isAuthenticated(state: AuthState): boolean {
        console.log(state.sessionExpiry);
        if (state.sessionExpiry == null) return false;
        if (state.sessionExpiry > Date.now()) return true;
        return false;
    },

    userDataLoading(state: AuthState): boolean {
        if (state.loading === false) return true;
        return false;
    },

    getUsername(state: AuthState): string {
        return state.username ?? 'Unkown';
    },

    
    getUserEmail(state: AuthState): string | null {
        return state.userEmail;
    }
};