<template>
    <div class="loader"></div>
</template>

<style lang="scss" >

.loader {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 2;
    margin: 0;
    background-color: var(--background-1-color-1-opacity-1);
}

.loader::after {
    content: "";
    height: var(--spinner-height);
    aspect-ratio: 1/1;
    border: var(--spinner-border);
    border-top-color: var(--background-2-color-1-opacity-1);
    border-radius: 50%;
    animation: loading 1s ease infinite;
}

@keyframes loading {
    from {
        transform: rotate(0turn); 
    }
    to {
        transform: rotate(1turn);
    }
}

</style>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'LoadingSpinner',
    props: {
        height: {
            type: String,
            required: true
        },
        width: {
            type: String,
            required: true
        }
    }
});

</script>