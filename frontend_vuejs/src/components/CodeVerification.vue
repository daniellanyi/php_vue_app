<template>
    <form class="form-card" autocomplete="new-password" @submit.prevent="resendCode">
        <slot name="above"></slot>
        <div>
            <p  class="error-text">{{ $t(errorPrompt) }}</p>
            <div class="wrapper">
                <loading-spinner v-if="loadingResponse"></loading-spinner>
                <div :class="containerClasses" aria-labelledby="code-verification" ref="container" @mousedown.prevent="containerMousedown">
                    <div v-for="idx in NOChars" 
                        class="input-wrapper"
                        :key="idx"
                    >
                        <input autocomplete="new-password" type="text" @input="handleInput($event, idx -1)" @keydown="handleKeyDown($event, idx - 1)" :class="focusable(idx - 1)">
                        
                    </div>
                </div>
            </div>
            <div class="button-wrapper">
                <button-1 v-if="showResend" :text="$t('prompt_email_check_resend')" @click="resendCode"></button-1>
            </div>
        </div>
    </form>
</template>


<style lang="scss" scoped>

.form-card {
    padding: 1em;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: var(--background-1-color-1);
    gap: 1em;
    label {
        font-size: x-large;
        padding: 0.2em;
    }
    button{
        font-size: x-large;
    }
    
}

.wrapper {
    position: relative;
    --spinner-height: 40%;
    --spinner-border: 6px solid var(--text-color-2);
}

.button-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 3em;
}

.container {
    display: flex;
    width: min-content;
    background-color: var(--background-1-color-5);
    border: 2px solid var(--border-color-1);
    cursor: text;
    
}

.error-text {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    color: var(--error-color);
    min-height: 2.5em;
}

.error-border {
    border: 2px solid var(--error-color);
}

button-1 {
    margin: 0;
}

.input-wrapper {
    padding: var(--code-wrapper-padding, 1em);
    font-size: var(--code-verification-font-size, xx-large);
    input{
        font-size: inherit;
        background-color: inherit;
        border: none;
        width: 1em;
        color: var(--text-color-3);
        border-bottom: 2px solid var(--text-color-3);
        text-align: center;
    }
    input:focus {
        outline: none;
        border: none;       
        box-shadow: none;
        border-bottom: 2px solid var(--text-color-3);
    }
}


.unfocusable {
    pointer-events: none;
}





</style>


<script lang="ts">


import { defineComponent } from 'vue';
import { HTTPStatusCode } from '@/services/fetchService';

    export default defineComponent({
        name: 'CodeVerification',
        data() {
            return {
                currentFieldIdx: 0,
                loadingResponse: false,
                showResend: false,
                errorPrompt: ''
            }
        },
        props: {
            NOChars: {
                type: Number,
                required: true
            },
            
            action: {
                type: String,
                required: true
            },
            resendAction: {
                type: String,
                required: true
            },
            
        },
        computed: {
            containerClasses(): Record<string, boolean> {
                return {
                    "container": true,
                    "error-border": this.errorPrompt.length > 0
                }
            },
            error(): boolean {
                return this.errorPrompt.length > 0;
            },
            
           
        },
        methods: {
            async handleSubmit(): Promise<void> {
                const containerRef = this.$refs.container as HTMLDivElement;
                let code = '';
                for (let child of containerRef.children) {
                    code += child.querySelector('input')?.value;
                }
                this.loadingResponse = true;
                try {
                    
                    await this.$store.dispatch(this.$props.action, {
                        'code' : code
                    });
                    this.$emit('success');
                    
                } catch (error: any) {
                    if (error.statusCode === HTTPStatusCode.GONE) {
                        this.showResend = true;
                    }
                    this.showResend = true;
                    this.errorPrompt = error.message;
                } finally {
                    this.loadingResponse = false;
                }
                
                
            },
            async resendCode() {
                this.showResend = false;
                this.loadingResponse = true;
                try {
                    await this.$store.dispatch(this.$props.resendAction);
                    this.resetState();
                } catch (error: any) {
                    console.log(error.message);
                    this.showResend = true;
                }
                this.loadingResponse = false;
            },

            resetState() {
                const containerRef = this.$refs.container as HTMLDivElement;
                let input: HTMLInputElement | null;
                for (let i = 0; i < this.NOChars; i++) {
                    input = containerRef.children[i].querySelector('input');
                    input!.value = '';
                }
                this.errorPrompt = '';
                this.currentFieldIdx = 0;
                input = containerRef.children[this.currentFieldIdx].querySelector('input');
                input!.focus();
            },

            handleInput(event: any, idx: number) {
                const value = this.cleanInput(event.target.value) as string;
                
                if (value.length === 0) {
                    event.target.value = value;
                    return
                }
                const containerRef = this.$refs.container as HTMLDivElement;
                let input: HTMLInputElement | null;
                const maxLength = this.NOChars - idx;
                const finalValue = value.slice(0, maxLength);
                let endReached: boolean;
                for (let i = 0; i < finalValue.length; i++) {
                    input = containerRef.children[idx + i].querySelector('input');
                    endReached = idx + i + 1 > this.NOChars - 1;
                    this.currentFieldIdx = endReached ? this.NOChars - 1: idx + i + 1;
                    input!.value = finalValue[i];
                    input = containerRef.children[endReached ? this.NOChars - 1: idx + i + 1].querySelector('input');
                    if (endReached) this.handleSubmit();
                }
                input!.focus();
            },
            handleKeyDown(event: any, idx: number) {
                if (idx === 0 || event.key !== 'Backspace') return;
                if (event.target.value.length > 0) {
                    this.errorPrompt = '';
                    return;
                }
                const containerRef = this.$refs.container as HTMLDivElement;
                const input = containerRef.children[idx - 1].querySelector('input');
                input!.value = '';
                this.currentFieldIdx = idx - 1;
                input!.focus();
            },
            containerMousedown() {
                const containerRef = this.$refs.container as HTMLDivElement;
                const input = containerRef.children[this.currentFieldIdx].querySelector('input');
                if (document.activeElement !== input) {
                    setTimeout(() => {
                        input!.focus();
                    }, 0);
                }
            },
            focusable(idx: number): Record<string, boolean> {
                return {
                    'unfocusable': idx !== this.currentFieldIdx
                };
            },
            cleanInput(value: string) {
                return value.replace(/[^a-zA-Z0-9]/g, "").toUpperCase();
            },
        },
        emits: {
            'success': function() :boolean {
                return true;
            },
            'resend': function(): boolean {
                return true;
            }
        }
       
    });

</script>