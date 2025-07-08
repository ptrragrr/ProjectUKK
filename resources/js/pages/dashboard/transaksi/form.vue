<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type { Transaksi } from "@/types";
import axios from "axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const props = defineProps<{
  selectedTicket: Transaksi | null;
}>();

const emit = defineEmits(["refresh"]);
const router = useRouter();

const jumlah = ref(1);
const keranjang = ref<{ ticket: Transaksi; jumlah: number }[]>([]);
const metodePembayaran = ref("Tunai");
const nama_kasir = ref("Kasir");

// Ambil nama user saat mount
onMounted(async () => {
  const token = localStorage.getItem("token");
  try {
    const res = await axios.get("/api/user", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    nama_kasir.value = res.data.name || res.data.nama || "Kasir";
  } catch (error: any) {
    console.warn("Gagal mengambil user, fallback ke 'Kasir'");
    nama_kasir.value = "Kasir";
  }
});

const totalHarga = computed(() =>
  keranjang.value.reduce(
    (total, item) => total + item.ticket.harga_ticket * item.jumlah,
    0
  )
);

const tambahKeKeranjang = () => {
  if (!props.selectedTicket || jumlah.value < 1) return;

  const idx = keranjang.value.findIndex(
    (item) => item.ticket.id === props.selectedTicket!.id
  );

  if (idx !== -1) {
    keranjang.value[idx].jumlah += jumlah.value;
  } else {
    keranjang.value.push({
      ticket: props.selectedTicket,
      jumlah: jumlah.value,
    });
  }

  jumlah.value = 1;
  emit("refresh");
};

const hapusItem = (id: number) => {
  keranjang.value = keranjang.value.filter((item) => item.ticket.id !== id);
};

const batalTransaksi = () => {
  keranjang.value = [];
  jumlah.value = 1;
  metodePembayaran.value = "Tunai";
};

const simpanSemuaTransaksi = async () => {
  if (metodePembayaran.value === "Tunai" && totalHarga.value <= 0) {
    alert("Total harga tidak valid");
    return;
  }

  const token = localStorage.getItem("token");

  const payload = {
    nama_kasir: nama_kasir.value,
    metode_pembayaran: metodePembayaran.value,
    keranjang: JSON.stringify(
      keranjang.value.map((item) => ({
        id_ticket: item.ticket.id,
        jumlah: item.jumlah,
        harga_ticket: item.ticket.harga_ticket,
        total_harga: item.ticket.harga_ticket * item.jumlah,
      }))
    ),
    total: totalHarga.value,
    bayar: totalHarga.value,
  };

  try {
    const response = await axios.post("/tickets/store", payload, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (response.data.success) {
      router.push({
        path: "/tickets/struk",
        query: {
          nama_kasir: nama_kasir.value,
          total_harga: totalHarga.value.toString(),
          detail_tiket: JSON.stringify(
            keranjang.value.map((item) => ({
              nama_ticket: item.ticket.nama_ticket,
              harga_ticket: item.ticket.harga_ticket,
              jumlah: item.jumlah,
              total_harga: item.ticket.harga_ticket * item.jumlah,
            }))
          ),
        },
      });
    } else {
      alert("Gagal menyimpan transaksi: " + response.data.message);
    }
  } catch (error: any) {
    alert("Gagal menyimpan transaksi: " + error.message);
    console.error(error);
  }
};
</script>

<template>
  <div class="card card-custom">
    <div class="card-header">
      <h3 class="card-title">Form Pemesanan Tiket</h3>
    </div>

    <div class="card-body">
      <div v-if="selectedTicket">
        <h5>{{ selectedTicket.nama_ticket }}</h5>
        <p>Harga: Rp {{ selectedTicket.harga_ticket.toLocaleString() }}</p>

        <div class="input-group my-2" style="max-width: 200px">
          <span class="input-group-text">Jumlah</span>
          <input type="number" v-model="jumlah" min="1" class="form-control" />
        </div>

        <button
          class="btn btn-primary mt-2"
          @click="tambahKeKeranjang"
          :disabled="selectedTicket.stok_ticket === 0"
        >
          {{
            selectedTicket.stok_ticket === 0
              ? "Stok Habis"
              : "Tambah ke Keranjang"
          }}
        </button>
      </div>

      <div v-else class="alert alert-warning">
        Silakan pilih tiket terlebih dahulu.
      </div>

      <div v-if="keranjang.length > 0" class="mt-4">
        <h5>Keranjang</h5>
        <ul class="list-group">
          <li
            v-for="item in keranjang"
            :key="item.ticket.id"
            class="d-flex justify-content-between align-items-center"
          >
            <div>
              {{ item.ticket.nama_ticket }} x {{ item.jumlah }}
              <button
                class="btn btn-sm btn-danger ms-2"
                @click="hapusItem(item.ticket.id)"
              >
                Hapus
              </button>
            </div>
            <span>
              Rp {{ (item.ticket.harga_ticket * item.jumlah).toLocaleString() }}
            </span>
          </li>
        </ul>

        <div class="mt-3 fw-bold">
          Total: Rp {{ totalHarga.toLocaleString() }}
        </div>

        <div class="d-flex mt-3">
          <button class="btn btn-danger me-2" @click="batalTransaksi">
            Batal
          </button>
          <button class="btn btn-success" @click="simpanSemuaTransaksi">
            Bayar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card-custom {
  border: 1px solid #ccc;
  border-radius: 8px;
}
</style>
