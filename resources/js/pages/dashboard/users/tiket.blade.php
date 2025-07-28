@extends('layouts.app')

@section('title', 'Pilih Tiket')

@section('content')
<style>
  :root {
    --primary-green: #1C290D;
    --sage-green: #676F53;
    --warm-beige: #B3B49A;
    --cream: #FEFAE0;
    --taupe: #A19379;
    --brown: #736046;
    --dark-brown: #381D03;
    --white: #ffffff;
    --shadow: rgba(28, 41, 13, 0.15);
    --shadow-hover: rgba(28, 41, 13, 0.25);
  }

  body {
    background: linear-gradient(135deg, var(--cream) 0%, var(--warm-beige) 100%);
    min-height: 100vh;
  }

  .poster-banner {
    width: 100%;
    max-height: 450px;
    overflow: hidden;
    border-radius: 20px;
    margin: 120px auto 2rem auto;
    box-shadow: 0 15px 40px var(--shadow);
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .poster-banner:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 60px var(--shadow-hover);
  }

  .poster-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(28, 41, 13, 0.1), transparent);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .poster-banner:hover::before {
    opacity: 1;
  }

  .poster-banner img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
    border-radius: 20px;
  }

  .ticket-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2.5rem;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, var(--cream) 0%, rgba(254, 250, 224, 0.8) 50%, var(--warm-beige) 100%);
    border-radius: 30px 30px 0 0;
    margin-top: -30px;
    position: relative;
    z-index: 2;
  }

  .ticket-list {
    flex: 2;
    min-width: 320px;
  }

  .ticket-card {
    background: linear-gradient(135deg, var(--white) 0%, var(--cream) 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px var(--shadow);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .ticket-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(103, 111, 83, 0.1), transparent);
    transition: left 0.5s;
  }

  .ticket-card:hover::before {
    left: 100%;
  }

  .ticket-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px var(--shadow-hover);
    border-color: var(--sage-green);
  }

  .ticket-card h3 {
    margin: 0;
    font-size: 1.4rem;
    color: var(--primary-green);
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
  }

  .ticket-description {
    margin: 0.8rem 0;
    font-size: 1rem;
    color: var(--brown);
    line-height: 1.6;
  }

  .ticket-price {
    font-weight: 800;
    margin: 1rem 0;
    color: var(--primary-green);
    font-size: 1.3rem;
    text-shadow: 1px 1px 2px rgba(28, 41, 13, 0.1);
  }

  .qty-control {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
    justify-content: center;
  }

  .qty-control button {
    width: 45px;
    height: 45px;
    border: 2px solid var(--sage-green);
    border-radius: 12px;
    background: linear-gradient(135deg, var(--white), var(--cream));
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--primary-green);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .qty-control button:hover {
    background: linear-gradient(135deg, var(--sage-green), var(--primary-green));
    color: var(--cream);
    transform: scale(1.1);
    box-shadow: 0 5px 15px var(--shadow);
  }

  .qty-control button:active {
    transform: scale(0.95);
  }

  .qty-input {
    width: 60px;
    height: 45px;
    text-align: center;
    border: 2px solid var(--warm-beige);
    background: var(--white);
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--primary-green);
    border-radius: 12px;
    transition: all 0.3s ease;
  }

  .qty-input:focus {
    outline: none;
    border-color: var(--sage-green);
    box-shadow: 0 0 10px rgba(103, 111, 83, 0.3);
  }

  .order-summary {
    flex: 1;
    min-width: 300px;
    background: linear-gradient(135deg, var(--white) 0%, var(--cream) 100%);
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 15px 40px var(--shadow);
    border: 2px solid var(--warm-beige);
    position: sticky;
    top: 140px;
    height: fit-content;
  }

  .summary-title {
    font-weight: 800;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    color: var(--primary-green);
    text-align: center;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--warm-beige);
  }

  .summary-item {
    display: flex;
    justify-content: space-between;
    font-size: 1rem;
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    color: var(--brown);
    font-weight: 500;
  }

  .summary-item:not(:last-child) {
    border-bottom: 1px solid rgba(179, 180, 154, 0.3);
  }

  .summary-total {
    border-top: 3px solid var(--sage-green);
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    font-weight: 800;
    font-size: 1.3rem;
    color: var(--primary-green);
    text-align: center;
    background: linear-gradient(135deg, var(--cream), var(--warm-beige));
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
  }

  .btn-checkout {
    margin-top: 1.5rem;
    width: 100%;
    background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
    color: var(--cream);
    border: none;
    padding: 1rem 1.5rem;
    font-weight: 700;
    border-radius: 15px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 8px 25px var(--shadow);
  }

  .btn-checkout:hover {
    background: linear-gradient(135deg, var(--sage-green), var(--brown));
    transform: translateY(-3px);
    box-shadow: 0 15px 35px var(--shadow-hover);
  }

  .btn-checkout:active {
    transform: translateY(-1px);
  }

  /* Floating animation for decorative elements */
  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
  }

  .ticket-container::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 10%;
    width: 30px;
    height: 30px;
    background: var(--sage-green);
    border-radius: 50%;
    opacity: 0.3;
    animation: float 4s ease-in-out infinite;
  }

  .ticket-container::after {
    content: '';
    position: absolute;
    top: -10px;
    right: 15%;
    width: 20px;
    height: 20px;
    background: var(--warm-beige);
    border-radius: 50%;
    opacity: 0.4;
    animation: float 6s ease-in-out infinite reverse;
  }

  @media (max-width: 768px) {
    .ticket-container {
      flex-direction: column;
      align-items: stretch;
      padding: 2rem 1rem;
    }

    .poster-banner {
      margin: 100px auto 1rem auto;
      border-radius: 15px;
    }

    .ticket-card {
      padding: 1.5rem;
      border-radius: 15px;
    }

    .order-summary {
      padding: 2rem;
      border-radius: 15px;
      position: static;
    }

    .qty-control {
      gap: 0.8rem;
    }

    .qty-control button {
      width: 40px;
      height: 40px;
    }

    .qty-input {
      width: 50px;
      height: 40px;
    }
  }
</style>

<!-- Poster Event -->
<div class="poster-banner">
  <img src="{{ asset('images/banner.jpeg') }}" alt="SOD Festival Poster">
</div>

<!-- Daftar Tiket + Ringkasan -->
<div class="ticket-container">
  <!-- LEFT SIDE - Ticket List -->
  <div class="ticket-list">
    <div class="ticket-card" data-price="125000">
      <h3>Presale Terakhir - SOD Day 1</h3>
      <p class="ticket-description">Tiket berlaku untuk 1 orang pada 1 Agustus 2025.</p>
      <div class="ticket-price">Rp 125.000</div>
      <div class="qty-control">
        <button class="btn-minus">-</button>
        <input type="text" class="qty-input" value="0" readonly>
        <button class="btn-plus">+</button>
      </div>
    </div>

    <div class="ticket-card" data-price="200000">
      <h3>Presale Terakhir - VOD Day 1</h3>
      <p class="ticket-description">Tiket online untuk 1 Agustus 2025.</p>
      <div class="ticket-price">Rp 200.000</div>
      <div class="qty-control">
        <button class="btn-minus">-</button>
        <input type="text" class="qty-input" value="0" readonly>
        <button class="btn-plus">+</button>
      </div>
    </div>
  </div>

  <!-- RIGHT SIDE - Order Summary -->
  <div class="order-summary">
    <div class="summary-title">Order Summary</div>
    <div id="summary-list"></div>
    <div class="summary-item">
      <span>Subtotal</span><span id="subtotal">Rp 0</span>
    </div>
    <div class="summary-item">
      <span>Tax (10%)</span><span id="tax">Rp 0</span>
    </div>
    <div class="summary-item">
      <span>Platform Fee</span><span id="fee">Rp 0</span>
    </div>
    <div class="summary-total">
      Total: <span id="total">Rp 0</span>
    </div>
    <button class="btn-checkout" id="checkout-btn">Checkout (0 tiket)</button>
  </div>
</div>

<script>
  const cards = document.querySelectorAll('.ticket-card');
  const summaryList = document.getElementById('summary-list');
  const subtotalEl = document.getElementById('subtotal');
  const taxEl = document.getElementById('tax');
  const feeEl = document.getElementById('fee');
  const totalEl = document.getElementById('total');
  const checkoutBtn = document.getElementById('checkout-btn');

  function formatRupiah(value) {
    return 'Rp ' + value.toLocaleString('id-ID');
  }

  function updateSummary() {
    let subtotal = 0;
    let ticketCount = 0;
    summaryList.innerHTML = '';

    cards.forEach(card => {
      const qty = parseInt(card.querySelector('.qty-input').value);
      const price = parseInt(card.dataset.price);
      const title = card.querySelector('h3').innerText;

      if (qty > 0) {
        const total = qty * price;
        subtotal += total;
        ticketCount += qty;

        const item = document.createElement('div');
        item.classList.add('summary-item');
        item.innerHTML = `<span>${title}</span><span>${formatRupiah(price)} × ${qty}</span>`;
        summaryList.appendChild(item);
      }
    });

    const tax = subtotal * 0.10;
    const fee = subtotal * 0.05;
    const total = subtotal + tax + fee;

    subtotalEl.innerText = formatRupiah(subtotal);
    taxEl.innerText = formatRupiah(tax);
    feeEl.innerText = formatRupiah(fee);
    totalEl.innerText = formatRupiah(total);
    checkoutBtn.innerText = `Checkout (${ticketCount} tiket)`;
  }

  cards.forEach(card => {
    const plusBtn = card.querySelector('.btn-plus');
    const minusBtn = card.querySelector('.btn-minus');
    const qtyInput = card.querySelector('.qty-input');

    plusBtn.addEventListener('click', () => {
      qtyInput.value = parseInt(qtyInput.value) + 1;
      updateSummary();
    });

    minusBtn.addEventListener('click', () => {
      qtyInput.value = Math.max(0, parseInt(qtyInput.value) - 1);
      updateSummary();
    });
  });

  updateSummary();
</script>

@guest
<script>
  document.getElementById('checkout-btn').addEventListener('click', function () {
    if (confirm("Anda harus login untuk melanjutkan ke checkout. Login sekarang?")) {
      window.location.href = "{{ route('login') }}";
    }
  });
</script>
@endguest

@auth
<script>
  document.getElementById('checkout-btn').addEventListener('click', function () {
    const ticketCount = [...document.querySelectorAll('.qty-input')].reduce((acc, input) => {
      return acc + parseInt(input.value);
    }, 0);

    if (ticketCount === 0) {
      alert("Silakan pilih minimal 1 tiket sebelum checkout.");
      return;
    }

    window.location.href = "{{ route('checkout.page') }}"; // ganti sesuai rute kamu
  });
</script>
@endauth
@endsection