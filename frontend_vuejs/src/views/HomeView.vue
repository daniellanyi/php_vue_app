<template>
  <div class="home-page" >
    <h1>{{ $t('page_title_home') }}</h1>
  </div>
</template>

<style lang="scss">





.home-page{
  display: flex;
  align-items: center;
  flex-direction: column;
  width: 100%;
  h1{
    margin: 40px 0 20px 0;
  }
}


</style>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'HomeView',
});
</script>
