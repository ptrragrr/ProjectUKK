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
    <div class="container-fluid px-4 py-6">
        <!-- Modern Hero Header -->
        <div class="card border-0 shadow-sm mb-6 overflow-hidden profile-hero">
            <div class="position-relative">
                <!-- Background Gradient -->
                <div class="profile-hero-bg"></div>
                
                <div class="card-body p-8 position-relative">
                    <div class="row align-items-center">
                        <!-- Avatar Section -->
                        <div class="col-lg-auto text-center text-lg-start mb-6 mb-lg-0">
                            <div class="position-relative d-inline-block">
                                <div class="avatar-wrapper">
                                    <img :src="profileDetails.photo" alt="Profile" class="profile-avatar" />
                                </div>
                                <div class="status-badge">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                        </div>

                        <!-- User Info Section -->
                        <div class="col-lg">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-4">
                                <div>
                                    <div class="d-flex align-items-center mb-3">
                                        <h1 class="text-white fw-bold mb-0 me-3 display-6">
                                            {{ profileDetails.name || "Nama belum diisi" }}
                                        </h1>
                                        <span class="badge-verified">
                                            <i class="bi bi-patch-check-fill"></i>
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-4 mb-3">
                                        <div class="info-chip">
                                            <i class="bi bi-shield-check me-2"></i>
                                            <span>{{ profileDetails.role || "Role belum dipilih" }}</span>
                                        </div>
                                        <div class="info-chip" v-if="profileDetails.phone">
                                            <i class="bi bi-telephone me-2"></i>
                                            <span>{{ profileDetails.phone }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stats Card -->
                                <div class="stats-container">
                                    <div class="stat-item">
                                        <i class="bi bi-person-badge"></i>
                                        <span class="stat-label">Verified Account</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="row g-6">
            <!-- Left Sidebar -->
            <div class="col-xl-4">
                <!-- Profile Info Card -->
                <div class="card border-0 shadow-sm mb-6 info-card">
                    <div class="card-header border-0 bg-transparent pt-6 pb-0">
                        <h3 class="card-title">
                            <i class="bi bi-person-circle text-primary fs-2 me-2"></i>
                            <span class="fw-bold">Detail Profil</span>
                        </h3>
                    </div>

                    <div class="card-body pt-5">
                        <div class="info-item">
                            <div class="info-icon bg-gradient-primary">
                                <i class="bi bi-person-fill text-white"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Nama Lengkap</span>
                                <span class="info-value">{{ profileDetails.name || "Belum diisi" }}</span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon bg-gradient-success">
                                <i class="bi bi-shield-fill-check text-white"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Role</span>
                                <span class="info-value">{{ profileDetails.role || "Belum diisi" }}</span>
                            </div>
                        </div>

                        <div class="info-item" v-if="profileDetails.phone">
                            <div class="info-icon bg-gradient-info">
                                <i class="bi bi-telephone-fill text-white"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Nomor Telepon</span>
                                <span class="info-value">{{ profileDetails.phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notice Card -->
                <div class="card border-0 shadow-sm notice-card">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-start gap-4">
                            <div class="notice-icon">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Mode Read-Only</h5>
                                <p class="text-muted mb-0 fs-7">
                                    Data profil ditampilkan dalam mode view-only. Untuk melakukan perubahan, hubungi administrator.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-light pt-6">
                        <h3 class="card-title">
                            <i class="bi bi-gear-fill text-primary fs-2 me-2"></i>
                            <span class="fw-bold">Pengaturan Profil</span>
                        </h3>
                    </div>

                    <div class="card-body p-8">
                        <VForm class="form" novalidate v-slot="{ handleSubmit }">
                            <form @submit.prevent="handleSubmit(saveChanges)">
                                <!-- Profile Picture -->
                                <div class="form-section">
                                    <label class="form-section-label">
                                        <i class="bi bi-image me-2"></i>
                                        Foto Profil
                                    </label>
                                    
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="profile-preview">
                                            <img :src="profileDetails.photo" alt="Profile Preview" />
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="alert alert-light border mb-0">
                                                <i class="bi bi-lock-fill text-muted me-2"></i>
                                                <span class="text-muted">Foto profil tidak dapat diubah pada halaman ini</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-divider"></div>

                                <!-- Name Field -->
                                <div class="form-section">
                                    <label class="form-section-label required">
                                        <i class="bi bi-person-fill me-2"></i>
                                        Nama Lengkap
                                    </label>
                                    <Field
                                        type="text"
                                        name="name"
                                        class="form-control form-control-lg form-input-readonly"
                                        placeholder="Nama lengkap"
                                        v-model="profileDetails.name"
                                        disabled
                                    />
                                </div>

                                <!-- Role Field -->
                                <div class="form-section">
                                    <label class="form-section-label">
                                        <i class="bi bi-shield-check me-2"></i>
                                        Role
                                    </label>
                                    <Field
                                        type="text"
                                        name="role"
                                        class="form-control form-control-lg form-input-readonly"
                                        placeholder="Role pengguna"
                                        v-model="profileDetails.role"
                                        disabled
                                    />
                                </div>

                                <!-- Phone Field -->
                                <div class="form-section">
                                    <label class="form-section-label">
                                        <i class="bi bi-telephone-fill me-2"></i>
                                        Nomor Telepon
                                    </label>
                                    <Field
                                        type="tel"
                                        name="phone"
                                        class="form-control form-control-lg form-input-readonly"
                                        placeholder="Nomor telepon"
                                        v-model="profileDetails.phone"
                                        disabled
                                    />
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end gap-3 mt-8 pt-6 border-top">
                                    <button
                                        type="button"
                                        class="btn btn-light btn-lg px-6"
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
    </div>
</template>

<style scoped>
/* Hero Section */
.profile-hero {
    border-radius: 16px;
    overflow: hidden;
}

.profile-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.profile-hero-bg::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-opacity="0.05" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.5;
}

/* Avatar */
.avatar-wrapper {
    position: relative;
    width: 140px;
    height: 140px;
    border-radius: 24px;
    padding: 4px;
    background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.profile-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 20px;
    border: 3px solid rgba(255,255,255,0.8);
}

.status-badge {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 2px 12px rgba(16, 185, 129, 0.4);
}

.status-badge i {
    color: white;
    font-size: 14px;
}

/* Badge */
.badge-verified {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    color: white;
    font-size: 18px;
}

/* Info Chips */
.info-chip {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    color: white;
    font-weight: 500;
    font-size: 14px;
    border: 1px solid rgba(255,255,255,0.2);
}

.info-chip i {
    font-size: 16px;
}

/* Stats */
.stats-container {
    display: flex;
    gap: 16px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 24px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.2);
    min-width: 160px;
}

.stat-item i {
    font-size: 24px;
    color: white;
    margin-bottom: 8px;
}

.stat-label {
    color: white;
    font-weight: 600;
    font-size: 13px;
    text-align: center;
}

/* Info Card */
.info-card {
    border-radius: 16px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateX(4px);
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #10b981, #059669);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
}

.info-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.info-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
}

/* Notice Card */
.notice-card {
    border-radius: 16px;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #fbbf24;
}

.notice-icon {
    width: 48px;
    height: 48px;
    background: rgba(251, 191, 36, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notice-icon i {
    font-size: 24px;
    color: #d97706;
}

/* Form Sections */
.form-section {
    margin-bottom: 32px;
}

.form-section-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-section-label.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 4px;
}

.form-section-label i {
    color: #667eea;
    font-size: 16px;
}

.form-input-readonly {
    background: #f8f9fa;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #6b7280;
    cursor: not-allowed;
}

.form-input-readonly:disabled {
    opacity: 1;
}

.form-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, #e5e7eb, transparent);
    margin: 32px 0;
}

/* Profile Preview */
.profile-preview {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    overflow: hidden;
    border: 3px solid #e5e7eb;
    flex-shrink: 0;
}

.profile-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Cards */
.card {
    border-radius: 16px;
}

.card-header {
    border-radius: 16px 16px 0 0;
}

/* Buttons */
.btn {
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-light:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 991px) {
    .avatar-wrapper {
        width: 120px;
        height: 120px;
    }
    
    .stat-item {
        min-width: 140px;
        padding: 12px 16px;
    }
    
    .profile-hero .card-body {
        padding: 24px !important;
    }
}

@media (max-width: 576px) {
    .stats-container {
        width: 100%;
    }
    
    .stat-item {
        flex: 1;
        min-width: auto;
    }
    
    .info-chip {
        font-size: 13px;
        padding: 6px 12px;
    }
}
</style>