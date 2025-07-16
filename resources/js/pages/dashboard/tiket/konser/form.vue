<script setup lang="ts">
import { Field, ErrorMessage, Form as VForm } from 'vee-validate';
import * as Yup from 'yup';
import axios from '@/libs/axios';
import { useRouter } from 'vue-router';
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import { watch, onMounted } from "vue";

const emit = defineEmits(['close', 'refresh']);
const router = useRouter();
const file = ref<File | null>(null);

const formSchema = Yup.object({
  nama_konser: Yup.string().required(),
  lokasi: Yup.string().required(),
  tanggal: Yup.date().required(),
  deskripsi: Yup.string().required(),
});

const props = defineProps({
  selected: {
    type: String,
    default: "",
  },
});

const values = ref({
  nama_konser: "",
  lokasi: "",
  tanggal: "",
  deskripsi: "",
});

const onFileChange = (e: Event) => {
  const input = e.target as HTMLInputElement;
  if (input.files && input.files.length > 0) {
    file.value = input.files[0];
  }
};

watch(
  () => props.selected,
  async (val) => {
    if (val) {
      try {
        const res = await axios.get(`/konser/${val}`);
        values.value = {
          nama_konser: res.data.nama_konser,
          lokasi: res.data.lokasi,
          tanggal: res.data.tanggal,
          deskripsi: res.data.deskripsi,
        };
      } catch (err) {
        toast.error("Gagal mengambil data konser.");
      }
    } else {
      // Reset saat tambah data
      values.value = {
        nama_konser: "",
        lokasi: "",
        tanggal: "",
        deskripsi: "",
      };
    }
  },
  { immediate: true }
);

// const onSubmit = async (values: any) => {
//   const formData = new FormData();
//   formData.append('nama_konser', values.nama_konser);
//   formData.append('lokasi', values.lokasi);
//   formData.append('tanggal', values.tanggal);
//   formData.append('deskripsi', values.deskripsi);
//   if (file.value) {
//     formData.append('banner', file.value);
//   }

//   try {
//     await axios.post('/konser', formData);
//     toast.success('Konser berhasil ditambahkan!');
//     emit('refresh');
//     emit('close');
//   } catch (err) {
//     toast.error('Gagal menambahkan konser.');
//   }
// };
const onSubmit = async (formValues: any) => {
  const formData = new FormData();
  formData.append("nama_konser", formValues.nama_konser);
  formData.append("lokasi", formValues.lokasi);
  formData.append("tanggal", formValues.tanggal);
  formData.append("deskripsi", formValues.deskripsi);
  if (file.value) formData.append("banner", file.value);

  try {
    if (props.selected) {
      await axios.post(`/konser/${props.selected}?_method=PUT`, formData); // ← untuk update
      toast.success("Konser berhasil diupdate!");
    } else {
      await axios.post("/konser", formData); // ← untuk create
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
  <VForm :validation-schema="formSchema" v-slot="{ handleSubmit }">
    <form @submit.prevent="handleSubmit(onSubmit)" id="form-konser" class="form card mb-10">
      <div class="card-header align-items-center">
        <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Konser</h2>
        <button
          type="button"
          class="btn btn-sm btn-light-danger ms-auto"
          @click="emit('close')"
        >
          Batal <i class="la la-times-circle p-0"></i>
        </button>
      </div>

      <div class="row px-4 pt-4">
        <!-- Nama Konser -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Nama Konser</label>
          <Field
            name="nama_konser"
            as="input"
            type="text"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan nama konser"
          />
          <ErrorMessage name="nama_konser" class="text-danger ps-2 text-sm" />
        </div>

        <!-- Lokasi -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Lokasi</label>
          <Field
            name="lokasi"
            as="input"
            type="text"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan lokasi konser"
          />
          <ErrorMessage name="lokasi" class="text-danger ps-2 text-sm" />
        </div>

        <!-- Tanggal -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Tanggal</label>
          <Field
            name="tanggal"
            as="input"
            type="date"
            class="form-control form-control-lg form-control-solid"
          />
          <ErrorMessage name="tanggal" class="text-danger ps-2 text-sm" />
        </div>

        <!-- Deskripsi -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Deskripsi</label>
          <Field
            name="deskripsi"
            as="textarea"
            rows="3"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan deskripsi konser"
          />
          <ErrorMessage name="deskripsi" class="text-danger ps-2 text-sm" />
        </div>

        <!-- Banner -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 ps-2">Banner</label>
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
