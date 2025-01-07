<template>
    <div class="forgot-password-page" id="f">
        <form-builder
            v-if="currentStageEmail"
            :fields="emailStageConfig"
            action="auth/forgotPassword"
            @success="proceed()"
        >
            <template #above>
                <p class="form-prompt">{{ $t('form_title_forgot_password') }}</p>
            </template>
        </form-builder>
        <div v-if="currentStageVerifyEmail" style="margin-top: 100px;"></div>
        <code-verification
            v-if="currentStageVerifyEmail"
            :NOChars="5"
            action="auth/verifyEmail"
            resendAction="auth/resendVerificationCode"
            @success="proceed()"
        >
            <template #above>
                <p class="form-prompt">{{ userPrompt }}</p>
            </template>
        </code-verification>
        <form-builder
            v-if="currentStageResetPassword"
            :fields="resetPasswordStageConfig"
            action="auth/changePassword"
            @success="proceed()"
        >
            <template #above>
                <p class="form-prompt">{{ $t('form_title_reset_password') }}</p>
            </template>
        </form-builder>
    </div>

</template>

<style lang="scss">



.forgot-password-page{
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    --button-1-padding: 0.7em 1em;
    --button-1-inset-shadow:4px;
    --button-1-inset-shadow-active: 2px;
    
    --code-verification-font-size: var(--font-size-large);
    --button-1-font-size: var(--font-size-small);
    --text-field-font-size: var(--font-size-small);
}



.form-prompt {
    font-size: var(--font-size-large, 2rem);
    font-weight: bold;
}

@media (max-width: 500px) {
    .forgot-password-page {
        --code-wrapper-padding: 0.5em;
    }
    .forgot-password-page{
        --font-size-large: 8vw;
        --font-size-small: 5vw;
        
    }
    
}

@media (max-width:400px) {
     
}





</style>


<script lang="ts">
import { defineComponent } from 'vue';
import { TextFieldType } from '@/types/textField';

enum ForgotPasswordStage {
    EMAIL = 0,
    VERIFY_EMAIL = 1,
    RESET_PASSWORD = 2
}

interface ForgotPasswordPage {
    currentStage: ForgotPasswordStage,
}

export default defineComponent({
    data(): ForgotPasswordPage {
        return {
            currentStage: ForgotPasswordStage.EMAIL,
        }
    },
    computed: {
        currentStageEmail(): boolean {
            return this.currentStage === ForgotPasswordStage.EMAIL;
        },
        currentStageVerifyEmail(): boolean {
            return this.currentStage === ForgotPasswordStage.VERIFY_EMAIL;
        },
        currentStageResetPassword(): boolean {
            return this.currentStage === ForgotPasswordStage.RESET_PASSWORD;
        },
        emailStageConfig(): Array<Record <string, string|boolean>> {
            return [
                {
                    'id': 'email',
                    'textFieldType': TextFieldType.EMAIL,
                    'label': this.$t('field_label_email'),
                    'confirm': true,
                    'confirmLabel': this.$t('field_label_email_confirm')
                }
            ]
        },
        resetPasswordStageConfig(): Array<Record <string, string|boolean>> {
            return [
                {
                    'id': 'password',
                    'textFieldType': TextFieldType.PASSWORD,
                    'label': this.$t('field_label_password'),
                    'confirm': true,
                    'confirmLabel': this.$t('field_label_password_confirm')
                },
            ]
        },
        userPrompt(): string {
            const userEmail = this.$store.getters['auth/getUserEmail'];
            console.log(userEmail);
            return userEmail
            ? `${this.$t('prompt_email_check')} ${userEmail.split('@')[1]}`
            : this.$t('prompt_email_check_fallback');
        },
        

    },
    methods: {
        proceed() {
            if (this.currentStage === ForgotPasswordStage.RESET_PASSWORD) {
                this.$router.replace('/');
                return;
            }
            this.currentStage += 1;
        },
    
    }
}) 

</script>