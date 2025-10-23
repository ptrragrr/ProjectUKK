<template>
  <div class="ticket-card" :class="{ 'sold-out': ticket.stok_tiket === 0 }">
    <div class="ticket-badge">
      <span class="badge-icon">🎫</span>
      <span class="badge-text">{{ ticket.jenis_tiket }}</span>
    </div>
    
    <h3 class="ticket-title">{{ ticket.nama_event }}</h3>
    <p class="ticket-desc">{{ ticket.deskripsi }}</p>
    
    <div class="ticket-info">
      <div class="info-row price-row">
        <span class="info-label">Harga</span>
        <strong class="info-value price">{{ formatRupiah(ticket.harga_tiket) }}</strong>
      </div>
      <div class="info-row stock-row">
        <span class="info-label">Stok</span>
        <strong class="info-value stock" :class="{ 'low-stock': ticket.stok_tiket < 10 }">
          {{ ticket.stok_tiket }} tersisa
        </strong>
      </div>
    </div>

    <div class="qty-controls">
      <button 
        class="btn-minus" 
        @click="decrement" 
        :disabled="qty === 0"
        aria-label="Kurangi jumlah"
      >
        <span class="btn-text">−</span>
      </button>
      <div class="qty-display">
        <input type="text" :value="qty" readonly />
        <span class="qty-label">Tiket</span>
      </div>
      <button 
        class="btn-plus" 
        @click="increment" 
        :disabled="qty >= ticket.stok_tiket"
        aria-label="Tambah jumlah"
      >
        <span class="btn-text">+</span>
      </button>
    </div>
    
    <div v-if="qty > 0" class="subtotal">
      <span class="subtotal-label">Subtotal</span>
      <span class="subtotal-value">{{ formatRupiah(ticket.harga_tiket * qty) }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from "vue";

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["updateQty"]);
const qty = ref(props.ticket.qty ?? 0);

watch(qty, (val) => emit("updateQty", props.ticket.id, val));

const increment = () => {
  if (qty.value < props.ticket.stok_tiket) qty.value++;
};
const decrement = () => {
  if (qty.value > 0) qty.value--;
};

const formatRupiah = (v: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(v);
};
</script>

<style scoped>
.ticket-card {
  border: 2px solid rgba(179, 180, 154, 0.3);
  padding: 24px;
  border-radius: 20px;
  background: white;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(28, 41, 13, 0.05);
  position: relative;
  overflow: hidden;
}

.ticket-card:hover {
  border-color: #A19379;
  box-shadow: 0 8px 24px rgba(28, 41, 13, 0.1);
  transform: translateY(-4px);
}

.ticket-card.sold-out {
  opacity: 0.6;
  pointer-events: none;
  background: linear-gradient(135deg, #F5F5F5 0%, #E5E5E5 100%);
}

.ticket-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: linear-gradient(135deg, #A19379, #736046);
  color: white;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(161, 147, 121, 0.3);
}

.badge-icon {
  font-size: 14px;
}

.badge-text {
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.ticket-title {
  font-size: 20px;
  font-weight: 800;
  color: #381D03;
  margin-bottom: 12px;
  line-height: 1.3;
  letter-spacing: -0.01em;
}

.ticket-desc {
  font-size: 14px;
  color: #736046;
  margin-bottom: 20px;
  line-height: 1.6;
  min-height: 42px;
}

.ticket-info {
  background: #FEFAE0;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 20px;
  border: 1px solid rgba(179, 180, 154, 0.2);
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.info-row:not(:last-child) {
  border-bottom: 1px solid rgba(179, 180, 154, 0.2);
  margin-bottom: 8px;
  padding-bottom: 12px;
}

.info-label {
  font-size: 14px;
  color: #736046;
  font-weight: 500;
}

.info-value {
  font-size: 16px;
  color: #381D03;
  font-weight: 700;
}

.info-value.price {
  color: #A19379;
  font-size: 18px;
}

.info-value.stock {
  color: #676F53;
}

.info-value.stock.low-stock {
  color: #DC2626;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}

.qty-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-top: 20px;
}

.btn-minus,
.btn-plus {
  width: 44px;
  height: 44px;
  font-size: 24px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(161, 147, 121, 0.3);
}

.btn-plus {
  background: linear-gradient(135deg, #A19379, #736046);
  color: white;
}

.btn-plus:hover:not(:disabled) {
  background: linear-gradient(135deg, #8B7C65, #5C4D39);
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 4px 12px rgba(161, 147, 121, 0.4);
}

.btn-plus:active:not(:disabled) {
  transform: translateY(0) scale(0.95);
}

.btn-minus {
  background: linear-gradient(135deg, #B3B49A, #9A9B82);
  color: white;
}

.btn-minus:hover:not(:disabled) {
  background: linear-gradient(135deg, #8C8D76, #73745F);
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 4px 12px rgba(179, 180, 154, 0.4);
}

.btn-minus:active:not(:disabled) {
  transform: translateY(0) scale(0.95);
}

.btn-minus:disabled,
.btn-plus:disabled {
  background: linear-gradient(135deg, #E5E5E5, #CCCCCC);
  color: #999999;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
  opacity: 0.5;
}

.btn-text {
  font-size: 20px;
  line-height: 1;
}

.qty-display {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.qty-display input {
  width: 80px;
  height: 44px;
  text-align: center;
  font-size: 20px;
  font-weight: 800;
  border: 2px solid rgba(179, 180, 154, 0.3);
  border-radius: 12px;
  background: white;
  color: #381D03;
  outline: none;
  transition: all 0.3s ease;
}

.qty-display input:focus {
  border-color: #A19379;
  box-shadow: 0 0 0 3px rgba(161, 147, 121, 0.1);
}

.qty-label {
  font-size: 11px;
  color: #736046;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.subtotal {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid rgba(179, 180, 154, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.subtotal-label {
  font-size: 14px;
  color: #736046;
  font-weight: 600;
}

.subtotal-value {
  font-size: 20px;
  font-weight: 800;
  color: #676F53;
  background: linear-gradient(135deg, #A19379, #736046);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Responsive */
@media (max-width: 640px) {
  .ticket-card {
    padding: 20px;
    border-radius: 16px;
  }

  .ticket-title {
    font-size: 18px;
    margin-bottom: 10px;
  }

  .ticket-desc {
    font-size: 13px;
    margin-bottom: 16px;
    min-height: 39px;
  }

  .ticket-info {
    padding: 14px;
    margin-bottom: 16px;
  }

  .info-label {
    font-size: 13px;
  }

  .info-value {
    font-size: 15px;
  }

  .info-value.price {
    font-size: 16px;
  }

  .qty-controls {
    gap: 10px;
    margin-top: 16px;
  }

  .btn-minus,
  .btn-plus {
    width: 40px;
    height: 40px;
    font-size: 20px;
  }

  .qty-display input {
    width: 70px;
    height: 40px;
    font-size: 18px;
  }

  .subtotal {
    margin-top: 16px;
    padding-top: 16px;
  }

  .subtotal-label {
    font-size: 13px;
  }

  .subtotal-value {
    font-size: 18px;
  }
}

@media (max-width: 480px) {
  .ticket-card {
    padding: 16px;
  }

  .ticket-badge {
    padding: 5px 14px;
    font-size: 11px;
    margin-bottom: 14px;
  }

  .ticket-title {
    font-size: 16px;
  }

  .ticket-desc {
    font-size: 12px;
    min-height: 36px;
  }

  .btn-minus,
  .btn-plus {
    width: 36px;
    height: 36px;
    font-size: 18px;
  }

  .qty-display input {
    width: 60px;
    height: 36px;
    font-size: 16px;
  }

  .qty-label {
    font-size: 10px;
  }

  .subtotal-value {
    font-size: 16px;
  }
}
</style>