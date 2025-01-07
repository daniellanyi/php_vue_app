<template>
    <div>
        <div class="register-page">
            <form-builder
                v-if="currentStageEmail"
                :fields="emailStageConfig"
                action="auth/signUp"
                @success="proceed()"
            >
                <template #above>
                    <p class="form-prompt">{{ $t('form_title_signup') }}</p>
                </template>
            </form-builder>
            <code-verification
                v-if="currentStageVerifyEmail"
                :NOChars="5"
                action="auth/verifyEmail"
                resendAction="resendVerificationCode"
                @success="proceed()"
            >
                <template #above>
                    <p class="form-prompt">{{ userPrompt }}</p>
                </template>
            </code-verification>
            <form-builder
                v-if="currentStageEnterDetails"
                :fields="enterDetailsStageConfig"
                action="auth/completeSignUp"
                @success="proceed()"
            >
                <template #above>
                    <p class="form-prompt">{{ $t('prompt_user_detail_entry') }}</p>
                </template>
            </form-builder>
        </div>
    </div>
</template>

<style lang="scss">
    .register-page {
        align-items: center;
        flex-direction: column;
        width: 100%;
        --button-1-padding: 0.7em 1em;
        --button-1-inset-shadow:4px;
        --button-1-inset-shadow-active: 2px;
        label{
            font-size: var(--font-size-small, 1rem);
        }
        --code-verification-font-size: var(--font-size-large);
    }

    .form-prompt{
        font-size: var(--font-size-large, 2rem);
        font-weight: bold;
    }

    @media (max-width:400px) {
        .register-page {
            --code-verification-font-size: 10vw;
        }
    }

</style>

<script lang="ts">

enum SignUpStage {
    EMAIL = 0,
    VERIFY_EMAIL = 1,
    ENTER_DETAILS = 2
}

interface SignUpPage {
    currentStage: SignUpStage,
}

import { defineComponent } from 'vue';
import { TextFieldType } from '@/types/textField';
    
export default defineComponent({
    data(): SignUpPage {
        return {
            currentStage: SignUpStage.EMAIL,
        }
        
    },
    computed: {
        currentStageEmail(): boolean {
            return this.currentStage === SignUpStage.EMAIL;
        },
        currentStageVerifyEmail(): boolean {
            return this.currentStage === SignUpStage.VERIFY_EMAIL;
        },
        currentStageEnterDetails(): boolean {
            return this.currentStage === SignUpStage.ENTER_DETAILS;
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
        enterDetailsStageConfig(): Array<Record <string, string|boolean>> {
            return [
                {
                    'id': 'username',
                    'textFieldType': TextFieldType.USERNAME,
                    'label': this.$t('field_label_username')
                },
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
            const userEmail = this.$store.getters['auth/getUserEmail']
            return userEmail
            ? `${this.$t('prompt_email_check')} ${userEmail.split('@')[1]}`
            : this.$t('prompt_email_check_fallback');
        }
    },
    methods: {
        proceed() {
            if (this.currentStage === SignUpStage.ENTER_DETAILS) {
                this.$router.replace('/');
                return;
            }
            this.currentStage += 1;
        },
        getEmail() : string {
            return this.$store.getters['auth/getUserEmail'];
        }

    }
})

</script>