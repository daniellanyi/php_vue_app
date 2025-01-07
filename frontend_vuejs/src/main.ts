import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import MainLayout from '@/layouts/MainLayout.vue';
import HeaderComponent from '@/components/HeaderComponent.vue';
import FormBuilder from '@/components/FormBuilder.vue';
import TextField from './components/ui/TextField.vue';
import Button from './components/ui/Button.vue';
import CodeVerification from './components/CodeVerification.vue';
import LoadingSpinner from './components/ui/LoadingSpinner.vue';
import DropDown from './components/ui/DropDown.vue';
import DropdownSlide from './components/ui/DropdownSlide.vue';
import { i18n } from './i18n';




const app = createApp(App);
app.use(store);
app.use(router);
app.component('main-layout', MainLayout)
app.component('form-builder', FormBuilder)
app.component('header-component', HeaderComponent)
app.component('text-field', TextField)
app.component('button-1', Button)
app.component('code-verification', CodeVerification)
app.component('loading-spinner', LoadingSpinner)
app.component('drop-down', DropDown)
app.component('drop-down-slide', DropdownSlide)
app.use(i18n)
app.mount('#app')