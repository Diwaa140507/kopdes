<x-anggota-layout activeMenu="pinjaman" headerTitle="Pinjaman — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Pinjaman</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('pinjaman.cek-kelayakan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Cek Kelayakan</a>
        <a href="{{ route('pinjaman.ajukan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Ajukan Pinjaman</a>
        <a href="{{ route('pinjaman.detail') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Detail Pinjaman Aktif</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Form Pengajuan Pinjaman</h3>

    <div style="background:#EAF7EC; border-radius:6px; padding:12px 16px; margin-bottom:20px; text-align:center;">
        <p style="margin:0; color:#1E7A34; font-size:14px; font-weight:bold;">
            Hasil kelayakan terakhir: LAYAK — Batas maksimal pinjaman: Rp {{ number_format($hasil['batas_maksimal'], 0, ',', '.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('pinjaman.ajukan.store') }}" id="form-ajukan">
        @csrf

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Tujuan Pinjaman</label>
            <input type="text" name="tujuan_pinjaman" value="{{ old('tujuan_pinjaman', $hasil['tujuan_pinjaman']) }}"
                   style="width:100%; max-width:500px; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
            @error('tujuan_pinjaman')
                <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display:flex; gap:20px; margin-bottom:16px; max-width:500px;">
            <div style="flex:1;">
                <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Nominal Pinjaman</label>
                <input type="text" inputmode="numeric" id="nominal_display" placeholder="Rp 0" autocomplete="off"
                       value="Rp {{ number_format(old('nominal', $hasil['nominal']), 0, ',', '.') }}"
                       style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                <input type="hidden" name="nominal" id="nominal_hidden" value="{{ old('nominal', $hasil['nominal']) }}">
                <p id="nominal_error" style="display:none; color:#B91C1C; font-size:12px; margin:4px 0 0;"></p>
                @error('nominal')
                    <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="flex:1;">
                <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Tenor (bulan)</label>
                <input type="number" id="tenor_bulan" name="tenor_bulan" min="1" max="12" step="1"
                       value="{{ old('tenor_bulan', $hasil['tenor_bulan']) }}"
                       style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                <p style="font-size:12px; color:#9CA3AF; margin:4px 0 0;">Maksimal 12 bulan</p>
                <p id="tenor_error" style="display:none; color:#B91C1C; font-size:12px; margin:4px 0 0;"></p>
                @error('tenor_bulan')
                    <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom:20px; max-width:500px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Rekening Tujuan</label>
            <input type="text" name="rekening_tujuan" value="{{ old('rekening_tujuan') }}"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
            @error('rekening_tujuan')
                <p style="margin:4px 0 0; color:#B91C1C; font-size:12px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Estimasi otomatis sistem --}}
        <div style="background:#F4F4F4; border-radius:6px; padding:16px 20px; margin-bottom:24px; max-width:700px;">
            <p style="margin:0 0 10px; font-size:13px; font-weight:bold; color:#3A3A3A;">Estimasi otomatis sistem:</p>
            <p style="margin:0; font-size:13px; color:#3A3A3A;">
                Persentase Jasa: {{ $persentaseJasa }}% &nbsp;|&nbsp;
                Jumlah Jasa: Rp <span id="est-jasa">0</span> &nbsp;|&nbsp;
                Cicilan / bulan: Rp <span id="est-cicilan">0</span> &nbsp;|&nbsp;
                Total Pengembalian: Rp <span id="est-total">0</span>
            </p>
        </div>

        <button type="submit" style="padding:12px 24px; border:none; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px; cursor:pointer; margin-right:12px;">
            Ajukan
        </button>
        <a href="{{ route('pinjaman.ajukan') }}" style="display:inline-block; padding:12px 24px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px; text-decoration:none;">
            Batal
        </a>
    </form>

    <script>
        (function () {
            var persentaseJasa = {{ $persentaseJasa }};
            var nominalDisplay = document.getElementById('nominal_display');
            var nominalHidden = document.getElementById('nominal_hidden');
            var nominalError = document.getElementById('nominal_error');
            var tenorInput = document.getElementById('tenor_bulan');
            var tenorError = document.getElementById('tenor_error');
            var form = document.getElementById('form-ajukan');

            function formatRibuan(v) {
                return v.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function hitungEstimasi() {
                var nominal = parseFloat(nominalHidden.value) || 0;
                var tenor = parseInt(tenorInput.value) || 0;

                var jasa = nominal * persentaseJasa / 100;
                var total = nominal + jasa;
                var cicilan = tenor > 0 ? total / tenor : 0;

                document.getElementById('est-jasa').textContent = formatRibuan(String(Math.round(jasa)));
                document.getElementById('est-total').textContent = formatRibuan(String(Math.round(total)));
                document.getElementById('est-cicilan').textContent = formatRibuan(String(Math.round(cicilan)));
            }

            nominalDisplay.addEventListener('input', function () {
                var angka = this.value.replace(/\D/g, '');
                this.value = angka ? 'Rp ' + formatRibuan(angka) : '';
                nominalHidden.value = angka;
                hitungEstimasi();
            });

            tenorInput.addEventListener('input', function () {
                var nilai = parseInt(this.value || '0', 10);
                if (nilai > 12) {
                    tenorError.textContent = 'Tenor maksimal 12 bulan.';
                    tenorError.style.display = 'block';
                } else {
                    tenorError.style.display = 'none';
                }
                hitungEstimasi();
            });

            form.addEventListener('submit', function (e) {
                var angka = parseInt(nominalHidden.value || '0', 10);
                var tenor = parseInt(tenorInput.value || '0', 10);
                var valid = true;

                if (!angka || angka < 1) {
                    valid = false;
                }

                if (!tenor || tenor < 1 || tenor > 12) {
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    document.getElementById('modal-field-kosong').style.display = 'flex';
                }
            });

            hitungEstimasi();
        })();
    </script>

    {{-- MODAL M-03: FIELD KOSONG (client-side) --}}
    <div id="modal-field-kosong" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:1000; padding:16px;">
        <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
            <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
            </div>
            <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
            <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Mohon lengkapi semua field pengajuan pinjaman.</p>
            <button type="button" onclick="document.getElementById('modal-field-kosong').style.display='none'"
                    style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                MENGERTI
            </button>
        </div>
    </div>

    {{-- MODAL M-03: DATA TIDAK VALID (backend, kecuali error nominal terkait batas) --}}
    @if ($errors->any() && !$errors->has('nominal'))
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
    @elseif ($errors->has('nominal'))
        {{-- MODAL M-05: NOMINAL MELEBIHI BATAS KELAYAKAN --}}
        <div id="modal-nominal-melebihi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FCE9C7; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#8A5A00; font-size:26px; font-weight:bold; line-height:1;">&#9888;</span>
                </div>
                <h3 style="color:#8A5A00; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Melebihi Batas</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ $errors->first('nominal') }}</p>
                <button type="button" onclick="document.getElementById('modal-nominal-melebihi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-06: INFO (pengajuan terkirim, menunggu persetujuan Bendahara) --}}
    @if (session('info'))
        <div id="modal-info-ajukan" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:26px; font-weight:bold; font-style:italic; line-height:1;">i</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Menunggu Diproses</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('info') }}</p>
                <button type="button" onclick="document.getElementById('modal-info-ajukan').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-anggota-layout>