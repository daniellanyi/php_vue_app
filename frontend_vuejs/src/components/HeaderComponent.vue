<template>
    <nav :class="navbarClass">
      <transition name="overlay">
        <div class="overlay" v-if="sidebarOpen"></div>
      </transition>
      <ul class="sidebar" :class="classes" @click.stop>
        <li class="sidebar-close-button" @click="closeSideBar">
          <div class="x-shape"></div>
        </li>
        <li  v-if="sessionChecked && !isLoggedIn" :class="currentRouteClass('/login')" @click="routerLinkOnClick($event, '/login')">
          <router-link  to="/login" >{{ $t('action_sign_in') }}</router-link>
        </li>
        <li  :class="currentRouteClass('/')" @click="routerLinkOnClick($event, '/')">
          <router-link to="/" >{{ $t('page_title_home') }}</router-link>
        </li>
        <li v-if="sessionChecked && isLoggedIn" :class="currentRouteClass('/about')" @click="routerLinkOnClick($event, '/about')">
          <router-link to="/about" >{{ $t('page_title_about') }}</router-link>
        </li>
        <li v-if="sessionChecked" class="submenu">
          <div>
            <drop-down-slide
              :dropdownConfig="sidebarSelectThemeDropdownConfig"
              :styles="{
                listItemPadding: '1em 1.5em',
                listItemHeight: '1em',
                backgroundColors: [
                  'var(--background-1-color-4)',
                  'var(--background-2-color-3)',
                  'var(--background-2-color-2)'
                ],
                timeToOpen: 0.5
              }"
            ></drop-down-slide>
          </div>
          
        </li>
        <li v-if="sessionChecked" class="submenu">
          <div>
            <drop-down-slide
              :dropdownConfig="sidebarSelectLanguageDropdownConfig"
              :styles="{
                listItemPadding: '1em 1.5em',
                listItemHeight: '1em',
                backgroundColors: [
                  'var(--background-1-color-4)',
                  'var(--background-2-color-3)',
                  'var(--background-2-color-2)'
                ],
                timeToOpen: 0.5
              }"
            ></drop-down-slide>
          </div>
          
        </li>
        <li v-if="sessionChecked && isLoggedIn" @click.prevent="logout">
          {{ $t('action_logout') }}
        </li>
      </ul>
      <ul class="horizontal-list">
        <li class="side-bar-menu-button" @click.stop="openSideBar">
          <div class="ham-menu">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </li>
        <div class="header-list-container">
          <ul class="header-list-1">
            <li v-if="sessionChecked" class="hide-on-mobile" :class="currentRouteClass('/')">
              <router-link to="/"><h1>{{ $t('page_title_home') }}</h1></router-link>
            </li>
            <li v-if="sessionChecked && isLoggedIn" class="hide-on-mobile" :class="currentRouteClass('/about')">
              <router-link to="/about"><h1>{{ $t('page_title_about') }}</h1></router-link>
            </li>
          </ul>
          <ul class="header-list-2">
            <li v-if="sessionChecked && !isLoggedIn" class="hide-on-mobile sign-up-nav">
              <ul class="onboarding-links">
                <li :class="currentRouteClass('/login')">
                  <router-link class="login-link" to="/login">{{ $t('action_sign_in') }}</router-link>
                </li>
              </ul>
            </li>
            <li v-if="sessionChecked && !isLoggedIn" class="hide-on-mobile">
              <drop-down
                :dropdownConfig="selectThemeDropdownConfig"
                :styles="{
                  listItemPadding: '0.5em',
                  listItemHeight: '1.1em',
                  listItemMinWidth: '6em',
                  backgroundColors: [
                    'var(--background-1-color-3)',
                    'var(--background-2-color-3)',
                    'var(--background-2-color-2)'
                  ],
                  timeToOpen: 0.2
                }"
              ></drop-down>
            </li>
            <li v-if="sessionChecked && !isLoggedIn">
              <drop-down
                :dropdownConfig="selectLanguageDropdownConfig"
                :styles="{
                  listItemPadding: '0.5em',
                  listItemHeight: '1.1em',
                  listItemMinWidth: '6em',
                  backgroundColors: [
                    'var(--background-1-color-3)',
                    'var(--background-2-color-3)',
                    'var(--background-2-color-2)'
                  ],
                  timeToOpen: 0.2
                }"
              ></drop-down>
            </li>
            <li v-if="sessionChecked && isLoggedIn" class="hide-on-mobile user-control-menu">
              <drop-down
                :dropdownConfig="userDropdownConfig"
                :styles="{
                  listItemPadding: '0.5em',
                  listItemHeight: '1.1em',
                  listItemMinWidth: '6em',
                  backgroundColors: [
                    'var(--background-1-color-3)',
                    'var(--background-2-color-3)',
                    'var(--background-2-color-2)'
                  ],
                  timeToOpen: 0.2
                }"
              ></drop-down>
              
            </li>
          </ul>
        </div>
      </ul>
    </nav>
</template>


<style lang="scss" scoped>
.login-link {
    display: block;
    padding: 0.5em 1em 0.5em 1em;
    background-color: var(--highlight-color-1);
    color: var(--text-color-1);
    font-weight: bold;
    
}

.login-link:hover{
    color: var(--text-color-1-hover);
  }

.current-route {
  .login-link {
    color: var(--text-color-1-hover);
  }
  a {
    color: var(--text-color-1-selected);
  }
}


.onboadring-links {
    display: flex;
}

.user-control-menu {
  align-self: flex-start;
}



.x-shape {
    
    position: relative;
    height: 20px;
    width: 20px;
}


.x-shape::before,
.x-shape::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 10%;
    background-color: var(--text-color-1);
    transform-origin: center;
}

.x-shape::before {
    transform: translate(-50%, -50%) rotate(45deg);
}

.x-shape::after {
    transform: translate(-50%, -50%) rotate(-45deg);
}

@keyframes slideIn {
    from {
        transform: translateX(-100%); 
    }
    to {
    transform: translateX(0); 
    }
}
@keyframes slideOut {
    from {
        display: block;
        transform: translateX(0%); 
    }
    to {
        display: none;
        transform: translateX(-100%); 
    }
}



nav {
    height: var(--navbar-height);
    background-color: var(--background-1-color-4);
}



nav .sidebar {
    display: flex;
    height: 100vh;
    padding: 0;
    max-width: max-content;
    list-style: none;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    display: none;
    background-color: var(--background-1-color-4);
}


.sidebar > li:not(.submenu) {
    color: var(--text-color-1);
    padding: 1em 1.5em;
}

.sidebar > li {
  font-size: x-large;
  font-weight: bold;
  text-align: left;
}

.sidebar > li:hover{
    color: var(--text-color-1-hover);
  } 


.sidebar > li:first-of-type {
    padding: 1em;
}

.sidebar > li:first-of-type:hover{
  background-color: var(--background-2-color-3);
}

.side-bar-menu-button:active .ham-menu span {
    background: linear-gradient(90deg, var(--background-2-color-2) 10%, 
    var(--background-2-color-3) 90%);
}

.horizontal-list {
    margin: 0;
    height: inherit;
    display: grid;
    list-style: none;
    width: 100vw;
    padding: 0;
}

.sidebar-close-button{
  align-self: center;
  
}

.header-list-container {
  justify-self: center;
  position: relative;
  display: flex;
  flex-direction: column-reverse;
  width: 50%;
  height: 100%;
  ul {
    display: flex;
    margin: 0;
    align-items: start;
    padding: 0;
    list-style-type: none;
  }
}

.header-list-1 {
  margin: 10px;
  height: 50%;
  width: 100%;
  justify-content: start;
  gap: 4em;
  position: absolute;
  top: 50%;
  >li:hover{
    color: var(--text-color-1-hover);
  }
  >li{
    color: var(--text-color-1);
  }
}

.header-list-2 {
  --dropdown-min-width: 90px;
  height: 50%;
  width: 100%;
  justify-content: end;
  top: 0;
  position: absolute;

}




nav a {
  text-decoration: none;
  color: inherit;
  font-size: inherit;
  h1 {
    margin: 0;
  }
}


.ham-menu {
    height: 50px;
    width: 40px;
    margin-left: auto;
    position: relative;
}

.ham-menu span {
    height: 5px;
    width: 100%;
    border-radius: 25px;
    position: absolute;
    z-index: 0;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    transition: .3s ease;
    background: linear-gradient(90deg, var(--background-2-color-1) 10%, 
    var(--background-2-color-3) 90%);
}

.ham-menu span:nth-child(1) {
    top: 25%;
}
.ham-menu span:nth-child(3) {
    top: 75%;
}





.overlay {
  top: 0;
  left: 0;
  position: absolute;
  z-index: 1;
  width: 100vw;
  height: 100vh;
  background-color: black;
  opacity: 0.3;
}

.overlay-enter-from {
  opacity: 0;
}

.overlay-enter-active {
  transition: all 0.2s ease-in;
}


.overlay-leave-active {
  transition: all 0.2s ease-in;
}

.overlay-leave-to {
  opacity: 0;
}


.sign-up-nav {
    align-self: flex-start;
}




.side-bar-menu-button {
    display: none;
    padding-left: 0.5em;
    padding-right: 0.5em;
}


nav .open-sidebar {
    display: block;
    animation: slideIn 0.25s ease-out;
}

nav .close-sidebar {
    animation: slideOut 0.25s ease-out;
  }



@media (max-width: 910px) {

    nav ul {
        justify-content: flex-start;
        margin: 0;
    }

    .navbar-mobile {
      position: fixed;
      height: var(--navbar-height-mobile);
      z-index: 1;
    }

    nav li {
        cursor: pointer;
    }


    nav li:hover .x-shape::after  {
        background-color: var(--text-color-3);
    }

    nav li:hover .x-shape::before  {
        background-color: var(--text-color-3);
    }

    nav {
        width: 100%;
    }

    nav .sidebar {
      position: absolute;
      z-index: 1;
    }

    .header-list-container{
      display: none;
    }

    .side-bar-menu-button {
        display: block;
        align-self: center;
    }
    
    .hide-on-mobile{
        display: none;
    }

}

@media (min-width: 910px) {
    nav .sidebar {
        display: none;
    }
    .overlay {
      display: none;
    }
}
</style>


<script lang="ts">
import { defineComponent } from 'vue';
import { DropDownListNode, OpeningMode, TriggerMode } from './ui/DropDown.vue';
import { DropDownListMenu, DropListItem } from './ui/DropdownSlide.vue';
import { Trans } from '@/i18n/translations';
  export default defineComponent({
    name: 'HeaderComponent',
    data() {
      return {
        sidebarOpen: false,
      }
    },
    computed: {
      classes(): Record<string, boolean> {
        return {
          'open-sidebar': this.sidebarOpen,
          'close-sidebar': !this.sidebarOpen
        }
      },
      navbarClass(): Record<string, boolean> {
        return {
          'navbar-mobile':true,
          'navbar-open-sidebar': this.sidebarOpen
        }
      },
      isLoggedIn(): boolean {
        return this.$store.getters['auth/isAuthenticated'];
      },
      getUsername(): string {
        return this.$store.getters['auth/getUsername'];
      },
      sessionChecked() : boolean {
        return this.$store.getters['auth/userDataLoading'];
      },
      getTheme(): number {
        return this.$store.getters['getColorTheme'];
      },
      localeChoices(): DropListItem[] {
        const locales = [];
        for (let locale of Trans.getSupportedLocales) {
          locales.push({
            id: locale,
            label: this.$t(`locale.${locale}`)
          } as DropListItem)
        }
        return locales;
      },
      getLocale():number {
        return Trans.getSupportedLocales.indexOf(Trans.currentLocale);
      },
      sidebarSelectThemeDropdownConfig(): DropDownListMenu {
        return {
          id: 'theme-dropdown',
          label: this.$t('dropdown_title_theme'),
          trigger: TriggerMode.CLICK,
          isSelect: true,
          action: 'changeColorTheme',
          selectedItemIdx: this.getTheme,
          hasCaret: true,
          children: [
            {
              id: 'theme-dark',
              label: this.$t('theme_name_dark'),
              
            },
            {
              id: 'theme-light',
              label: this.$t('theme_name_light')
            }
          ],
        }
      },
      sidebarSelectLanguageDropdownConfig(): DropDownListMenu {
        return {
          id: 'language-dropdown',
          label: this.$t('dropdown_title_language'),
          trigger: TriggerMode.CLICK,
          isSelect: true,
          action: 'changeLanguage',
          selectedItemIdx: this.getLocale,
          hasCaret: true,
          children: this.localeChoices
        }
      },
      sidebarUserDropdownConfig(): DropDownListNode {
        return {
          id: 'user-drop-down-sidebar',
          label: this.getUsername,
          trigger: TriggerMode.CLICK,
          hasCaret: true,
          children: [
            this.sidebarSelectThemeDropdownConfig,
            {
              id: 'logout',
              label: this.$t('action_logout'),
              action: 'auth/logout'
            }
          ]
        }
      },
      selectThemeDropdownConfig(): DropDownListNode {
        return {
          id: 'user-drop-down',
          label: this.$t('dropdown_title_theme'),
          trigger: TriggerMode.HOVER,
          opening: OpeningMode.FADE_IN_BELOW,
          isSelect: true,
          action: 'changeColorTheme',
          selectedItemIdx: this.getTheme,
          hasCaret: true,
          closeOnListItemClicked: true,
          children: [
            {
              id: 'theme-dark',
              label: this.$t('theme_name_dark'),
            },
            {
              id: 'theme-light',
              label: this.$t('theme_name_light')
            }
          ]
        }
      },
      selectLanguageDropdownConfig(): DropDownListNode {
        return {
          id: 'user-drop-down',
          label: this.$t('dropdown_title_language'),
          trigger: TriggerMode.HOVER,
          opening: OpeningMode.FADE_IN_BELOW,
          isSelect: true,
          action: 'changeLanguage',
          selectedItemIdx: this.getLocale,
          hasCaret: true,
          closeOnListItemClicked: true,
          children: this.localeChoices
        }
      },
      userDropdownConfig(): DropDownListNode {
        return {
          id: 'user-drop-down',
          label: this.getUsername,
          trigger: TriggerMode.CLICK,
          opening: OpeningMode.FADE_IN_BELOW,
          hasCaret: true,
          children: [
            {
              id: 'theme-dropdown',
              label: this.$t('dropdown_title_theme'),
              trigger: TriggerMode.HOVER,
              opening: OpeningMode.FADE_IN_LEFT,
              isSelect: true,
              action: 'changeColorTheme',
              selectedItemIdx: this.getTheme,
              closeOnListItemClicked: true,
              children: [
                {
                  id: 'theme-dark',
                  label: this.$t('theme_name_dark'),
                  
                },
                {
                  id: 'theme-light',
                  label: this.$t('theme_name_light')
                }
              ],
            },
            {
              id: 'theme-dropdown',
              label: this.$t('dropdown_title_language'),
              trigger: TriggerMode.HOVER,
              opening: OpeningMode.FADE_IN_LEFT,
              isSelect: true,
              action: 'changeLanguage',
              selectedItemIdx: this.getLocale,
              closeOnListItemClicked: true,
              children: this.localeChoices
            },
            {
              id: 'logout',
              label: this.$t('action_logout'),
              action: 'auth/logout'
            }
          ]

        }
      },
    },
    methods: {
      closeSideBar() {
        this.sidebarOpen = false;
        document.body.style.overflow = "auto";
      },
      openSideBar() {
        this.sidebarOpen = true;
        document.body.style.overflow = "hidden";
        window.addEventListener('click', this.closeSideBar);
      },
      currentRouteClass(link:string): Record<string, boolean> {
        let isCurrentRoute: boolean;
       
        if (link === this.$route.path) isCurrentRoute = true;
        else{
          const locale: string = this.$route.params.locale as string;
          const pathWithoutLocale = this.$route.path.replace(locale, '').replace('//', '/')
          if (link === pathWithoutLocale) isCurrentRoute = true;
          else isCurrentRoute = false;
        }
        return {
          'current-route': isCurrentRoute
        }
      },
      routerLinkOnClick(event: MouseEvent,path: string) {
        if (this.$route.path === path) return;
        const target = event.target as HTMLLIElement;
        const link = target.firstElementChild as HTMLAnchorElement;
        if (link) link.click();
        this.closeSideBar();
      },
      logout() {
        this.$store.dispatch('auth/logout')
      }
    },

    
    beforeUnmount() {
      window.removeEventListener('click', this.closeSideBar);
    }
  });
</script>


