<x-anggota-layout activeMenu="cicilan" headerTitle="Cicilan — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Cicilan</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('cicilan.tagihan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Tagihan</a>
        <a href="{{ route('cicilan.bayar') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Bayar Cicilan</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Form Pembayaran Cicilan</h3>

    @error('jumlah_bayar')
        <div style="background:#FADBD8; border-radius:6px; padding:12px 16px; margin-bottom:16px; color:#A5301F; font-size:14px;">{{ $message }}</div>
    @enderror
    @error('bukti_transaksi')
        <div style="background:#FADBD8; border-radius:6px; padding:12px 16px; margin-bottom:16px; color:#A5301F; font-size:14px;">{{ $message }}</div>
    @enderror

    <div style="border:1px solid #F3B4B4; border-radius:6px; padding:16px 20px; margin-bottom:20px; max-width:600px;">
        <p style="margin:0; font-size:14px; color:#241412;">
            Angsuran ke-{{ $tagihanBerjalan['no_angsuran'] }} jatuh tempo {{ $tagihanBerjalan['jatuh_tempo']->format('d/m/y') }} — Cicilan: Rp {{ number_format($tagihanBerjalan['cicilan'], 0, ',', '.') }}
        </p>
        @if ($tagihanBerjalan['denda'] > 0)
            <div style="background:#FCE9C7; border-radius:6px; padding:8px 12px; margin-top:10px;">
                <p style="margin:0; color:#8A5A00; font-size:13px;">⚠ Denda keterlambatan: Rp {{ number_format($tagihanBerjalan['denda'], 0, ',', '.') }}</p>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('cicilan.bayar.store') }}" enctype="multipart/form-data" id="form-bayar">
        @csrf

        <div style="margin-bottom:16px; max-width:500px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">
                Metode Pembayaran <span style="font-weight:normal; color:#6B7280; font-size:12px;">*Per Angsuran Sesuai Jadwal / Pelunasan Sekaligus</span>
            </label>
            <select name="metode_pembayaran" id="metode_pembayaran"
                    style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                <option value="per_angsuran" {{ old('metode_pembayaran') === 'per_angsuran' ? 'selected' : '' }}>Per Angsuran Sesuai Jadwal</option>
                <option value="pelunasan_sekaligus" {{ old('metode_pembayaran') === 'pelunasan_sekaligus' ? 'selected' : '' }}>Pelunasan Sekaligus</option>
            </select>
        </div>

        <div style="margin-bottom:8px; max-width:500px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Jumlah Bayar (Rp)</label>
            <input type="text" inputmode="numeric" id="jumlah_bayar_display" readonly
                   value="Rp {{ number_format(old('jumlah_bayar', $tagihanBerjalan['total_bayar']), 0, ',', '.') }}"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box; background:#FDEEEE;">
            <input type="hidden" name="jumlah_bayar" id="jumlah_bayar" value="{{ old('jumlah_bayar', $tagihanBerjalan['total_bayar']) }}">
        </div>
        <p id="keterangan-jumlah" style="margin:0 0 16px; font-size:12px; color:#6B7280;">
            (otomatis: cicilan Rp {{ number_format($tagihanBerjalan['cicilan'], 0, ',', '.') }} + denda Rp {{ number_format($tagihanBerjalan['denda'], 0, ',', '.') }})
        </p>

        <div style="margin-bottom:16px; max-width:500px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Metode Setoran</label>
            <select name="metode_setoran" id="metode_setoran"
                    style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                <option value="QRIS" {{ old('metode_setoran', 'QRIS') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                <option value="Tunai" {{ old('metode_setoran') === 'Tunai' ? 'selected' : '' }}>Tunai</option>
            </select>
        </div>

        <div id="qris-area" style="margin-bottom:20px; max-width:500px;">
            <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:10px; padding:20px; text-align:center; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,.05);">
                <h4 style="margin:0 0 15px; color:#B91C1C; font-size:18px; font-weight:bold;">Scan QRIS Pembayaran</h4>
                <img src="{{ asset('images/qris.png') }}" alt="QRIS"
                     style="width:260px; max-width:100%; border:1px solid #E5E7EB; border-radius:8px; background:#fff; padding:10px;">
                <p style="margin-top:15px; color:#6B7280; font-size:13px; line-height:20px;">
                    Silakan scan QRIS menggunakan aplikasi
                    <b>Mobile Banking</b>,
                    <b>DANA</b>,
                    <b>OVO</b>,
                    <b>GoPay</b>,
                    <b>ShopeePay</b>,
                    atau aplikasi lain yang mendukung QRIS.
                </p>
            </div>
        </div>

        <div id="wrapper-upload" style="margin-bottom:20px; max-width:500px;">
            <label for="bukti_transaksi" style="display:block; border:1px dashed #F3B4B4; border-radius:6px; padding:24px; text-align:center; background:#FDEEEE; color:#B91C1C; font-size:14px; cursor:pointer;">
                ↑ Upload Bukti QRIS
            </label>
            <input type="file" name="bukti_transaksi" id="bukti_transaksi" accept="image/*" style="margin-top:8px;">
        </div>

        <button type="submit" style="padding:12px 24px; border:none; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px; cursor:pointer; margin-right:12px;">
            Bayar
        </button>
        <a href="{{ route('cicilan.bayar') }}" style="display:inline-block; padding:12px 24px; border:1px solid #999999; border-radius:6px; color:#999999; font-weight:bold; font-size:14px; text-decoration:none;">
            Batal
        </a>
    </form>

    <script>
        const cicilan = {{ $tagihanBerjalan['cicilan'] }};
        const denda = {{ $tagihanBerjalan['denda'] }};
        const sisaHutangTotal = {{ $sisaHutangTotal }};

        const metodePembayaran = document.getElementById('metode_pembayaran');
        const metodeSetoran = document.getElementById('metode_setoran');
        const jumlahBayar = document.getElementById('jumlah_bayar');
        const jumlahBayarDisplay = document.getElementById('jumlah_bayar_display');
        const qrisArea = document.getElementById('qris-area');
        const wrapperUpload = document.getElementById('wrapper-upload');
        const buktiInput = document.getElementById('bukti_transaksi');
        const keterangan = document.getElementById('keterangan-jumlah');

        function formatRupiah(angka) {
            return Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function updateJumlahOtomatis() {
            let nominal;
            if (metodePembayaran.value === 'pelunasan_sekaligus') {
                nominal = sisaHutangTotal;
                keterangan.textContent = '(otomatis: pelunasan sisa hutang penuh Rp ' + formatRupiah(sisaHutangTotal) + ')';
            } else {
                nominal = cicilan + denda;
                keterangan.textContent = '(otomatis: cicilan Rp ' + formatRupiah(cicilan) + ' + denda Rp ' + formatRupiah(denda) + ')';
            }
            jumlahBayar.value = nominal;
            jumlahBayarDisplay.value = 'Rp ' + formatRupiah(nominal);
        }

        function toggleUpload() {
            const wajib = metodeSetoran.value === 'QRIS';
            buktiInput.required = wajib;
            wrapperUpload.style.display = wajib ? 'block' : 'none';
            qrisArea.style.display = wajib ? 'block' : 'none';
        }

        metodePembayaran.addEventListener('change', updateJumlahOtomatis);
        metodeSetoran.addEventListener('change', toggleUpload);
        toggleUpload();
    </script>

</x-anggota-layout>