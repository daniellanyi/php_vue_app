import { AuthState } from "./types";

export default {

    setUser(state: AuthState, payload: AuthState) {
        state.userId = payload.userId;
        state.username = payload.username;
        state.userEmail = payload.userEmail;
        state.sessionExpiry = payload.sessionExpiry;
        
    },
    endLoading(state: AuthState) {
        state.loading = false;
    },
    


};