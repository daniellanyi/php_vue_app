<template>
        <div class="login-page">
            <form-builder
                v-if="currentStageDetails"
                :fields="detailsStageConfig"
                action="auth/login"
                @success="proceed()"
            >
                <template #above>
                    <p class="form-prompt">{{ $t('form_title_sign_in') }}</p>
                </template>
                <template #below>
                    <div class="link-container">
                        <div
                            class="link"
                            
                        >
                            <button-1
                                :text="$t('action_no_account')"
                                @click="redirect('/register')"
                            ></button-1>
                        </div>
                        <div
                            class="link"
                        >
                            <button-1
                                :text="$t('action_forgot_password')"
                                @click="redirect('/forgot-password')"
                            >
                            </button-1>
                        </div>
                    </div>
                </template>
            </form-builder>
        </div>
</template>

<style lang="scss">
    .login-page {
        display: flex;
        align-items: center;
        flex-direction: column;
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
        .login-page {
            --code-wrapper-padding: 0.5em;
        }
        .login-page{
            --font-size-large: 7vw;
            --font-size-small: 4vw;
            
        }
        
    }

    .link-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
        
    }

</style>

<script lang="ts">
import { defineComponent } from 'vue';
import { TextFieldType } from '@/types/textField';

enum LoginStage {
    DETAILS = 0,
    VERIFY_EMAIL = 1
}

interface LoginPage {
    currentStage: LoginStage,
}
    
export default defineComponent({
    data(): LoginPage {
        return {
            currentStage: LoginStage.DETAILS,
            
        }
    },
    computed: {
        currentStageDetails(): boolean {
            return this.currentStage === LoginStage.DETAILS
        },
        currentStageVerifyEmail(): boolean {
            return this.currentStage === LoginStage.VERIFY_EMAIL
        },
        detailsStageConfig(): Array<Record <string, string|boolean>> {
            return [
            {
                    'id': 'email',
                    'textFieldType': TextFieldType.EMAIL,
                    'label': this.$t('field_label_email')
                },
                {
                    'id': 'password',
                    'textFieldType': TextFieldType.PASSWORD,
                    'label': this.$t('field_label_password'),
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
            if (this.currentStage === LoginStage.DETAILS) {
                this.$router.replace('/');
                return;
            }
        },
        redirect(to: string) {
            console.log(to);
            this.$router.push(to);
        }
    }
})
</script>