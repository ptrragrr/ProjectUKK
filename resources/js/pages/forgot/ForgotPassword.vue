<!-- <script>
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            password: "",
            password_confirmation: ""
        };
    },
    methods: {
        async submit() {
            if (this.password !== this.password_confirmation) {
                toast.error("Konfirmasi password tidak sama");
                return;
            }

            try {
                // await axios.post("/auth/reset-password-direct", {
                //     password: this.password,
                //     password_confirmation: this.password_confirmation,
                // });

                await axios.post("/auth/forgot-password", {
    email: this.email
});

                toast.success("Password berhasil direset");
                this.$router.push("/sign-in");
            } catch (error) {
                toast.error(error.response?.data?.message || "Gagal reset password");
            }
        }
    }
};
</script> -->

<script>
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

export default {
    data() {
        return {
            email: ""
        };
    },
    methods: {
        async submit() {
            try {
                await axios.post("/auth/forgot-password", {
                    email: this.email
                });

                toast.success("Cek email kamu untuk reset password");
            } catch (error) {
                toast.error(error.response?.data?.message || "Gagal kirim email");
            }
        }
    }
};
</script>

<template>
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <!-- Icon -->
            <div class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>

            <h2 class="title">Reset Password</h2>
            <p class="subtitle">
                Masukkan password baru Anda untuk melanjutkan.
            </p>

           <form @submit.prevent="submit">
    <div class="form-group">
        <label class="form-label">Email</label>
        <input
            v-model="email"
            type="email"
            class="form-input"
            placeholder="nama@email.com"
            required
        />
    </div>

    <button type="submit" class="btn-submit">
        Kirim Link Reset
    </button>
</form>


            <div class="back-to-login">
                <a href="/sign-in" class="back-link">
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
.forgot-password-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input::placeholder {
    color: #a0aec0;
}

.btn-submit {
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
</style>