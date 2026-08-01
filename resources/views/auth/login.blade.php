<x-full-width-layout>
    <div style="min-height:100vh; background:#ffffff; display:flex; flex-direction:column; font-family: Arial, sans-serif;">

        {{-- HEADER --}}
        <div style="display:flex; align-items:center; gap:16px; padding:20px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi"
                 style="height:40px; width:auto; background:#ffffff; padding:6px 10px; border-radius:4px;">
            <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">Login Pengguna</h1>
        </div>

        {{-- BODY --}}
        <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:40px 16px;">
            <div style="width:100%; max-width:480px;">

                {{-- Status message rendered as modal below --}}

                {{-- Kotak Login Pengurus --}}
                <div style="border:2px dashed #F3B4B4; border-radius:6px; background:#FDEEEE; text-align:center; padding:24px; margin-bottom:24px;">
                    <p style="font-style:italic; color:#7F1D1D; margin:0 0 16px 0;">Login Khusus Pengurus</p>
                    <a href="{{ route('pengurus.login') }}"
                       style="display:inline-block; border:2px solid #B91C1C; color:#B91C1C; font-weight:bold; padding:10px 24px; border-radius:4px; text-decoration:none;">
                        MASUK SEBAGAI PENGURUS
                    </a>
                </div>

                <div style="text-align:center; color:#9CA3AF; margin-bottom:24px;">— atau —</div>

                <h2 style="text-align:center; color:#B91C1C; font-weight:bold; font-size:18px; margin-bottom:24px;">
                    Masuk Ke Akun Anggota Koperasi
                </h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div style="display:flex; align-items:center; margin-bottom:20px;">
                        <label for="email" style="width:110px; color:#1F2937; flex-shrink:0;">Email</label>
                        <span style="margin-right:8px; color:#1F2937;">:</span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               style="flex:1; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mb-2" />

                    {{-- Password --}}
                    <div style="display:flex; align-items:center; margin-bottom:24px;">
                        <label for="password" style="width:110px; color:#1F2937; flex-shrink:0;">Kata Sandi</label>
                        <span style="margin-right:8px; color:#1F2937;">:</span>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               style="flex:1; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mb-4" />

                    {{-- Tombol Masuk --}}
                    <div style="text-align:center; margin-bottom:20px;">
                        <button type="submit"
                                style="width:220px; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                            MASUK
                        </button>
                    </div>

                    {{-- Link Daftar / Lupa Kata Sandi --}}
                    <div style="text-align:center; font-size:14px;">
                        <p style="margin:0 0 4px 0; color:#1F2937;">
                            Belum punya akun?
                            <a href="{{ route('register') }}" style="color:#B91C1C; font-weight:600; text-decoration:underline;">Daftar sebagai anggota baru</a>
                        </p>
                        <a href="{{ route('lupa-kata-sandi') }}" style="color:#B91C1C; font-weight:600; text-decoration:underline;">
                            LUPA KATA SANDI
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- MODAL M-04: LOGIN GAGAL --}}
    @if ($errors->any())
        <div id="modal-login-gagal" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#C0392B; font-size:28px; font-weight:bold; line-height:1;">&times;</span>
                </div>
                <h3 style="color:#C0392B; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Login Gagal</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Email atau kata sandi salah.</p>
                <button type="button" onclick="document.getElementById('modal-login-gagal').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    COBA LAGI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-06: INFO (session status, misal setelah daftar) --}}
    @if (session('status'))
        <div id="modal-info-status" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:26px; font-weight:bold; font-style:italic; line-height:1;">i</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Menunggu Diproses</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('status') }}</p>
                <button type="button" onclick="document.getElementById('modal-info-status').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif
</x-full-width-layout>