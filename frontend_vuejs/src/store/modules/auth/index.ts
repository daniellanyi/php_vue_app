import mutations from './mutations';
import actions from './actions';
import getters from './getters';
import { AuthState } from './types';


export default {
    namespaced: true,
    state(): AuthState {
        return {
            userId: null,
            userEmail: null,
            username: null,
            sessionExpiry: null,
            loading: true
        };
    },
    mutations,
    actions,
    getters
}