<template>
    <div class="success-wrapper">
        <div class="card">
            <div class="icon">✔</div>

            <h1>Password Berhasil Diubah</h1>
            <p>
                Password akun kamu sudah berhasil diperbarui.
                Silakan login kembali menggunakan password baru.
            </p>

            <!-- <button @click="goToLogin">
                Login Sekarang
            </button> -->

              <!-- <div class="back-to-login">
                <a href="/sign-in" class="back-link">
                    Login Sekarang
                </a>
            </div> -->

        </div>
    </div>
</template>

<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const goToLogin = () => {
    router.push("/auth/sign-in");
};
</script>

<style scoped>
.success-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    /* background: linear-gradient(135deg, #4f46e5, #22c55e); */
    padding: 20px;
}

.card {
    background: white;
    max-width: 420px;
    width: 100%;
    padding: 40px 30px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: #1B84FF;
    color: white;
    font-size: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

h1 {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 12px;
    color: #111827;
}

p {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 28px;
    line-height: 1.6;
}

button {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    background: #4f46e5;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease;
}

button:hover {
    background: #4338ca;
}
</style>
