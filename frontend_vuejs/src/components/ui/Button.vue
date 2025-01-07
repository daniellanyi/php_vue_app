<template>
    <div class="button-box">
        <button class="button" type="submit" :disabled="buttonDisabledState" @click.prevent="onClick">
            {{ text }}
        </button>
    </div>
</template>

<style lang="scss" scoped>
    .button {
        font-size: var(--button-1-font-size , medium);
        padding: var(--button-1-padding ,0.5em);
        background-color: var(--background-1-color-5);
        color: var(--text-color-3);
        cursor: pointer;
        border: 0px solid white;
        box-shadow: 0 0 var(--button-1-inset-shadow, 3px) 0px var( --box-shadow-color-1) inset;
        
    }

    .button:active {
        box-shadow: 0 0 var(--button-1-inset-shadow-active, 1px) 0px var(--box-shadow-color-1) inset;
    }

    .button:disabled {
        color: var(--disabled-text-color);
        opacity: 0.4;
        cursor: default;
    }
</style>
    
<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent(
    {
        name: "ButtonPrimary",
        data() {
            return {
                
            }
        },
        props: {
            text : {
                type: String,
                required: true
            },
            buttonDisabledState: {
                type: Boolean,
                required: false
            }
        },
        methods: {
            onClick() {
                this.$emit('click');
            }
        },
        emits: {
            'click': function(): boolean {
                return true;
            }
    
        }

    }
)

</script>