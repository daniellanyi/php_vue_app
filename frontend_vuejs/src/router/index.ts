import { createRouter, createWebHistory, NavigationGuardNext, RouteLocationNormalized, RouteRecordRaw, RouterView } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import store from '@/store'
import { Trans } from '@/i18n/translations'

const routes: Array<RouteRecordRaw> = [
  { 
    path: "/:locale?",
    beforeEnter: Trans.routeMiddleware,
    children: [{
      path: '',
      name: 'home',
      component: () => import(/* webpackChunkName: "about" */ '../views/HomeView.vue')
    },
    {
      path: 'login',
      name: 'login',
      meta: {guest: true},
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ '../views/LoginView.vue')
    },
    {
      path: 'register',
      name: 'register',
      meta: {guest: true},
      component: () => import(/* webpackChunkName: "about" */ '../views/RegisterView.vue')
    },
    {
      path: 'forgot-password',
      name: 'forgot-password',
      meta: {guest: true},
      component: () => import(/* webpackChunkName: "about" */ '../views/ForgotPassword.vue')
    },
    {
      path: 'about',
      name: 'about',
      meta: {auth: true},
      // route level code-splitting
      // this generates a separate chunk (about.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
    }]
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})



router.beforeEach(function(to, from, next) {
  if (routeInvalid(to)) next(false)
  
  else next();

});



function routeInvalid(route: RouteLocationNormalized): boolean {
  if (!route.meta) return false;
  if (route.meta.auth && !store.getters['auth/isAuthenticated']) {
    return true;
  } else if (route.meta.guest && store.getters['auth/isAuthenticated']) {
    return true;
  }
  return false;
}


export default router
