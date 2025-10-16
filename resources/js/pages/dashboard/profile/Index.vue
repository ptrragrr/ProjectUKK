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

// ✅ Ambil data profil dan roles dari backend
onMounted(async () => {
    try {
        console.log("Starting to load profile and roles...");

        // Ambil data roles dulu
        console.log("Fetching roles...");
        const rolesResponse = await axios.get("/roles");
        console.log("Roles Response:", rolesResponse);
        console.log("Roles Response Data:", rolesResponse.data);

        // Set roles
        if (rolesResponse.data.success && rolesResponse.data.data) {
            roles.value = rolesResponse.data.data;
            console.log("Roles set:", roles.value);
        } else {
            console.error(
                "Invalid roles response structure:",
                rolesResponse.data
            );
        }

        // Ambil data profil
        console.log("Fetching profile...");
        const profileResponse = await axios.get("/me");
        console.log("Profile Response:", profileResponse);
        console.log("Profile Response Data:", profileResponse.data);

        // Set profile details
        if (profileResponse.data.success && profileResponse.data.data) {
            const userData = profileResponse.data.data;
            profileDetails.value = {
                photo:
                    userData.photo_url ||
                    getAssetPath("media/avatars/blank.png"),
                photo_url: userData.photo_url,
                name: userData.name || "",
                role: userData.role || "",
                role_id: userData.role_id || null,
                phone: userData.phone || "",
            };
            console.log("Profile Details Set:", profileDetails.value);
        } else {
            console.error(
                "Invalid profile response structure:",
                profileResponse.data
            );
        }
    } catch (error) {
        console.error("Error loading data:", error);
        console.error("Error details:", error.response?.data);
        Swal.fire("Error", `Gagal memuat data: ${error.message}`, "error");
    }
});

const saveChanges = async (values: any) => {
    if (submitButton.value) {
        submitButton.value.setAttribute("data-kt-indicator", "on");

        try {
            console.log("Form values:", values);
            console.log("Profile details:", profileDetails.value);

            const formData = new FormData();

            // Pastikan name ada dan tidak kosong
            if (!values.name || values.name.trim() === "") {
                throw new Error("Nama tidak boleh kosong");
            }

            formData.append("name", values.name.trim());
            formData.append("phone", values.phone || "");

            // Kirim role_id berdasarkan role yang dipilih
            if (values.role && values.role !== "") {
                const selectedRole = roles.value.find(
                    (r) => r.name === values.role
                );
                if (selectedRole) {
                    formData.append("role_id", selectedRole.id.toString());
                    console.log("Sending role_id:", selectedRole.id);
                }
            }

            // Foto (jika ada)
            const photoInput = document.querySelector(
                'input[name="avatar"]'
            ) as HTMLInputElement;
            if (photoInput?.files?.[0]) {
                formData.append("photo", photoInput.files[0]);
            }

            // Debug: Log FormData contents
            console.log("FormData contents:");
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            // Gunakan POST dengan _method untuk FormData
            formData.append("_method", "PUT");

            const response = await axios.post("/me", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Accept: "application/json",
                },
            });

            console.log("Response:", response.data);

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
            console.error("Error response:", error.response);

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
                photo:
                    userData.photo_url ||
                    getAssetPath("media/avatars/blank.png"),
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

const removeImage = () => {
    profileDetails.value.photo = getAssetPath("media/avatars/blank.png");
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
    <div class="row g-5 g-xl-8">
        <!-- Left Column - Profile Card -->
        <div class="col-xl-4">
            <!-- Profile Summary Card -->
            <div class="card shadow-sm">
                <div class="card-body pt-9 pb-0">
                    <!-- Profile Image -->
                    <div class="d-flex flex-center flex-column mb-5">
                        <div class="symbol symbol-150px symbol-circle mb-7 border border-4 border-white shadow">
                            <img :src="profileDetails.photo" alt="Profile Photo" />
                        </div>
                        
                        <div class="text-center">
                            <h3 class="fw-bold text-gray-900 mb-1">
                                {{ profileDetails.name || "Nama belum diisi" }}
                            </h3>
                            <div class="badge badge-light-primary fw-bold mb-3">
                                {{ profileDetails.role || "Role belum dipilih" }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="separator mb-6"></div>
                    
                    <div class="pb-8">
                        <div class="d-flex align-items-center bg-light-primary rounded p-4 mb-4">
                            <span class="svg-icon svg-icon-primary svg-icon-2x me-4">
                                <i class="bi bi-person-fill text-primary fs-4 me-2"></i>
                            </span>
                            <div class="flex-grow-1">
                                <span class="text-gray-600 fw-semibold d-block fs-7">Nama Lengkap</span>
                                <span class="text-gray-800 fw-bold fs-6">
                                    {{ profileDetails.name || "Belum diisi" }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center bg-light-info rounded p-4">
                            <span class="svg-icon svg-icon-info svg-icon-2x me-4">
                                <i class="bi bi-shield-check text-primary fs-4 me-2"></i>
                            </span>
                            <div class="flex-grow-1">
                                <span class="text-gray-600 fw-semibold d-block fs-7">Role</span>
                                <span class="text-gray-800 fw-bold fs-6">
                                    {{ profileDetails.role || "Belum diisi" }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Profile Details -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <!-- Card Header with Icon -->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="svg-icon svg-icon-primary svg-icon-2x me-3">
                            <i class="bi bi-person-circle fs-2"></i>
                        </span>
                        <h3 class="fw-bold m-0">Informasi Profile</h3>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="card-body pt-6">
                    <VForm
                        class="form"
                        novalidate
                        v-slot="{ handleSubmit }"
                        :validation-schema="profileDetailsValidator"
                    >
                        <form @submit.prevent="handleSubmit(saveChanges)">
                            <!-- Info Alert -->
                            <div class="alert alert-dismissible bg-light-warning border border-warning d-flex flex-column flex-sm-row p-5 mb-8">
                                <span class="svg-icon svg-icon-2hx svg-icon-warning me-4 mb-5 mb-sm-0">
                                    <i class="bi bi-info-circle-fill fs-2"></i>
                                </span>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="mb-1">Mode Tampilan Saja</h5>
                                    <span>Data profil Anda ditampilkan dalam mode read-only dan tidak dapat diubah pada halaman ini.</span>
                                </div>
                            </div>

                            <!-- Foto Profile Section -->
                            <div class="mb-8">
                                <h4 class="fw-bold text-gray-800 mb-5">
                                    <i class="bi bi-image me-2 text-primary"></i>Foto Profile
                                </h4>
                                <div class="d-flex align-items-center bg-light rounded p-6">
                                    <div
                                        class="image-input image-input-outline me-5"
                                        data-kt-image-input="true"
                                        :style="{
                                            backgroundImage: `url(${getAssetPath('/media/avatars/blank.png')})`,
                                        }"
                                    >
                                        <div
                                            class="image-input-wrapper w-100px h-100px rounded-circle"
                                            :style="`background-image: url(${profileDetails.photo})`"
                                        ></div>
                                    </div>
                                    <div>
                                        <p class="text-gray-700 fw-semibold mb-1">Foto profil Anda</p>
                                        <p class="text-muted fs-7 mb-0">
                                            <i class="bi bi-lock-fill me-1"></i>Foto profil tidak dapat diubah
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information Section -->
                            <div class="mb-8">
                                <h4 class="fw-bold text-gray-800 mb-5">
                                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Informasi Pribadi
                                </h4>
                                
                                <!-- Name Input -->
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6 align-items-center d-flex">
                                        <i class="bi bi-person-fill text-primary fs-4 me-2"></i>
                                        Nama Lengkap
                                    </label>
                                    <div class="col-lg-8">
                                        <Field
                                            type="text"
                                            name="name"
                                            class="form-control form-control-lg form-control-solid bg-light"
                                            v-model="profileDetails.name"
                                            disabled
                                        />
                                    </div>
                                </div>

                                <!-- Role Input -->
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6 align-items-center d-flex">
                                        <i class="bi bi-shield-check text-primary fs-4 me-2"></i>
                                        Role
                                    </label>
                                    <div class="col-lg-8">
                                        <Field
                                            type="text"
                                            name="role"
                                            class="form-control form-control-lg form-control-solid bg-light"
                                            v-model="profileDetails.role"
                                            disabled
                                        />
                                    </div>
                                </div>

                                <!-- Phone Input -->
                                <div class="row mb-6">
                                    <label class="col-lg-4 col-form-label fw-semibold fs-6 align-items-center d-flex">
                                        <i class="bi bi-telephone-fill text-primary fs-4 me-2"></i>
                                        Nomor Telepon
                                    </label>
                                    <div class="col-lg-8">
                                        <Field
                                            type="tel"
                                            name="phone"
                                            class="form-control form-control-lg form-control-solid bg-light"
                                            v-model="profileDetails.phone"
                                            disabled
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="separator mb-6"></div>
                            <div class="d-flex justify-content-end pt-2">
                                <button
                                    type="button"
                                    class="btn btn-light-primary btn-lg"
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