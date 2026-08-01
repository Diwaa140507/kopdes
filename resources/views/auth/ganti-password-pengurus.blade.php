<x-full-width-layout>

    <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:#F3F4F6; font-family:Arial, sans-serif;">
        <div style="width:100%; max-width:420px; background:#ffffff; border:1px solid #F3B4B4; border-radius:8px; padding:32px;">

            <h2 style="font-size:20px; color:#241412; margin:0 0 4px 0;">Ganti Password</h2>
            <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">
                Password sementara harus diganti sebelum melanjutkan ke dashboard.
            </p>

            <form method="POST" action="{{ route('pengurus.ganti-password.store') }}">
                @csrf

                <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Password Baru</label>
                <input type="password" name="password"
                       style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-size:14px; margin-bottom:16px; box-sizing:border-box;">

                <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                       style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-size:14px; margin-bottom:24px; box-sizing:border-box;">

                <button type="submit"
                        style="width:100%; padding:12px 24px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                    Simpan Password Baru
                </button>
            </form>

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

    {{-- MODAL M-01: SUCCESS (setelah password berhasil diubah) --}}
    @if (session('password_updated'))
        <div id="modal-sukses" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Password berhasil diubah.</p>
                <a href="{{ route('pengurus.dashboard') }}"
                   style="display:block; width:100%; box-sizing:border-box; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer; text-decoration:none;">
                    OKE
                </a>
            </div>
        </div>
    @endif

</x-full-width-layout>