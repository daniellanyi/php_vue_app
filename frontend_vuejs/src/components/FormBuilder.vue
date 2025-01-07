<template>
    <div class="form-builder">
        <div :class="{'hide': invalidFormPromptList.length === 0}" :style="{minHeight: `${invalidFormPromptLimit*3}em`}">
            <div :class="invalidFormClass" ref="formInvalidPrompt">
                <p v-for="(prompt, index) in invalidFormPromptListTrans" :key="index">{{ prompt }}</p>
            </div>
        </div>
        <form class="form-builder-form"  @submit.prevent="handleSubmit">
            <slot name="above"></slot>
            <div class="form-builder-wrapper">
                <loading-spinner v-if="loadingResponse"></loading-spinner>
                <div class="form-builder-content">
                    <template v-for="field in fields"
                        :key="field.id"
                    >
                        <template v-if="field.confirm">
                            <text-field
                                :id="field.id"
                                :state="fieldSetState[field.id]" 
                                :textFieldType="field.textFieldType"
                                :label="field.label"
                                @input="getPairedValidation($event, field.textFieldType, field.id, `${field.id}-2`)">
                            </text-field>
                            <text-field
                                :id="`${field.id}-2`"
                                :state="fieldSetState[`${field.id}-2`]" 
                                :textFieldType="field.textFieldType"
                                :label="field.confirmLabel"
                                @input="getPairedValidation($event, field.textFieldType,`${field.id}-2`,  field.id)">
                            </text-field>
                        </template>
                        <template v-else>
                            <text-field
                                :id="field.id"
                                :state="fieldSetState[field.id]" 
                                :textFieldType="field.textFieldType"
                                :label="field.label"
                                @input="getValidation($event, field.textFieldType, field.id)">
                            </text-field>
                        </template>
                    </template>
                    <button-1 text="Submit" :buttonDisabledState="buttonDisabledState" @click="handleSubmit"></button-1>
                </div>
            </div>
            <slot name="below"></slot>
        </form>
    </div>
</template>

<style lang="scss">

    

    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus,
    textarea:-webkit-autofill,
    textarea:-webkit-autofill:hover,
    textarea:-webkit-autofill:focus,
    select:-webkit-autofill,
    select:-webkit-autofill:hover,
    select:-webkit-autofill:focus {
        border: none;
        caret-color: var(--text-color-3);
        -webkit-box-shadow: 0 0 0px 1000px var(--background-1-color-5) inset;
        -webkit-text-fill-color: var(--text-color-3) !important;
        
    }

   

    @keyframes colorTransition {
        from {
            background-color: var(--error-flash-color);
        }
        to {
            background-color: var(--background-1-color-1);
        }
    }

    .form-builder{
        display: flex;
        flex-direction: column;
        gap: var(--form-builder-field-gap , 10px);
        color: var(--text-color-2);
        width: var(--form-builder-width , inherit);
        text-align: start;
    }

    .form-builder-wrapper{
        position: relative;
        --spinner-height: 20%;
        --spinner-border: 12px solid var(--text-color-2);
    }

    .form-builder-form{
        padding: 20px;
        background-color: var(--background-1-color-1);
        h1 {
            margin: 0 0 40px 0;
        }
    }
    .form-builder-content{
        display: flex;
        flex-direction: column;
        gap: 20px;
        
    }

    .form-invalid-prompt {
        animation: colorTransition 1.5s forwards;
        color: var(--error-color);
        padding: 1em;
        border: 2px solid var(--error-color);
    }
    .form-invalid-prompt p {
        margin: 0;
    }

    @media (max-height: 760px) {
        .hide {
            display: none;
        }
    }

</style>


<script lang="ts">
import { PropType, defineComponent } from 'vue';
import { TextFieldType, TextFieldState, textFieldPrompt } from '../types/textField';
const emailRegex = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;
const passwordNumRegex = /.*[0-9].*/;
const passwordSpecialCharRegex = /.*[!@#$%^&*].*/;
export interface Field {
    id: string,
    textFieldType: TextFieldType,
    value: string,
    label: string,
    confirm:boolean,
    confirmLabel: string | null

}

type FieldSetState = {
    [key: string] : TextFieldState;
}



export default defineComponent({
    name: 'FormBuilder',
    data() {
        return{
            fieldSetState: {} as FieldSetState,
            invalidFormPromptLimit: 2,
            invalidFormPromptList: [] as textFieldPrompt[],
            buttonDisabledState: true,
            loadingResponse: false
        }
    },
    props: {
        fields: {
            type: Array as PropType<Field[]>,
            required: true
        },
        
        action: {
            type: String,
            required: true
        },
        
    },
    computed: {
        invalidFormClass() : Record<string, boolean> {
            return {
                'form-invalid-prompt': this.invalidFormPromptList.length > 0
            }
        },
        translatables() : textFieldPrompt[] {
            return [
                ['prompt_text_field_default'],
                ['error_text_field_username_length', {lb: 2, ub: 32}],
                ['prompt_text_field_email_entry'],
                ['error_text_field_email_validation'],
                ['prompt_text_field_password_entry'],
                ['prompt_text_field_password_confirm'],
                ['error_text_field_password_length', {lb: 6, ub: 16}],
                ['error_text_field_password_num'],
                ['error_text_field_password_special_char'],
                ['error_text_field_password_match'],
                ['prompt_text_field_confirm_email'],
                ['error_text_field_email_match'],
                ['prompt_text_field_password_confirm'],
                ['error_text_field_password_match']

            ]
        },
        invalidFormPromptListTrans(): string[] {
            return this.invalidFormPromptList.map((key=> {
                if (key[1]) {
                    return this.$t(key[0], key[1])
                } else {
                    return this.$t(key[0])
                }
            }))
        }
    },
    methods: {
        validateInput(text: string, id: string): [boolean, textFieldPrompt?] {
            if (text.length === 0) {
                return [false, this.translatables[0]];
            }
            return [true];
        },
        validateUsername(username: string, id: string): [boolean, textFieldPrompt?] {
            let result: [boolean, textFieldPrompt?];
            if (username.length === 0) result = [false, this.translatables[1]];
            else if (2 >= username.length || username.length > 32) result = [false, this.translatables[2]];
            else result = [true];
            return result;
        },
        validateEmail(email: string, id: string) : [boolean, textFieldPrompt?] {
            if (email.length === 0) return [false, this.translatables[3]];
            else if (!emailRegex.test(email)) return [false, this.translatables[4]];
            return [true];
        },
        validatePassword2(password: string, id: string): [boolean, textFieldPrompt?] {
            let otherPasswordValue
            const passwordFields = this.$props.fields.filter(field => field.textFieldType === TextFieldType.PASSWORD);
            const isFirst = passwordFields[0].id === id;
            const otherPassword = isFirst ? passwordFields[1]: passwordFields[0];
            if (otherPassword != null) {
                otherPasswordValue = this.fieldSetState[otherPassword.id].value;
            }
            if (password.length === 0) {
                return [false, isFirst ? this.translatables[5]: this.translatables[6]];
            } else if (password.length < 6 || password.length > 16) {
                return [false, this.translatables[7]];
            } else if (!passwordNumRegex.test(password)) {
                return [false, this.translatables[8]];
            } else if (!passwordSpecialCharRegex.test(password)) {
                return [false, this.translatables[9]];
            }
            if (!isFirst && (otherPasswordValue != null && otherPasswordValue.length > 0) && password !== otherPasswordValue) {
                return [false, this.translatables[10]];
            }
            return [true];
        },
        validatePassword(password: string, id: string): [boolean, textFieldPrompt?] {
            if (password.length === 0) {
                return [false,  this.translatables[5]];
            } else if (password.length < 6 || password.length > 16) {
                return [false, this.translatables[7]];
            } else if (!passwordNumRegex.test(password)) {
                return [false, this.translatables[8]];
            } else if (!passwordSpecialCharRegex.test(password)) {
                return [false, this.translatables[9]];
            }
            return [true];
        },
        getValidation(event: any, textFieldType: TextFieldType, id: string): any {
            const value = event.value;
            let result: [boolean, textFieldPrompt?];
            switch (textFieldType){
                case TextFieldType.DEFAULT:
                    result = this.validateInput(value, id);
                    break;
                case TextFieldType.USERNAME:
                    result = this.validateUsername(value, id);
                    break;
                case TextFieldType.EMAIL:
                    result = this.validateEmail(value, id);
                    break;
                case TextFieldType.PASSWORD:
                    result = this.validatePassword(value, id);
                    break;
                default:
                    result = this.validateInput(value, id);
                    break;
            }
            
            this.fieldSetState[id] = {
                value: value,
                prompt: result[1]
            }

            this.buttonDisabledState = !this.isFormFilledIn();
        },
        getPairedValidation(event: any, textFieldType: TextFieldType, id: string, otherId: string) {
            const [confirmText, matchFailText, validationMethod] =  this.getPairedValidationAssetsBasedOnType(textFieldType);
            const value = event.value;
            const result = validationMethod(value, id);
            let prompt = result[1];
            const otherValue = this.fieldSetState[otherId].value;
            
            if (otherValue != null && otherValue.length > 0) {
                if (result[0] === false) {
                    if (value.length === 0) prompt = confirmText;
                } else {
                    if (otherValue !== value) {
                        prompt = matchFailText;
                    }
                    this.fieldSetState[otherId] = {
                        value: otherValue,
                        prompt: undefined
                    }
                }
            }
            
            this.fieldSetState[id] = {
                    value: value,
                    prompt: prompt
            }
            this.buttonDisabledState = !this.isFormFilledIn();
        },
        getPairedValidationAssetsBasedOnType(textFieldType: TextFieldType)  {
            let result: [textFieldPrompt, textFieldPrompt, (password: string, id: string) => [boolean, textFieldPrompt?]]
            switch (textFieldType) {
                case TextFieldType.EMAIL:
                    result = [this.translatables[10], this.translatables[11], this.validateEmail];
                    break;
                default:
                    result = [this.translatables[12], this.translatables[13], this.validatePassword];
                    break;
            }
            return result;
        },
        validateForm(): [boolean, textFieldPrompt[]] {
            
            const prompts: textFieldPrompt[] = [];
            let iter = 0;
            for (let id in this.fieldSetState) {
                if (iter === this.invalidFormPromptLimit) {
                    prompts.push(['prompt_form_generic_error']);
                    break
                } else {
                    const prompt = this.fieldSetState[id].prompt;
                    if (prompt == null) continue;
                    prompts.push(prompt);
                }
                iter += 1;
            }
            if (prompts.length > 0) return [false, prompts];
            return [true, []];
            
        },
        generateInitialState(): void {
            let fieldSetState: FieldSetState = {}
            for (let field of this.$props.fields) {
                fieldSetState[field.id] = {
                    value: field.value ?? ''
                }
                if (field.confirm) {
                    fieldSetState[`${field.id}-2`] = {
                        value: field.value ?? ''
                    }
                }
            }
            this.fieldSetState = fieldSetState;
        },
        async handleSubmit(): Promise<void> {
            const validation = this.validateForm();
            if (validation[0] === false) {
                this.invalidFormPromptList = validation[1];
                this.scrollToPromptList();
                
            } else {
                const payload = Object.fromEntries(
                    Object.entries(this.fieldSetState)
                        .filter(([key, fieldState]) => !key.endsWith('-2'))
                        .map(([key, fieldState]) => [key, fieldState.value!]));
                this.loadingResponse = true;
                try {
                    
                    await this.$store.dispatch(this.$props.action, payload);
                    this.$emit('success');
                    this.invalidFormPromptList = [];
                } catch (error: any) {
                    this.invalidFormPromptList = [[error.message]];
                    this.scrollToPromptList();
                } finally {
                    this.loadingResponse = false;
                    
                }
            }
        },
        scrollToPromptList() {
            const promptRef = this.$refs.formInvalidPrompt as HTMLDivElement;
            if (promptRef != null) {
                promptRef.style.animation = 'none';
                promptRef.offsetHeight;
                promptRef.style.animation = '';
                promptRef.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        },
        getPromptList(): textFieldPrompt[] {
            const prompts: textFieldPrompt[] = [];
            let iter = 0;
            for (let id in this.fieldSetState) {
                if (iter === this.invalidFormPromptLimit) {
                    prompts.push(['prompt_form_generic_error']);
                    break
                } else {
                    const prompt = this.fieldSetState[id].prompt;
                    if (prompt == null) continue;
                    prompts.push(prompt);
                }
                iter += 1;
            }
            return prompts;
        },
        isFormFilledIn(): boolean {
            for (let id in this.fieldSetState) {
                if (this.fieldSetState[id].value?.length === 0) return false;
            }
            return true;
        },
    },
    emits: {
    'success': function(): boolean {
        return true;
        }
    },
    created(): void {
        this.generateInitialState();
    },
})

</script>

