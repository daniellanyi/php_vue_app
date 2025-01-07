<template>
  <div :class="themeClass">
      <header-component></header-component>
      <main-layout>
      <template #left>
      </template>
      <template #main>
        <router-view/>
      </template>
      <template #right>
      </template>
    </main-layout>
  </div>
</template>

<style lang="scss">






.light-mode {
  --background-1-color-1: #d8d8eb;
  --background-1-color-1-opacity-1: #d8d8eb99;
  --background-1-color-2: #e4e6f8;
  --background-1-color-3: #f0f1f8;
  --background-1-color-4: #c8c8e6;
  --background-1-color-4-hover: #B1B3C4;
  --background-1-color-5: #e8e9fd;
  --background-2-color-1: #A2C6FF;
  --background-2-color-1-opacity-1: #A2C6FF66;
  --background-2-color-2: #66A9FF;
  --background-2-color-3: #6A8DFF;
  --border-color-1: #9b9bce;
  --text-color-1: #535772;
  --text-color-1-hover: #294150;
  --text-color-1-selected: #1100ff;
  --text-color-2: #1100ff;
  --text-color-3: #294150;
  --box-shadow-color-1: #6A8DFF;
  --disabled-text-color: rgb(44, 36, 87);
  --highlight-color-1: rgb(205, 248, 205);
  --error-color: red;
  --error-flash-color: rgb(243, 178, 178);

}

.dark-mode {
  --background-1-color-1: #252536;
  --background-1-color-1-opacity-1: #25253699; 
  --background-1-color-2: #2b2b3d;
  --background-1-color-3: #1a1a46;
  --background-1-color-4: #141427;
  --background-1-color-4-hover: #4f4f6b;
  --background-1-color-5: #4f4f6b;
  --background-2-color-1: #0084ff;
  --background-2-color-1-opacity-1: #0084ff66;
  --background-2-color-2: #0263be;
  --background-2-color-3: #013c74;
  --background-2-color-3-opacity-0:#013c7400;
  --background-2-color-3-opacity-30:#013c744D;
  --border-color-1: #252536;
  --text-color-1: rgb(204, 202, 202);
  --text-color-1-hover: white;
  --text-color-1-selected: #0084ff;
  --text-color-2: #0084ff;
  --text-color-3: white;
  --box-shadow-color-1: #787896;
  --disabled-text-color: rgb(204, 202, 202);
  --highlight-color-1: green;
  --error-color: red;
  --error-flash-color: rgb(243, 178, 178);
  
}


.theme{
  
  background: linear-gradient(60deg, var(--background-1-color-3) 10%, 
    var(--background-1-color-2) 40%,
    var(--background-1-color-1) 80%);
    min-height: 100vh;
}

body {
  max-width: 100vw;
  min-height: 100vh;
  overflow-x: hidden;
  margin: 0;
  

}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;

  --navbar-height-mobile: 60px;
  --navbar-height: 100px;
  --main-layout-min-height: calc(100vh - var(--navbar-height));


  @media(max-width: 910px) {
    --main-layout-min-height: calc(100vh - var(--navbar-height-mobile));
  }
}

</style>

<script lang="ts">
  import { defineComponent } from 'vue';
  import { ColorTheme } from './store/types';


 

  export default defineComponent({
    
    computed: {
      themeClass(): Record<string, boolean> {
        return {
          'light-mode': this.isColourLight,
          'dark-mode': this.isColourDark,
          'theme': true
        }
      },
      isColourLight(): boolean {
        return this.$store.getters['getColorTheme'] === ColorTheme.LIGHT
      },
      isColourDark(): boolean {
        return this.$store.getters['getColorTheme'] === ColorTheme.DARK
      },
    },
    created() {
      this.$store.dispatch('auth/checkAuthenticationState');
      this.$store.dispatch('getUserSettings');
    }
  }); 


</script>