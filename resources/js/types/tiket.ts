export interface Tiket {
    id: number;                  // Ganti BigInteger → number
    nama_destinasi: string;
    harga_tiket: number;
    stok_tiket: number;          // Ganti BigInteger → number
}
