import { createStore } from 'vuex'
import authModule from './modules/auth/index';
import actions from './actions';
import RootState, { ColorTheme, Loc } from './types';
import getters from './getters';
import mutations from './mutations';


export default createStore({
  state(): RootState {{
    return {
      allowedModules: [],
      theme: ColorTheme.DARK,
      language: Loc.en,
      CSRFToken: null
    }
  }},
  getters: getters,
  mutations: mutations,
  actions: actions,
  modules: {
    auth: authModule
  }
})



  
