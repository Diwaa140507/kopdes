<x-full-width-layout>
    <style>
        @media (max-width: 820px) {
            .field-row { flex-direction: column !important; align-items: flex-stretch !important; }
            .field-label { width: 100% !important; margin-bottom: 6px; padding-top: 0 !important; }
        }
    </style>

    <div style="min-height:100vh; background:#ffffff; display:flex; flex-direction:column; font-family: Arial, sans-serif;">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:20px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
            <div style="display:flex; align-items:center; gap:16px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi"
                     style="height:40px; width:auto; background:#ffffff; padding:6px 10px; border-radius:4px;">
                <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">Pendaftaran Anggota Baru</h1>
            </div>
            <a href="{{ route('login') }}" style="color:#ffffff; text-decoration:underline; font-size:14px;">
                Keluar
            </a>
        </div>

        <div style="flex:1; padding:40px 24px;">
            <div style="width:100%; max-width:632px; margin:0 auto;">

                <h2 style="text-align:center; color:#B91C1C; font-weight:bold; font-size:22px; margin:0 0 8px 0;">
                    Formulir Pendaftaran Anggota
                </h2>
                <p style="text-align:center; color:#6B7280; margin:0 0 28px 0;">
                    Lengkapi data diri berikut untuk mendaftar sebagai anggota koperasi
                </p>

                <form id="form-register" method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="field-row" style="display:flex; align-items:center; margin-bottom:20px;">
                        <label for="nik" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0;">NIK</label>
                        <input id="nik" type="text" name="nik" maxlength="16" value="{{ old('nik') }}" required
                               style="flex:1; min-width:0; width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                    </div>

                    <div class="field-row" style="display:flex; align-items:flex-start; margin-bottom:20px;">
                        <label for="dokumen_pendukung" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0; padding-top:10px;">Dokumen Pendukung</label>
                        <div style="flex:1; min-width:0; width:100%;">
                            <label for="dokumen_pendukung" style="display:block; border:2px dashed #F3B4B4; background:#FDEEEE; border-radius:6px; text-align:center; padding:24px; cursor:pointer; color:#7F1D1D;">
                                Klik untuk Upload pas foto
                            </label>
                            <input id="dokumen_pendukung" type="file" name="dokumen_pendukung" accept="image/*" style="display:none;"
                                   onchange="document.getElementById('nama-file').innerText = this.files[0] ? this.files[0].name : ''">
                            <div id="nama-file" style="font-size:12px; color:#6B7280; margin-top:6px;"></div>
                        </div>
                    </div>

                    <div class="field-row" style="display:flex; align-items:center; margin-bottom:20px;">
                        <label for="name" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0;">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required
                               style="flex:1; min-width:0; width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                    </div>

                    <div class="field-row" style="display:flex; align-items:center; margin-bottom:20px;">
                        <label for="jenis_kelamin" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0;">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" required
                                style="flex:1; min-width:0; width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none; background:#fff;">
                            <option value="" disabled selected>Laki-laki / Perempuan</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="field-row" style="display:flex; align-items:center; margin-bottom:20px;">
                        <label for="tanggal_lahir" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0;">Tanggal Lahir</label>
                        <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                               style="flex:1; min-width:0; width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                    </div>

                    <div class="field-row" style="display:flex; align-items:flex-start; margin-bottom:20px;">
                        <label for="alamat" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0; padding-top:10px;">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" required
                                  style="flex:1; min-width:0; width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none; resize:vertical;">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="field-row" style="display:flex; align-items:center; margin-bottom:24px;">
                        <label for="email" class="field-label" style="width:130px; color:#1F2937; flex-shrink:0;">Email Aktif</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               style="flex:1; min-width:0; width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                    </div>

                    <div style="text-align:center; font-size:14px; color:#8A5A00; background:#FCE9C7; border:1px solid #F0D8A8; border-radius:4px; padding:12px 16px; margin-bottom:24px;">
                        Pendaftaran akan diverifikasi dahulu oleh Sekretaris.
                    </div>

                    <div style="text-align:center;">
                        <button type="button" onclick="document.getElementById('modal-konfirmasi-daftar').style.display='flex'"
                                style="width:100%; max-width:280px; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer; font-size:15px;">
                            DAFTAR
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

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

    {{-- MODAL M-02: KONFIRMASI SEBELUM KIRIM --}}
    <div id="modal-konfirmasi-daftar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:1000; padding:16px;">
        <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
            <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                <span style="color:#B91C1C; font-size:26px; font-weight:bold; line-height:1;">?</span>
            </div>
            <h3 style="color:#1F2937; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Yakin ingin melanjutkan?</h3>
            <p style="color:#6B7280; font-size:14px; margin:0 0 24px 0;">Tindakan ini akan diproses oleh sistem.</p>
            <div style="display:flex; gap:12px;">
                <button type="button" onclick="document.getElementById('modal-konfirmasi-daftar').style.display='none'"
                        style="flex:1; font-weight:bold; color:#B91C1C; padding:12px 0; border-radius:6px; background:#ffffff; border:2px solid #B91C1C; cursor:pointer;">
                    TIDAK
                </button>
                <button type="button" onclick="document.getElementById('form-register').submit()"
                        style="flex:1; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    YA
                </button>
            </div>
        </div>
    </div>
</x-full-width-layout>