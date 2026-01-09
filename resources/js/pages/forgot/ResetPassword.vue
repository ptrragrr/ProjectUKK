<!-- <template>
    <div class="reset-wrapper">
        <div class="card">
            <h1>Reset Password</h1>
            <p class="subtitle">
                Masukkan password baru untuk akun kamu
            </p>

            <form @submit.prevent="submit">
                <div class="field">
                    <label>Password Baru</label>
                    <div class="password-wrapper">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="password"
                            placeholder="Minimal 8 karakter"
                            required
                        />
                        <span class="eye" @click="showPassword = !showPassword">
                            {{ showPassword ? '🙈' : '👁' }}
                        </span>
                    </div>
                </div>

                <div class="field">
                    <label>Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            v-model="password_confirmation"
                            placeholder="Ulangi password"
                            required
                        />
                        <span class="eye" @click="showConfirm = !showConfirm">
                            {{ showConfirm ? '🙈' : '👁' }}
                        </span>
                    </div>
                </div>

                <button :disabled="loading">
                    <span v-if="!loading">Reset Password</span>
                    <span v-else>Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>
</template> -->

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const route = useRoute();
const router = useRouter();

const password = ref("");
const password_confirmation = ref("");
const loading = ref(false);

const showPassword = ref(false);
const showConfirm = ref(false);

const submit = async () => {
    if (password.value !== password_confirmation.value) {
        toast.error("Konfirmasi password tidak sama");
        return;
    }

    loading.value = true;

    try {
        await axios.post("/auth/reset-password", {
            email: route.query.email,
            token: route.query.token,
            password: password.value,
            password_confirmation: password_confirmation.value,
        });

        toast.success("Password berhasil diubah");
        router.push("/reset-succes");
    } catch (err) {
        toast.error(err.response?.data?.message || "Gagal reset password");
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="forgot-password-container">
        <div class="forgot-password-card">

            <h2 class="title">Reset Password</h2>
            <p class="subtitle">
                Masukkan password baru untuk akun Anda.
            </p>

            <form @submit.prevent="submit">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <div class="password-wrapper">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            v-model="password"
                            class="form-input"
                            placeholder="Minimal 8 karakter"
                            required
                        />
                        <span class="eye" @click="showPassword = !showPassword">
                            {{ showPassword ? '🙈' : '👁' }}
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            v-model="password_confirmation"
                            class="form-input"
                            placeholder="Ulangi password"
                            required
                        />
                        <span class="eye" @click="showConfirm = !showConfirm">
                            {{ showConfirm ? '🙈' : '👁' }}
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Reset Password
                </button>
            </form>

            <!-- <div class="back-to-login">
                <a href="/sign-in" class="back-link">
                    Kembali ke Login
                </a>
            </div> -->
        </div>
    </div>
</template>

<style scoped>
.forgot-password-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
    padding: 20px;
}

.forgot-password-card {
    background: white;
    border-radius: 20px;
    padding: 48px 40px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    background: #1B84FF;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.title {
    font-size: 28px;
    font-weight: 700;
    color: #1a202c;
    text-align: center;
    margin-bottom: 12px;
}

.subtitle {
    font-size: 15px;
    color: #718096;
    text-align: center;
    margin-bottom: 32px;
    line-height: 1.6;
}

.forgot-form {
    margin-bottom: 24px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    pointer-events: none;
}

.form-input {
    width: 100%;
    padding: 14px 16px 14px 48px;
    font-size: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    outline: none;
    transition: all 0.3s ease;
    background: #f7fafc;
}

.form-input:focus {
    border-color: #1B84FF;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input::placeholder {
    color: #a0aec0;
}

.btn-submit {
    width: 100%;
    padding: 14px 24px;
    background: #1B84FF;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-submit:active {
    transform: translateY(0);
}

.back-to-login {
    text-align: center;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-link:hover {
    color: #764ba2;
    gap: 10px;
}

/* Responsive */
@media (max-width: 576px) {
    .forgot-password-card {
        padding: 36px 24px;
    }

    .title {
        font-size: 24px;
    }

    .subtitle {
        font-size: 14px;
    }
}

.password-wrapper {
    position: relative;
}

.password-wrapper .form-input {
    padding-right: 42px;
}

.eye {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    user-select: none;
}

</style>
