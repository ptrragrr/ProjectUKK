<template>
    <form @submit.prevent="submit">
        <input v-model="password" type="password" placeholder="Password baru" />
        <input v-model="password_confirmation" type="password" placeholder="Konfirmasi password" />
        <button>Reset Password</button>
    </form>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const route = useRoute();
const router = useRouter();

const password = ref("");
const password_confirmation = ref("");

const submit = async () => {
    try {
        // await axios.post("/auth/reset-password", {
        //     email: route.query.email,
        //     token: route.query.token,
        //     password: password.value,
        //     password_confirmation: password_confirmation.value,
        // });

        axios.post("/auth/reset-password-direct", {
    password: this.password,
    password_confirmation: this.password_confirmation,
}, {
    headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
    }
});

        toast.success("Password berhasil diubah");
        router.push("/auth/sign-in");
    } catch (err) {
        toast.error(err.response.data.message);
    }
};
</script>
