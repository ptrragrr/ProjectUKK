<script setup lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { ref, onMounted } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import Swal from "sweetalert2/dist/sweetalert2.js";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();

interface Role {
    id: number;
    name: string;
}

interface ProfileDetails {
    photo: string;
    photo_url?: string;
    name: string;
    role: string;
    role_id?: number;
    phone: string;
}

const submitButton = ref<HTMLElement | null>(null);
const router = useRouter();
const roles = ref<Role[]>([
    { id: 1, name: "admin" },
    { id: 2, name: "Panitia 1" },
]);
const profileDetails = ref<ProfileDetails>({
    photo: getAssetPath("media/avatars/blank.png"),
    name: "",
    role: "",
    phone: "",
});

onMounted(async () => {
    try {
        const rolesResponse = await axios.get("/roles");
        if (rolesResponse.data.success && rolesResponse.data.data) {
            roles.value = rolesResponse.data.data;
        }

        const profileResponse = await axios.get("/me");
        if (profileResponse.data.success && profileResponse.data.data) {
            const userData = profileResponse.data.data;
            profileDetails.value = {
                photo: userData.photo_url || getAssetPath("media/avatars/blank.png"),
                photo_url: userData.photo_url,
                name: userData.name || "",
                role: userData.role || "",
                role_id: userData.role_id || null,
                phone: userData.phone || "",
            };
        }
    } catch (error) {
        console.error("Error loading data:", error);
        Swal.fire("Error", `Gagal memuat data: ${error.message}`, "error");
    }
});

const saveChanges = async (values: any) => {
    if (submitButton.value) {
        submitButton.value.setAttribute("data-kt-indicator", "on");

        try {
            const formData = new FormData();

            if (!values.name || values.name.trim() === "") {
                throw new Error("Nama tidak boleh kosong");
            }

            formData.append("name", values.name.trim());
            formData.append("phone", values.phone || "");

            if (values.role && values.role !== "") {
                const selectedRole = roles.value.find((r) => r.name === values.role);
                if (selectedRole) {
                    formData.append("role_id", selectedRole.id.toString());
                }
            }

            const photoInput = document.querySelector('input[name="avatar"]') as HTMLInputElement;
            if (photoInput?.files?.[0]) {
                formData.append("photo", photoInput.files[0]);
            }

            formData.append("_method", "PUT");

            const response = await axios.post("/me", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Accept: "application/json",
                },
            });

            Swal.fire({
                text: "Profile berhasil diperbarui!",
                icon: "success",
                confirmButtonText: "Ok",
                buttonsStyling: false,
                heightAuto: false,
                customClass: { confirmButton: "btn btn-light-primary" },
            });

            await loadProfile();
        } catch (error) {
            console.error("Save error:", error);
            let errorMessage = "Terjadi kesalahan saat menyimpan";

            if (error.message) {
                errorMessage = error.message;
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            } else if (error.response?.data?.errors) {
                const errors = Object.values(error.response.data.errors).flat();
                errorMessage = errors.join(", ");
            }

            Swal.fire("Gagal", errorMessage, "error");
        } finally {
            if (submitButton.value) {
                submitButton.value.removeAttribute("data-kt-indicator");
            }
        }
    }
};

const loadProfile = async () => {
    try {
        const profileResponse = await axios.get("/me");
        if (profileResponse.data.success && profileResponse.data.data) {
            const userData = profileResponse.data.data;
            profileDetails.value = {
                photo: userData.photo_url || getAssetPath("media/avatars/blank.png"),
                photo_url: userData.photo_url,
                name: userData.name || "",
                role: userData.role || "",
                role_id: userData.role_id || null,
                phone: userData.phone || "",
            };
        }
    } catch (error) {
        console.error("Error loading profile:", error);
        Swal.fire("Error", "Gagal memuat profil", "error");
    }
};

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            profileDetails.value.photo = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <!-- Hero Header Section -->
    <div class="card mb-5 mb-xl-10 border-0 shadow-sm">
        <div class="card-body pt-9 pb-0">
            <div class="d-flex flex-wrap flex-sm-nowrap">
                <!-- Avatar -->
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        <img :src="profileDetails.photo" alt="Profile" class="rounded-3 shadow" />
                        <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                    {{ profileDetails.name || "Nama belum diisi" }}
                                </a>
                                <i class="bi bi-patch-check-fill text-primary fs-3"></i>
                            </div>

                            <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <i class="bi bi-shield-check fs-4 me-1"></i>
                                    {{ profileDetails.role || "Role belum dipilih" }}
                                </a>
                                <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2" v-if="profileDetails.phone">
                                    <i class="bi bi-telephone fs-4 me-1"></i>
                                    {{ profileDetails.phone }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="d-flex flex-wrap flex-stack">
                        <div class="d-flex flex-column flex-grow-1 pe-8">
                            <div class="d-flex flex-wrap gap-3">
                                <!-- Status Badge -->
                                <!-- <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 bg-light-success">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success fs-2 me-2"></i>
                                        <div class="fs-2 fw-bold text-success counted">Active</div>
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-400">Status</div>
                                </div> -->

                                <!-- Account Type -->
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 bg-light-primary">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-badge-fill text-primary fs-2 me-2"></i>
                                        <div class="fs-2 fw-bold text-primary counted">Verified</div>
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-400">Account</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mt-8">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="#">
                        <i class="bi bi-person-circle me-2"></i>Overview
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-5 g-xl-8">
        <!-- Left Sidebar - Quick Info -->
        <div class="col-xl-4">
            <!-- Profile Details Card -->
            <div class="card shadow-sm mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Detail Profil</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Informasi akun Anda</span>
                    </h3>
                </div>

                <div class="card-body pt-5">
                    <!-- Full Name -->
                    <div class="d-flex align-items-center mb-7">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label bg-light-primary">
                                <i class="bi bi-person-fill text-primary fs-2x"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fw-semibold d-block fs-7">Nama Lengkap</span>
                            <span class="text-gray-800 fw-bold d-block fs-6">
                                {{ profileDetails.name || "Belum diisi" }}
                            </span>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="d-flex align-items-center mb-7">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label bg-light-success">
                                <i class="bi bi-shield-fill-check text-success fs-2x"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fw-semibold d-block fs-7">Role</span>
                            <span class="text-gray-800 fw-bold d-block fs-6">
                                {{ profileDetails.role || "Belum diisi" }}
                            </span>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="d-flex align-items-center mb-7" v-if="profileDetails.phone">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label bg-light-info">
                                <i class="bi bi-telephone-fill text-info fs-2x"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <span class="text-gray-500 fw-semibold d-block fs-7">Nomor Telepon</span>
                            <span class="text-gray-800 fw-bold d-block fs-6">
                                {{ profileDetails.phone }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notice Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                        <i class="bi bi-info-circle-fill fs-2tx text-warning me-4"></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Mode Read-Only</h4>
                                <div class="fs-6 text-gray-700">
                                    Data profil ditampilkan dalam mode view-only. Untuk melakukan perubahan, hubungi administrator.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content - Profile Form -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header border-0 cursor-pointer">
                    <div class="card-title m-0">
                        <h3 class="fw-bold m-0">
                            <i class="bi bi-gear-fill text-primary fs-3 me-2"></i>
                            Pengaturan Profil
                        </h3>
                    </div>
                </div>

                <div class="card-body border-top p-9">
                    <VForm
                        class="form"
                        novalidate
                        v-slot="{ handleSubmit }"
                    >
                        <form @submit.prevent="handleSubmit(saveChanges)">
                            <!-- Profile Picture Section -->
                            <div class="row mb-10">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                    <span class="required">Foto Profil</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Foto profil dalam mode read-only"></i>
                                </label>

                                <div class="col-lg-8">
                                    <div class="image-input image-input-outline" data-kt-image-input="true"
                                        :style="{
                                            backgroundImage: `url(${getAssetPath('/media/avatars/blank.png')})`,
                                        }">
                                        <div class="image-input-wrapper w-125px h-125px rounded-circle"
                                            :style="`background-image: url(${profileDetails.photo})`"></div>
                                    </div>
                                    <div class="form-text mt-3">
                                        <i class="bi bi-lock-fill me-1"></i>
                                        Foto profil tidak dapat diubah pada halaman ini
                                    </div>
                                </div>
                            </div>

                            <div class="separator separator-dashed mb-8"></div>

                            <!-- Name Field -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">
                                    <i class="bi bi-person-fill text-primary me-2"></i>
                                    Nama Lengkap
                                </label>
                                <div class="col-lg-8 fv-row">
                                    <Field
                                        type="text"
                                        name="name"
                                        class="form-control form-control-lg form-control-solid bg-light"
                                        placeholder="Nama lengkap"
                                        v-model="profileDetails.name"
                                        disabled
                                    />
                                </div>
                            </div>

                            <!-- Role Field -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                    <i class="bi bi-shield-check text-primary me-2"></i>
                                    Role
                                </label>
                                <div class="col-lg-8 fv-row">
                                    <Field
                                        type="text"
                                        name="role"
                                        class="form-control form-control-lg form-control-solid bg-light"
                                        placeholder="Role pengguna"
                                        v-model="profileDetails.role"
                                        disabled
                                    />
                                </div>
                            </div>

                            <!-- Phone Field -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                    <i class="bi bi-telephone-fill text-primary me-2"></i>
                                    Nomor Telepon
                                </label>
                                <div class="col-lg-8 fv-row">
                                    <Field
                                        type="tel"
                                        name="phone"
                                        class="form-control form-control-lg form-control-solid bg-light"
                                        placeholder="Nomor telepon"
                                        v-model="profileDetails.phone"
                                        disabled
                                    />
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card-footer d-flex justify-content-end py-6 px-9 mt-8 border-top">
                                <button
                                    type="button"
                                    class="btn btn-light btn-active-light-primary me-2"
                                    @click="router.go(-1)"
                                >
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Kembali
                                </button>
                            </div>
                        </form>
                    </VForm>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.symbol-label {
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-line-tabs .nav-link.active {
    border-bottom: 2px solid;
}

.notice {
    align-items: flex-start;
}
</style>