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

// const profileDetailsValidator = Yup.object().shape({
//     name: Yup.string().required().label("Nama"),
//     role: Yup.string().required().label("Role"),
//     phone: Yup.string().required().label("Nomor Telepon"),
// });

const profileDetailsValidator = Yup.object().shape({
    name: Yup.string().required().label("Nama"),
    phone: Yup.string().required().label("Nomor Telepon"),
});

// const saveChanges = async (values: any) => {
//     if (submitButton.value) {
//         submitButton.value.setAttribute("data-kt-indicator", "on");

//         try {
//             const formData = new FormData();
//             formData.append("name", values.name);
//             formData.append("phone", values.phone || "");

//             // Foto
//             const photoInput = document.querySelector(
//                 'input[name="avatar"]'
//             ) as HTMLInputElement;
//             if (photoInput?.files?.[0]) {
//                 formData.append("photo", photoInput.files[0]);
//             }

//             await axios.put("/me", formData, {
//                 headers: { "Content-Type": "multipart/form-data" },
//             });

//             Swal.fire({
//                 text: "Profile berhasil diperbarui!",
//                 icon: "success",
//                 confirmButtonText: "Ok",
//                 buttonsStyling: false,
//                 heightAuto: false,
//                 customClass: { confirmButton: "btn btn-light-primary" },
//             });

//             await loadProfile();
//         } catch (error) {
//             console.error("Save error:", error);
//             let errorMessage = "Terjadi kesalahan saat menyimpan";

//             if (error.response?.data?.errors) {
//                 const errors = Object.values(error.response.data.errors).flat();
//                 errorMessage = errors.join(", ");
//             }

//             Swal.fire("Gagal", errorMessage, "error");
//         } finally {
//             submitButton.value.removeAttribute("data-kt-indicator");
//         }
//     }
// };

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

// Handle role selection chang
</script>

<template>
    <!-- Profile Details Card -->
    <div class="card mb-5 mb-xl-10">
        <!-- Card Header -->
        <div class="card-header border-0">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Detail Profile</h3>
            </div>
        </div>

        <!-- Card Content -->
        <div class="card-body border-top p-9">
            <!-- Profile Form -->
            <!-- <VForm
                class="form"
                novalidate
                @submit="saveChanges()"
                :validation-schema="profileDetailsValidator"
            > -->
            <VForm
                class="form"
                novalidate
                v-slot="{ handleSubmit }"
                :validation-schema="profileDetailsValidator"
            >
                <form @submit.prevent="handleSubmit(saveChanges)">
                    <!-- semua input -->
                    <!-- Photo Upload Section -->
                    <div class="row mb-8">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Foto Profile
                        </label>
                        <div class="col-lg-8">
                            <!-- Image Input -->
                            <div
                                class="image-input image-input-outline"
                                data-kt-image-input="true"
                                :style="{
                                    backgroundImage: `url(${getAssetPath(
                                        '/media/avatars/blank.png'
                                    )})`,
                                }"
                            >
                                <!-- Image Preview -->
                                <div
                                    class="image-input-wrapper w-125px h-125px"
                                    :style="`background-image: url(${profileDetails.photo})`"
                                ></div>

                                <!-- Change Photo Button -->
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change"
                                    data-bs-toggle="tooltip"
                                    title="Ganti Foto"
                                >
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input
                                        type="file"
                                        name="avatar"
                                        accept=".png, .jpg, .jpeg"
                                        @change="handleImageUpload"
                                    />
                                    <input type="hidden" name="avatar_remove" />
                                </label>

                                <!-- Remove Photo Button -->
                                <span
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove"
                                    data-bs-toggle="tooltip"
                                    @click="removeImage()"
                                    title="Hapus Foto"
                                >
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>

                            <!-- File Type Hint -->
                            <div class="form-text">
                                Tipe file yang diizinkan: png, jpg, jpeg.
                            </div>
                        </div>
                    </div>

                    <!-- Name Input -->
                    <div class="row mb-6">
                        <label
                            class="col-lg-4 col-form-label required fw-semibold fs-6"
                        >
                            Nama Lengkap
                        </label>
                        <div class="col-lg-8 fv-row">
                            <Field
                                type="text"
                                name="name"
                                class="form-control form-control-lg form-control-solid"
                                placeholder="Masukkan nama lengkap"
                                v-model="profileDetails.name"
                            />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="name" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Selection - DINAMIS -->
                    <div class="row mb-6">
                        <label
                            class="col-lg-4 col-form-label required fw-semibold fs-6"
                        >
                            Role
                        </label>
                        <div class="col-lg-8 fv-row">
                            <!-- <Field
                                as="select"
                                name="role"
                                class="form-select form-select-solid form-select-lg"
                                v-model="profileDetails.role"
                                @change="onRoleChange"
                            >
                                <option value="">Pilih Role</option>
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </option>
                            </Field> -->
                            <Field
                                type="text"
                                name="role"
                                class="form-control form-control-lg form-control-solid"
                                placeholder="Role"
                                v-model="profileDetails.role"
                                disabled
                            />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="role" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phone Input -->
                    <div class="row mb-6">
                        <label
                            class="col-lg-4 col-form-label required fw-semibold fs-6"
                        >
                            Nomor Telepon
                            <i
                                class="fas fa-exclamation-circle ms-1 fs-7"
                                data-bs-toggle="tooltip"
                                title="Nomor telepon harus aktif"
                            ></i>
                        </label>
                        <div class="col-lg-8 fv-row">
                            <Field
                                type="tel"
                                name="phone"
                                class="form-control form-control-lg form-control-solid"
                                placeholder="Contoh: 081234567890"
                                v-model="profileDetails.phone"
                            />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <ErrorMessage name="phone" />
                                </div>
                            </div>
                            <div class="form-text">Format: 08xxxxxxxxxx</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="card-footer d-flex justify-content-end py-6 px-9"
                    >
                        <button
                            type="reset"
                            class="btn btn-light btn-active-light-primary me-3"
                            @click="router.go(-1)"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            ref="submitButton"
                            class="btn btn-primary"
                        >
                            <span class="indicator-label">
                                Simpan Perubahan
                            </span>
                            <span class="indicator-progress">
                                Menyimpan...
                                <span
                                    class="spinner-border spinner-border-sm align-middle ms-2"
                                ></span>
                            </span>
                        </button>
                    </div>
                </form>
            </VForm>
        </div>
    </div>

    <!-- Profile Summary Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="fw-bold m-0">Preview Profile</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <!-- Profile Image -->
                <div class="me-5">
                    <div
                        class="symbol symbol-100px symbol-circle"
                        :style="`background-image: url(${profileDetails.photo})`"
                    ></div>
                </div>

                <!-- Profile Info -->
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-gray-800 mb-2">
                        {{ profileDetails.name || "Nama belum diisi" }}
                    </h4>
                    <div class="text-muted mb-1">
                        <i class="bi bi-person-badge me-2"></i>
                        {{ profileDetails.role || "Role belum dipilih" }}
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-telephone me-2"></i>
                        {{
                            profileDetails.phone || "Nomor telepon belum diisi"
                        }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Info (hapus setelah testing) -->
    <div class="card mt-5" v-if="false">
        <div class="card-body">
            <h5>Debug Info:</h5>
            <pre>{{ JSON.stringify(profileDetails, null, 2) }}</pre>
            <pre>{{ JSON.stringify(roles, null, 2) }}</pre>
        </div>
    </div>
</template>
