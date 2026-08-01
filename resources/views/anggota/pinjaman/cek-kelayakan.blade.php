<x-anggota-layout activeMenu="pinjaman" headerTitle="Pinjaman — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Pinjaman</h2>

    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('pinjaman.cek-kelayakan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Cek Kelayakan</a>
        <a href="{{ route('pinjaman.ajukan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Ajukan Pinjaman</a>
        <a href="{{ route('pinjaman.detail') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Detail Pinjaman Aktif</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Cek Kelayakan Pinjaman</h3>

    @if (session('info'))
        <div style="background:#FCE9C7; border-radius:6px; padding:12px 16px; margin-bottom:16px; color:#8A5A00; font-size:14px;">
            {{ session('info') }}
        </div>
    @endif

    @if ($hasil)
        <div style="display:flex; gap:20px; margin-bottom:20px;">
            <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px; text-align:center;">
                <p style="margin:0 0 4px; color:#7F1D1D; font-weight:bold; font-size:14px;">Tunggakan Cicilan</p>
                @if ($hasil['punya_pinjaman_aktif'])
                    <p style="margin:0 0 10px; color:#6B7280; font-size:12px;">Masih ada pinjaman aktif berjalan</p>
                @elseif ($hasil['ada_tunggakan'])
                    <p style="margin:0 0 10px; color:#6B7280; font-size:12px;">Ada tunggakan cicilan</p>
                @else
                    <p style="margin:0 0 10px; color:#6B7280; font-size:12px;">Tidak ada tunggakan</p>
                @endif
                @php $amanTunggakan = !$hasil['punya_pinjaman_aktif'] && !$hasil['ada_tunggakan']; @endphp
                <span style="display:inline-block; padding:4px 14px; border-radius:4px; font-size:12px; font-weight:bold; background:{{ $amanTunggakan ? '#EAF7EC' : '#FADBD8' }}; color:{{ $amanTunggakan ? '#1E7A34' : '#A5301F' }};">
                    {{ $amanTunggakan ? 'Aman' : 'Tidak Aman' }}
                </span>
            </div>
            <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px; text-align:center;">
                <p style="margin:0 0 4px; color:#7F1D1D; font-weight:bold; font-size:14px;">Rasio Simpanan</p>
                <p style="margin:0 0 10px; color:#6B7280; font-size:12px;">{{ $hasil['rasio_persen'] }}% dari batas maksimal</p>
                <span style="display:inline-block; padding:4px 14px; border-radius:4px; font-size:12px; font-weight:bold; background:{{ $hasil['rasio_aman'] ? '#EAF7EC' : '#FADBD8' }}; color:{{ $hasil['rasio_aman'] ? '#1E7A34' : '#A5301F' }};">
                    {{ $hasil['rasio_aman'] ? 'Aman' : 'Tidak Aman' }}
                </span>
            </div>
            <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px; text-align:center;">
                <p style="margin:0 0 4px; color:#7F1D1D; font-weight:bold; font-size:14px;">Riwayat Pembayaran</p>
                <p style="margin:0 0 10px; color:#6B7280; font-size:12px;">Lancar, {{ $hasil['jumlah_terlambat'] }}x terlambat</p>
                <span style="display:inline-block; padding:4px 14px; border-radius:4px; font-size:12px; font-weight:bold; background:{{ $hasil['riwayat_aman'] ? '#EAF7EC' : '#FADBD8' }}; color:{{ $hasil['riwayat_aman'] ? '#1E7A34' : '#A5301F' }};">
                    {{ $hasil['riwayat_aman'] ? 'Aman' : 'Tidak Aman' }}
                </span>
            </div>
        </div>

        <div style="background:{{ $hasil['layak'] ? '#EAF7EC' : '#FADBD8' }}; border-radius:6px; padding:16px 20px; margin-bottom:24px; text-align:center;">
            <p style="margin:0; color:{{ $hasil['layak'] ? '#1E7A34' : '#A5301F' }}; font-size:16px; font-weight:bold;">
                Hasil: {{ $hasil['layak'] ? 'LAYAK' : 'TIDAK LAYAK' }}
            </p>
            <p style="margin:4px 0 0; color:{{ $hasil['layak'] ? '#1E7A34' : '#A5301F' }}; font-size:13px;">
                Batas maksimal pinjaman: Rp {{ number_format($hasil['batas_maksimal'], 0, ',', '.') }}
            </p>
        </div>

        @if ($hasil['layak'])
            <div style="margin-bottom:24px;">
                <a href="{{ route('pinjaman.ajukan') }}" style="text-decoration:none; padding:12px 24px; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Lanjut Ajukan Pinjaman</a>
            </div>
        @endif
    @endif

    <form method="POST" action="{{ route('pinjaman.cek-kelayakan.store') }}" id="form-cek-kelayakan">
        @csrf

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Tujuan Pinjaman</label>
            <input type="text" name="tujuan_pinjaman" value="{{ old('tujuan_pinjaman', $hasil['tujuan_pinjaman'] ?? '') }}"
                   style="width:100%; max-width:500px; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
            @error('tujuan_pinjaman')
                <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display:flex; gap:20px; margin-bottom:20px; max-width:500px;">
            <div style="flex:1;">
                <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Nominal (Rp)</label>
                <input type="text" inputmode="numeric" id="nominal_display" placeholder="Rp 0" autocomplete="off"
                       value="{{ old('nominal', $hasil['nominal'] ?? '') != '' ? 'Rp ' . number_format(old('nominal', $hasil['nominal'] ?? 0), 0, ',', '.') : '' }}"
                       style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                <input type="hidden" name="nominal" id="nominal_hidden" value="{{ old('nominal', $hasil['nominal'] ?? '') }}">
                <p id="nominal_error" style="display:none; color:#B91C1C; font-size:12px; margin:4px 0 0;"></p>
                @error('nominal')
                    <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="flex:1;">
                <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Tenor (bulan)</label>
                <input type="number" id="tenor_bulan" name="tenor_bulan" min="1" max="12" step="1" value="{{ old('tenor_bulan', $hasil['tenor_bulan'] ?? '') }}"
                       style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                <p style="font-size:12px; color:#9CA3AF; margin:4px 0 0;">Maksimal 12 bulan</p>
                <p id="tenor_error" style="display:none; color:#B91C1C; font-size:12px; margin:4px 0 0;"></p>
                @error('tenor_bulan')
                    <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" style="padding:12px 24px; border:none; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px; cursor:pointer;">
            Cek Kelayakan
        </button>
    </form>

    <script>
        (function () {
            var nominalDisplay = document.getElementById('nominal_display');
            var nominalHidden = document.getElementById('nominal_hidden');
            var nominalError = document.getElementById('nominal_error');
            var tenorInput = document.getElementById('tenor_bulan');
            var tenorError = document.getElementById('tenor_error');
            var form = document.getElementById('form-cek-kelayakan');

            function formatRibuan(v) {
                return v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            nominalDisplay.addEventListener('input', function () {
                var angka = this.value.replace(/\D/g, '');
                this.value = angka ? 'Rp ' + formatRibuan(angka) : '';
                nominalHidden.value = angka;
            });

            tenorInput.addEventListener('input', function () {
                var nilai = parseInt(this.value || '0', 10);
                if (nilai > 12) {
                    tenorError.textContent = 'Tenor maksimal 12 bulan.';
                    tenorError.style.display = 'block';
                } else {
                    tenorError.style.display = 'none';
                }
            });

            form.addEventListener('submit', function (e) {
                var angka = parseInt(nominalHidden.value || '0', 10);
                var tenor = parseInt(tenorInput.value || '0', 10);
                var valid = true;

                if (!angka || angka < 1) {
                    nominalError.textContent = 'Nominal wajib diisi.';
                    nominalError.style.display = 'block';
                    valid = false;
                } else {
                    nominalError.style.display = 'none';
                }

                if (!tenor || tenor < 1 || tenor > 12) {
                    tenorError.textContent = 'Tenor wajib diisi, maksimal 12 bulan.';
                    tenorError.style.display = 'block';
                    valid = false;
                } else {
                    tenorError.style.display = 'none';
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        })();
    </script>

    {{-- MODAL M-03: DATA TIDAK VALID --}}
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

</x-anggota-layout>