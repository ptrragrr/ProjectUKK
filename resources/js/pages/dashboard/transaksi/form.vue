<script setup lang="ts">
import { Field, ErrorMessage, Form as VForm, useForm } from 'vee-validate';
import * as Yup from 'yup';
import axios from '@/libs/axios';
import { ref, reactive, watch } from 'vue';
import { toast } from 'vue3-toastify';

const emit = defineEmits(['close', 'refresh']);

const props = defineProps({
  selected: {
    type: String,
    default: '',
  },
});

const formValues = reactive({
  user_id: '',
  kode_transaksi: '',
  metode_pembayaran: '',
  total_harga: '',
  bayar: '',
  status: 'pending',
});

const schema = Yup.object({
  user_id: Yup.number().required(),
  kode_transaksi: Yup.string().required(),
  metode_pembayaran: Yup.string().required(),
  total_harga: Yup.number().required(),
  bayar: Yup.number().required(),
  status: Yup.string().oneOf(['pending', 'paid', 'cancelled']).required(),
});

useForm({ validationSchema: schema, initialValues: formValues });

const getData = async () => {
  if (!props.selected) return;
  try {
    const { data } = await axios.get(`/transaksi/${props.selected}`);
    Object.assign(formValues, data);
  } catch (err) {
    toast.error('Gagal mengambil data transaksi');
  }
};

watch(() => props.selected, getData, { immediate: true });

const onSubmit = async (values: typeof formValues) => {
  try {
    if (props.selected) {
      await axios.post(`/transaksi/${props.selected}?_method=PUT`, values);
      toast.success('Transaksi berhasil diperbarui');
    } else {
      await axios.post('/transaksi', values);
      toast.success('Transaksi berhasil ditambahkan');
    }
    emit('refresh');
    emit('close');
  } catch (err) {
    toast.error('Gagal menyimpan transaksi');
  }
};
</script>

<template>
  <VForm v-slot="{ handleSubmit }">
    <form @submit.prevent="handleSubmit(onSubmit)" class="form card">
      <div class="card-header">
        <h2>{{ props.selected ? 'Edit' : 'Tambah' }} Transaksi</h2>
      </div>
      <div class="card-body row px-4 pt-4">
        <div class="col-md-6 mb-4">
          <label>User ID</label>
          <Field name="user_id" v-model="formValues.user_id" type="number" class="form-control" />
          <ErrorMessage name="user_id" class="text-danger" />
        </div>
        <div class="col-md-6 mb-4">
          <label>Kode Transaksi</label>
          <Field name="kode_transaksi" v-model="formValues.kode_transaksi" type="text" class="form-control" />
          <ErrorMessage name="kode_transaksi" class="text-danger" />
        </div>
        <div class="col-md-6 mb-4">
          <label>Metode Pembayaran</label>
          <Field name="metode_pembayaran" v-model="formValues.metode_pembayaran" type="text" class="form-control" />
          <ErrorMessage name="metode_pembayaran" class="text-danger" />
        </div>
        <div class="col-md-6 mb-4">
          <label>Total Harga</label>
          <Field name="total_harga" v-model="formValues.total_harga" type="number" class="form-control" />
          <ErrorMessage name="total_harga" class="text-danger" />
        </div>
        <div class="col-md-6 mb-4">
          <label>Bayar</label>
          <Field name="bayar" v-model="formValues.bayar" type="number" class="form-control" />
          <ErrorMessage name="bayar" class="text-danger" />
        </div>
        <div class="col-md-6 mb-4">
          <label>Status</label>
          <Field name="status" as="select" v-model="formValues.status" class="form-control">
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="cancelled">Cancelled</option>
          </Field>
          <ErrorMessage name="status" class="text-danger" />
        </div>
      </div>
      <div class="card-footer d-flex">
        <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
      </div>
    </form>
  </VForm>
</template>
