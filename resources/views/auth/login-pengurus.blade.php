<x-full-width-layout>
    <div style="min-height:100vh; background:#ffffff; display:flex; flex-direction:column; font-family: Arial, sans-serif;">

        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; padding:20px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
            <div style="display:flex; align-items:center; gap:16px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi"
                     style="height:40px; width:auto; background:#ffffff; padding:6px 10px; border-radius:4px;">
                <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">Login Pengurus</h1>
            </div>
            <a href="{{ route('login') }}" style="color:#ffffff; text-decoration:underline; font-size:14px;">
                &larr; Kembali ke Login Anggota
            </a>
        </div>

        <div style="flex:1; padding:60px 32px;">
            <div style="width:100%; max-width:632px; margin:0 auto;">

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <h2 style="text-align:center; color:#B91C1C; font-weight:bold; font-size:22px; margin:0 0 8px 0;">
                    Masuk Sebagai Pengurus
                </h2>
                <p style="text-align:center; color:#6B7280; margin:0 0 32px 0;">
                    Khusus untuk Ketua Koperasi, Sekretaris, dan Bendahara
                </p>

                <form method="POST" action="{{ route('pengurus.login.store') }}">
                    @csrf

                    <div style="display:flex; align-items:center; margin-bottom:24px;">
                        <label for="email" style="width:100px; color:#1F2937; flex-shrink:0;">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               style="flex:1; border:1px solid #B91C1C; border-radius:4px; padding:12px 14px; outline:none;">
                    </div>

                    <div style="display:flex; align-items:center; margin-bottom:24px;">
                        <label for="password" style="width:100px; color:#1F2937; flex-shrink:0;">Kata Sandi</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               style="flex:1; border:1px solid #B91C1C; border-radius:4px; padding:12px 14px; outline:none;">
                    </div>

                    <button type="submit"
                            style="width:100%; font-weight:bold; color:#ffffff; padding:14px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer; font-size:15px; margin-bottom:16px;">
                        MASUK
                    </button>

                    <div style="text-align:center; font-size:13px; color:#7F1D1D; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:4px; padding:12px 16px;">
                        Email login pengurus menggunakan format: [jabatan].[id]@koperasimerahputih.id
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
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ $errors->first() }}</p>
                <button type="button" onclick="document.getElementById('modal-login-gagal').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    COBA LAGI
                </button>
            </div>
        </div>
    @endif
</x-full-width-layout>