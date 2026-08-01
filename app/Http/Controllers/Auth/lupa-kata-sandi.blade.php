<x-full-width-layout>
    <style>
        @media (max-width: 640px) {
            .btn-row { flex-direction: column !important; }
            .btn-row a, .btn-row button { width: 100% !important; }
        }
    </style>

    <div style="min-height:100vh; background:#ffffff; display:flex; flex-direction:column; font-family: Arial, sans-serif;">

        {{-- HEADER --}}
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:20px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="background:#ffffff; color:#B91C1C; font-weight:bold; font-size:12px; padding:10px 14px; border-radius:4px;">
                    LOGO
                </div>
                <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">Lupa Kata Sandi</h1>
            </div>
            <a href="{{ route('login') }}" style="color:#ffffff; text-decoration:underline; font-size:14px;">
                &larr; Kembali ke Login
            </a>
        </div>

        {{-- BODY --}}
        <div style="flex:1; padding:40px 24px;">
            <div style="width:100%; max-width:632px; margin:0 auto;">

                @if (session('reset_terkirim'))
                    {{-- STATE SUKSES --}}
                    <div style="text-align:center; background:#EAF7EC; border:1px solid #1E7A34; border-radius:6px; padding:32px 24px;">
                        <p style="color:#1E7A34; font-weight:bold; font-size:18px; margin:0 0 8px 0;">
                            Permintaan Reset Kata Sandi Terkirim
                        </p>
                        <p style="color:#1E7A34; margin:0;">
                            Sekretaris akan memproses dan menghubungi Anda melalui kontak yang terdaftar.
                        </p>
                    </div>
                @else
                    {{-- FORM --}}
                    <h2 style="color:#1F2937; font-weight:bold; font-size:20px; margin:0 0 16px 0;">
                        Ajukan Reset Kata Sandi
                    </h2>

                    <div style="font-size:14px; color:#7F1D1D; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:4px; padding:12px 16px; margin-bottom:24px;">
                        Permintaan ini akan diverifikasi dan diproses langsung oleh Sekretaris koperasi.
                    </div>

                    @if ($errors->any())
                        <div style="margin-bottom:20px; padding:12px 16px; border-radius:4px; background:#FDEEEE; border:1px solid #F3B4B4; color:#7F1D1D; font-size:14px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

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
                @endif

            </div>
        </div>
    </div>
</x-full-width-layout>
