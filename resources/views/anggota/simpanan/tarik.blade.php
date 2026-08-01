<x-anggota-layout activeMenu="simpanan" headerTitle="Simpanan — Dashboard Anggota">

    <div style="display:flex; gap:20px; margin-bottom:24px;">
        <div style="flex:1; background:#FDEEEE; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#7F1D1D; font-size:13px;">Saldo Simpanan Wajib</p>
            <p style="margin:0; color:#241412; font-size:22px; font-weight:bold;">Rp {{ number_format($saldoWajib, 0, ',', '.') }}</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#7F1D1D; font-size:13px;">Saldo Simpanan Sukarela </p>
            <p style="margin:0; color:#241412; font-size:22px; font-weight:bold;">Rp {{ number_format($saldoSukarela, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tab --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('simpanan.setor') }}" style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">Setoran</a>
        <a href="{{ route('simpanan.tarik') }}" style="padding:10px 20px; border:1px solid #B91C1C; border-radius:4px; text-decoration:none; color:#ffffff; font-weight:bold; background:#B91C1C;">Penarikan</a>
        <a href="{{ route('simpanan.riwayat') }}" style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">Riwayat</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Form Penarikan Simpanan Sukarela</h3>

    <form method="POST" action="{{ route('simpanan.tarik.store') }}" style="max-width:500px;" id="formTarik">
        @csrf

        <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Jumlah Penarikan (Rp)</label>
<input type="text" inputmode="numeric" id="jumlah_display" placeholder="Rp 0" required
       value="{{ old('jumlah') ? 'Rp ' . number_format(old('jumlah'), 0, ',', '.') : '' }}"
       style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; box-sizing:border-box; font-family:Arial, sans-serif;">
<input type="hidden" name="jumlah" id="jumlah">
<p id="jumlah_error" style="display:none; color:#B91C1C; font-size:12px; margin:4px 0 0;"></p>
        <p style="font-size:12px; color:#9CA3AF; margin:4px 0 16px;">Minimal: Rp 50.000 sesuai ketentuan koperasi. Maks: Rp {{ number_format($saldoSukarela, 0, ',', '.') }}</p>

        <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Metode Penarikan</label>
        <select name="metode_penarikan" id="metode_penarikan" required
                style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; margin-bottom:16px; font-family:Arial, sans-serif;">
            <option value="">-- Pilih --</option>
            <option value="Transfer Bank" {{ old('metode_penarikan') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
            <option value="Tunai" {{ old('metode_penarikan') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
        </select>

        <div id="rekeningArea">
            <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Nama Bank / E-Wallet</label>
            <input type="text" id="nama_bank" name="nama_bank_ewallet" value="{{ old('nama_bank_ewallet') }}" placeholder="cth: BCA, Dana, dsb (kosongkan jika Tunai)"
                   style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; margin-bottom:16px; box-sizing:border-box; font-family:Arial, sans-serif;">

            <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">No. Rekening / No. HP Tujuan</label>
            <input type="text" id="no_rekening" name="no_rekening_tujuan" value="{{ old('no_rekening_tujuan') }}" placeholder="kosongkan jika pilih Tunai"
                   style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; margin-bottom:16px; box-sizing:border-box; font-family:Arial, sans-serif;">
        </div>

 <div style="background:#FCE9C7; color:#8A5A00; border-radius:4px; padding:12px 16px; margin-bottom:20px; font-size:13px;">
            Pengajuan akan diteruskan ke Bendahara untuk diproses dan dicairkan sesuai metode di atas.
        </div>

        <div style="display:flex; gap:8px;">
            <button type="submit" style="padding:12px 28px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Ajukan</button>
            <a href="{{ route('simpanan.setor') }}" style="padding:12px 28px; background:#ffffff; color:#241412; border:1px solid #F3B4B4; border-radius:4px; font-weight:bold; text-decoration:none;">Batal</a>
        </div>
    </form>

    <script>
        const metodeSelect = document.getElementById('metode_penarikan');
        const rekeningArea = document.getElementById('rekeningArea');
        const jumlahDisplayTarik = document.getElementById('jumlah_display');
const jumlahHiddenTarik = document.getElementById('jumlah');
const jumlahErrorTarik = document.getElementById('jumlah_error');
const saldoSukarelaTersedia = {{ $saldoSukarela }};

const MIN_TARIK = 50000;

function formatRibuanTarik(v) {
    return v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

jumlahDisplayTarik.addEventListener('input', function () {
    const angka = this.value.replace(/\D/g, '');
    this.value = angka ? 'Rp ' + formatRibuanTarik(angka) : '';
    jumlahHiddenTarik.value = angka;
});

document.getElementById('formTarik').addEventListener('submit', function (e) {
    const angka = parseInt(jumlahHiddenTarik.value || '0', 10);

    if (!angka || angka < MIN_TARIK) {
        e.preventDefault();
        document.getElementById('modal-min-tarik').style.display = 'flex';
    } else if (angka > saldoSukarelaTersedia) {
        e.preventDefault();
        document.getElementById('modal-saldo-kurang').style.display = 'flex';
    } else {
        jumlahErrorTarik.style.display = 'none';
    }
});
        function toggleRekening() {
            const bank=document.getElementById('nama_bank');
            const norek=document.getElementById('no_rekening');
            const pemilik=document.getElementById('nama_pemilik');
            if(metodeSelect.value==='Tunai'){
                rekeningArea.style.display='none';
                [bank,norek,pemilik].forEach(e=>{if(e){e.required=false;e.value='';}});
            }else{
                rekeningArea.style.display='block';
                [bank,norek,pemilik].forEach(e=>{if(e)e.required=true;});
            }
        }
        metodeSelect.addEventListener('change', toggleRekening);
        toggleRekening();
    </script>

    {{-- MODAL M-03: JUMLAH DI BAWAH MINIMUM (client-side) --}}
    <div id="modal-min-tarik" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:1000; padding:16px;">
        <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
            <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
            </div>
            <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
            <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Jumlah penarikan minimal Rp 50.000.</p>
            <button type="button" onclick="document.getElementById('modal-min-tarik').style.display='none'"
                    style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                MENGERTI
            </button>
        </div>
    </div>

    {{-- MODAL M-05: SALDO TIDAK MENCUKUPI (client-side) --}}
    <div id="modal-saldo-kurang" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:1000; padding:16px;">
        <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
            <div style="width:64px; height:64px; border-radius:50%; background:#FCE9C7; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                <span style="color:#8A5A00; font-size:26px; font-weight:bold; line-height:1;">&#9888;</span>
            </div>
            <h3 style="color:#8A5A00; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Saldo Tidak Mencukupi</h3>
            <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Jumlah penarikan melebihi Saldo Sukarela yang tersedia.</p>
            <button type="button" onclick="document.getElementById('modal-saldo-kurang').style.display='none'"
                    style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                MENGERTI
            </button>
        </div>
    </div>

    {{-- MODAL M-03: DATA TIDAK VALID (backend) --}}
    @if ($errors->any())
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

    {{-- MODAL M-06: INFO (pengajuan terkirim, menunggu diproses Bendahara) --}}
    @if (session('info'))
        <div id="modal-info-tarik" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:26px; font-weight:bold; font-style:italic; line-height:1;">i</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Menunggu Diproses</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('info') }}</p>
                <button type="button" onclick="document.getElementById('modal-info-tarik').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-anggota-layout>