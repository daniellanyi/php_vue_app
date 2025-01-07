<template>
    <div class="text-field-container">
        <label :for="id" class="text-field-label" >
            {{ label }}
            <div :class="wrapperClasses">
                <input :id="id" :maxlength="maxlength" :style="inputStyles" :value="state.value" :type="isPassword && !passwordVisibility ? 'password': 'text'" class="text-field-input" @input="onInput" spellcheck="false"/>
                <img
                    v-if="isPassword"
                    :src="eyeIcon"
                    alt="Password-Visibility"
                    @click="setPasswordVisibility"
                    >
            </div>
            <p class="error">
                {{ state.prompt == null ? '' : promptTrans }}
            </p>
        
        </label>
    </div>
</template>

<style lang="scss" scoped>

.text-field-container {
    font-size: var(--text-field-font-size , inherit);
}

.text-field-label{
    font-size: inherit;
}

.text-field-input-wrapper{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-right: 6px;
    background-color: var(--background-1-color-5);
    border-radius: 5px;
    height: min-content;
    border: 2px solid var(--border-color-1);
}

.text-field-input-wrapper img {
    position: absolute;
    right: 3%;
    cursor: pointer;
    width: 2em;
    
}

.text-field-input{
    font-size: inherit;
    border: none;
    outline: none;
    color: var(--text-color-3);
    padding: 0.5em;
    
    background-color: inherit;
}

.error{
    font-size: inherit;
    min-height: 2.5em;
    margin: 0;
    color: var(--error-color);
}

.error-border{
    border: 2px solid var(--error-color);
}
</style>

<script lang="ts">
    import { defineComponent, PropType } from 'vue';
    import { TextFieldType, TextFieldState } from '../../types/textField';
    import  eyeClosedIcon from '@/assets/icons/eye-close.png';
    import eyeOpenIcon from '@/assets/icons/eye-open.png';

    export default defineComponent({
        name: 'TextField',
        data() {
            return {
                passwordVisibility: false
            }
        },
        props: {
            id: {
                type: String,
                required: true
            },
            textFieldType: {
                type: String as PropType<TextFieldType>,
                required: true
            },
            label: {
                type: String,
                required: true
            },
            maxlength: {
                type: String,
                required: false,
                default: '30'
            },
            state: {
                type: Object as PropType<TextFieldState>,
                required: true
            }
        },
        computed: {
            wrapperClasses(): Record<string, boolean> {
                return {
                'text-field-input-wrapper': true,
                'error-border': this.state?.prompt != null
                }
            },
            inputStyles(): Record<string, string> {
                const inputWidth = this.textFieldType === TextFieldType.PASSWORD ? '80%' : '100%'; 
                return {
                    'width': inputWidth
                }
            },
            isPassword(): boolean {
                return this.textFieldType === TextFieldType.PASSWORD;
            },
            eyeIcon(): string {
                return this.passwordVisibility ? eyeOpenIcon: eyeClosedIcon;
            },
            promptTrans(): string {
                const prompt = this.state.prompt!;
                if (prompt[1]) return this.$t(prompt[0], prompt[1]);
                else return this.$t(prompt[0]);
            }
           
        },
        methods: {
            onInput(event: any) {
                this.$emit('input', {id: this.id, value: event.target.value})
            },
            setPasswordVisibility(): void {
                if (this.passwordVisibility === false) this.passwordVisibility = true
                else this.passwordVisibility = false
            }
        },
        emits: {
            'input': function(payload: {id: string, value: string}) : { id: string; value: string; } {
                return  payload
            }
        },
        
    })
</script>