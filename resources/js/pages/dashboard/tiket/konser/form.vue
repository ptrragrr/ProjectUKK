<script setup lang="ts">
import { Field, ErrorMessage, Form as VForm, useForm } from "vee-validate";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { ref, watch, reactive, nextTick } from "vue";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";

const emit = defineEmits(["close", "refresh"]);

const props = defineProps({
    selected: {
        type: String,
        default: "",
    },
});

const file = ref<File | null>(null);
const bannerPath = ref("");

// ✅ Buat form state reaktif
const formValues = reactive({
    nama_konser: "",
    lokasi: "",
    tanggal: "",
    deskripsi: "",
});

// ✅ Yup schema
const formSchema = Yup.object({
    nama_konser: Yup.string().required("Nama konser wajib diisi"),
    lokasi: Yup.string().required("Lokasi wajib diisi"),
    tanggal: Yup.string().required("Tanggal wajib diisi"),
    deskripsi: Yup.string().required("Deskripsi wajib diisi"),
});

// ✅ Bind reactive form to vee-validate
useForm({
    validationSchema: formSchema,
    initialValues: formValues,
});

const getEdit = async () => {
    if (!props.selected) {
        Object.assign(formValues, {
            nama_konser: "",
            lokasi: "",
            tanggal: "",
            deskripsi: "",
        });
        bannerPath.value = "";
        return;
    }

    const formEl = document.getElementById("form-konser");
    if (formEl) block(formEl);

    try {
        const { data } = await axios.get(`/konser/${props.selected}`);
        const konser = data.konser;

        await nextTick();

        Object.assign(formValues, {
            nama_konser: konser.nama_konser || "",
            lokasi: konser.lokasi || "",
            tanggal: konser.tanggal?.slice(0, 10) || "",
            deskripsi: konser.deskripsi || "",
        });

        bannerPath.value = konser.banner || "";
    } catch (err) {
        toast.error(
            err.response?.data?.message || "Gagal mengambil data konser."
        );
    } finally {
        if (formEl) unblock(formEl);
    }
};

watch(() => props.selected, getEdit, { immediate: true });

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        file.value = input.files[0];
    }
};

const onSubmit = async (values: typeof formValues) => {
    const formData = new FormData();
    formData.append("nama_konser", values.nama_konser);
    formData.append("lokasi", values.lokasi);
    formData.append("tanggal", values.tanggal);
    formData.append("deskripsi", values.deskripsi);

    if (file.value) {
        formData.append("banner", file.value);
    }

    try {
        if (props.selected) {
            await axios.post(`/konser/${props.selected}?_method=PUT`, formData);
            toast.success("Konser berhasil diupdate!");
        } else {
            await axios.post("/konser", formData);
            toast.success("Konser berhasil ditambahkan!");
        }

        emit("refresh");
        emit("close");
    } catch (err) {
        toast.error("Gagal menyimpan konser.");
    }
};
</script>

<template>
    <VForm v-slot="{ handleSubmit }">
        <form
            @submit.prevent="handleSubmit(onSubmit)"
            class="form card mb-10"
            id="form-konser"
        >
            <div class="card-header align-items-center">
                <h2 class="mb-0">
                    {{ props.selected ? "Edit" : "Tambah" }} Konser
                </h2>
                <button
                    type="button"
                    class="btn btn-sm btn-light-danger ms-auto"
                    @click="emit('close')"
                >
                    Batal <i class="la la-times-circle p-0"></i>
                </button>
            </div>

            <div class="row px-4 pt-4">
                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Nama Konser</label
                    >
                    <Field
                        name="nama_konser"
                        v-model="formValues.nama_konser"
                        as="input"
                        type="text"
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan nama konser"
                    />
                    <ErrorMessage
                        name="nama_konser"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Lokasi</label
                    >
                    <Field
                        name="lokasi"
                        v-model="formValues.lokasi"
                        as="input"
                        type="text"
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan lokasi konser"
                    />
                    <ErrorMessage
                        name="lokasi"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Tanggal</label
                    >
                    <Field
                        name="tanggal"
                        v-model="formValues.tanggal"
                        as="input"
                        type="date"
                        class="form-control form-control-lg form-control-solid"
                    />
                    <ErrorMessage
                        name="tanggal"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Deskripsi</label
                    >
                    <Field
                        name="deskripsi"
                        v-model="formValues.deskripsi"
                        as="textarea"
                        rows="3"
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan deskripsi konser"
                    />
                    <ErrorMessage
                        name="deskripsi"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <!-- <div class="col-md-6 mb-3" v-if="props.selected && bannerPath">
          <label class="form-label fw-bold fs-6 ps-2">Banner Saat Ini</label>
          <img :src="bannerPath" alt="Preview Banner" style="max-height: 100px; border-radius: 4px;" />
        </div> -->
                <div
                    class="col-md-12 mb-3 text-center"
                    v-if="props.selected && bannerPath"
                >
                    <label class="form-label fw-bold fs-6 ps-2 d-block"
                        >Banner Saat Ini</label
                    >
                    <div class="mt-2">
                        <img
                            :src="bannerPath"
                            alt="Preview Banner"
                            style="max-height: 180px; border-radius: 8px"
                        />
                    </div>
                </div>

                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 ps-2"
                        >Upload Banner Baru</label
                    >
                    <input
                        type="file"
                        @change="onFileChange"
                        class="form-control form-control-lg form-control-solid"
                    />
                </div>
            </div>

            <div class="card-footer d-flex">
                <button type="submit" class="btn btn-primary btn-sm ms-auto">
                    Simpan
                </button>
            </div>
        </form>
    </VForm>
</template>
