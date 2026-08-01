<x-full-width-layout>
    <style>
        @media (max-width: 640px) {
            .btn-row { flex-direction: column !important; }
            .btn-row a, .btn-row button { width: 100% !important; }
        }
    </style>

    <div style="min-height:100vh; background:#ffffff; display:flex; flex-direction:column; font-family: Arial, sans-serif;">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:20px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
            <div style="display:flex; align-items:center; gap:16px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi"
                     style="height:40px; width:auto; background:#ffffff; padding:6px 10px; border-radius:4px;">
                <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">Lupa Kata Sandi</h1>
            </div>
            <a href="{{ route('login') }}" style="color:#ffffff; text-decoration:underline; font-size:14px;">
                &larr; Kembali ke Login
            </a>
        </div>

        <div style="flex:1; padding:40px 24px;">
            <div style="width:100%; max-width:632px; margin:0 auto;">

                <h2 style="color:#1F2937; font-weight:bold; font-size:20px; margin:0 0 16px 0;">
                    Ajukan Reset Kata Sandi
                </h2>

                    <div style="font-size:14px; color:#7F1D1D; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:4px; padding:12px 16px; margin-bottom:24px;">
                        Permintaan ini akan diverifikasi dan diproses langsung oleh Sekretaris koperasi.
                    </div>

                    <form method="POST" action="{{ route('lupa-kata-sandi.store') }}">
                        @csrf

                        <div style="margin-bottom:16px;">
                            <label for="email" style="display:block; color:#1F2937; margin-bottom:6px;">Email Terdaftar</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   style="width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                        </div>

                        <div style="margin-bottom:8px;">
                            <label for="nik" style="display:block; color:#1F2937; margin-bottom:6px;">NIK Terdaftar</label>
                            <input id="nik" type="text" name="nik" maxlength="16" value="{{ old('nik') }}" required
                                   style="width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                        </div>

                        <p style="font-size:12px; color:#6B7280; font-style:italic; margin:0 0 24px 0;">
                            * Untuk verifikasi identitas dasar sebelum permintaan diteruskan ke Sekretaris
                        </p>

                        <div class="btn-row" style="display:flex; gap:12px;">
                            <button type="submit"
                                    style="font-weight:bold; color:#ffffff; padding:12px 28px; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                                Kirim Permintaan
                            </button>
                            <a href="{{ route('login') }}"
                               style="display:inline-flex; align-items:center; justify-content:center; font-weight:bold; color:#B91C1C; padding:12px 28px; border-radius:6px; border:2px solid #B91C1C; text-decoration:none;">
                                Batal
                            </a>
                        </div>
                    </form>

            </div>
        </div>
    </div>

    {{-- MODAL M-03: DATA TIDAK VALID --}}
    @if ($errors->any())
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ $errors->first() }}</p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-06: INFO (permintaan reset terkirim) --}}
    @if (session('reset_terkirim'))
        <div id="modal-reset-terkirim" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:26px; font-weight:bold; font-style:italic; line-height:1;">i</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Menunggu Diproses</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Permintaan reset kata sandi terkirim. Sekretaris akan memproses.</p>
                <a href="{{ route('login') }}"
                   style="display:block; width:100%; box-sizing:border-box; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer; text-decoration:none;">
                    OKE
                </a>
            </div>
        </div>
    @endif
</x-full-width-layout>