<template>
    <div id="layout" >
        <div id="left">
            <slot name="left"></slot>
        </div>
        <div id="main">
            <div></div>
            <slot name="main"></slot>
        </div>
        <div id="right">
            <slot name="right"></slot>
        </div>
       
    </div>
</template>

<style lang="scss" scoped>

#layout {
    min-height: var(--main-layout-min-height);
    overflow-x: hidden;
    display: grid;
    grid-template-areas: 
            'left main right'
    ;
    grid-template-columns: 0fr 6fr 0fr;
    color: #0084ff;
    --font-size-large: 30px;
    --font-size-small: 15px;
}

@media (max-aspect-ratio: 1) {
    #main {
        display: grid;
        grid-template-rows: 1fr 8fr;
    }
}



@media (min-width: 710px) {
    #layout{
        grid-template-columns: 1fr 6fr 1fr;
        --font-size-large: 35px;
    }
}


@media (min-width: 910px) {
    #layout {
        --main-width-910: 600px;
        --side-width-910: calc((100vw - var(--main-width-910))/2);
        
    }
   
    
    
}

@media (min-width: 910px) and (max-width: 1220px) {
    #left{
        width: var(--side-width-910);
    }
    #main{
        width: var(--main-width-910);
    }
    #right{
        width: var(--side-width-910);
    }
}


@media (min-width: 1220px) {
    #layout {
        grid-template-columns: 4fr 8fr 4fr;
    }
    
}


@media (min-width: 1440px) {
    #layout {
        grid-template-columns: 5fr 8fr 5fr;
    }
   
}




#left {
    grid-area: left;
    min-width: 0px;
}

#main {
    grid-area: main;
    
}

#right {
    grid-area: right;
    min-width: 0px;

}


</style>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    
})
</script>