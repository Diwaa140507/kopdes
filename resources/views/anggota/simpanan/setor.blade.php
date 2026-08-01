<x-anggota-layout activeMenu="simpanan" headerTitle="Simpanan — Dashboard Anggota">

    <div style="display:flex; gap:20px; margin-bottom:24px;">
        <div style="flex:1; background:#FDEEEE; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#7F1D1D; font-size:13px;">Saldo Simpanan Wajib</p>
            <p style="margin:0; color:#241412; font-size:22px; font-weight:bold;">Rp {{ number_format($saldoWajib, 0, ',', '.') }}</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#7F1D1D; font-size:13px;">Saldo Simpanan Sukarela</p>
            <p style="margin:0; color:#241412; font-size:22px; font-weight:bold;">Rp {{ number_format($saldoSukarela, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tab --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('simpanan.setor') }}" style="padding:10px 20px; border:1px solid #B91C1C; border-radius:4px; text-decoration:none; color:#ffffff; font-weight:bold; background:#B91C1C;">Setoran</a>
        <a href="{{ route('simpanan.tarik') }}" style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">Penarikan</a>
        <a href="{{ route('simpanan.riwayat') }}" style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">Riwayat</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Form Setoran Simpanan</h3>

    <form method="POST" action="{{ route('simpanan.setor.store') }}" enctype="multipart/form-data" style="max-width:500px;" id="formSetor">
        @csrf

        <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Jenis Simpanan</label>
        <select name="jenis_simpanan" id="jenis_simpanan" required
                style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; margin-bottom:4px; font-family:Arial, sans-serif;">
            <option value="">-- Pilih --</option>
            <option value="Wajib" {{ old('jenis_simpanan') == 'Wajib' ? 'selected' : '' }}>Wajib</option>
            <option value="Sukarela" {{ old('jenis_simpanan') == 'Sukarela' ? 'selected' : '' }}>Sukarela</option>
        </select>
        <p style="font-size:12px; color:#9CA3AF; margin:0 0 16px;">Min. wajib: Rp 50.000 &nbsp;|&nbsp; Min. sukarela: Rp 10.000</p>

        <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Jumlah Setoran (Rp)</label>
        <input type="text" inputmode="numeric" id="jumlah_display" placeholder="Rp 0" required
       value="{{ old('jumlah') ? 'Rp ' . number_format(old('jumlah'), 0, ',', '.') : '' }}"
       style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; margin-bottom:4px; box-sizing:border-box; font-family:Arial, sans-serif;">
<input type="hidden" name="jumlah" id="jumlah">
<p id="jumlah_error" style="display:none; color:#B91C1C; font-size:12px; margin:0 0 16px;"></p>

        <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Metode Setoran</label>
        <select name="metode_setoran" id="metode_setoran" required
                style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; margin-bottom:16px; font-family:Arial, sans-serif;">
            <option value="">-- Pilih --</option>
            <option value="QRIS" {{ old('metode_setoran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
            <option value="Tunai" {{ old('metode_setoran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
        </select>

        <div id="uploadArea" style="display:none; margin-bottom:20px;">

    <div style="
        background:#ffffff;
        border:1px solid #E5E7EB;
        border-radius:10px;
        padding:20px;
        text-align:center;
        margin-bottom:20px;
        box-shadow:0 2px 8px rgba(0,0,0,.05);
    ">

        <h4 style="
            margin:0 0 15px;
            color:#B91C1C;
            font-size:18px;
            font-weight:bold;
        ">
            Scan QRIS Pembayaran
        </h4>

        <img src="{{ asset('images/qris.png') }}"
             alt="QRIS"
             style="
                width:260px;
                max-width:100%;
                border:1px solid #E5E7EB;
                border-radius:8px;
                background:#fff;
                padding:10px;
             ">

        <p style="
            margin-top:15px;
            color:#6B7280;
            font-size:13px;
            line-height:20px;
        ">
            Silakan scan QRIS menggunakan aplikasi
            <b>Mobile Banking</b>,
            <b>DANA</b>,
            <b>OVO</b>,
            <b>GoPay</b>,
            <b>ShopeePay</b>,
            atau aplikasi lain yang mendukung QRIS.
        </p>

    </div>

    <label style="
        display:block;
        font-size:14px;
        color:#241412;
        margin-bottom:6px;
        font-weight:bold;
    ">
        Upload Bukti Pembayaran QRIS
    </label>

    <input
        type="file"
        name="bukti_qris"
        accept="image/*"
        style="
            width:100%;
            padding:12px;
            border:2px dashed #B91C1C;
            border-radius:8px;
            background:#FFF8F8;
            box-sizing:border-box;
        ">

    <p style="
        margin-top:8px;
        color:#6B7280;
        font-size:12px;
    ">
        Format JPG, JPEG atau PNG. Maksimal 2 MB.
    </p>

</div>

        <div style="display:flex; gap:8px;">
            <button type="submit" style="padding:12px 28px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Kirim</button>
            <a href="{{ route('simpanan.setor') }}" style="padding:12px 28px; background:#ffffff; color:#241412; border:1px solid #F3B4B4; border-radius:4px; font-weight:bold; text-decoration:none;">Batal</a>
        </div>
    </form>

    <script>
        const metodeSelect = document.getElementById('metode_setoran');
        const uploadArea = document.getElementById('uploadArea');
        function toggleUpload() {
        const jumlahDisplay = document.getElementById('jumlah_display');
        const jumlahHidden = document.getElementById('jumlah');

        function formatRibuan(v) {
            return v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        jumlahDisplay.addEventListener('input', function () {
            const angka = this.value.replace(/\D/g, '');
            this.value = angka ? 'Rp ' + formatRibuan(angka) : '';
            jumlahHidden.value = angka;
        });

        document.getElementById('formSetor').addEventListener('submit', function () {
            jumlahHidden.value = jumlahDisplay.value.replace(/\D/g, '');
        });
            uploadArea.style.display = metodeSelect.value === 'QRIS' ? 'block' : 'none';
        }
        metodeSelect.addEventListener('change', toggleUpload);
        toggleUpload();
    </script>

    {{-- MODAL M-04: BUKTI QRIS WAJIB DIUPLOAD --}}
    @if ($errors->has('bukti_qris'))
        <div id="modal-bukti-wajib" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#C0392B; font-size:28px; font-weight:bold; line-height:1;">&times;</span>
                </div>
                <h3 style="color:#C0392B; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Bukti Pembayaran Wajib</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Bukti pembayaran QRIS wajib diupload.</p>
                <button type="button" onclick="document.getElementById('modal-bukti-wajib').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @elseif ($errors->any())
        {{-- MODAL M-03: DATA TIDAK VALID --}}
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <div style="text-align:left; color:#4B5563; font-size:14px; margin:0 0 24px 0;">
                    <ul style="margin:0; padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-01: SUCCESS (setoran berhasil dikirim) --}}
    @if (session('success'))
        <div id="modal-setoran-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-setoran-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-anggota-layout>