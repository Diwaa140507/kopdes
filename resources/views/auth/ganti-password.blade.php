<x-full-width-layout>
    <div style="min-height:100vh; background:#F3F4F6; display:flex; flex-direction:column; font-family: Arial, sans-serif;">

        <div style="display:flex; align-items:center; gap:16px; padding:20px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi"
                 style="height:40px; width:auto; background:#ffffff; padding:6px 10px; border-radius:4px;">
            <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">Buat Password Baru — Anggota</h1>
        </div>

        <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:40px 16px;">
            <div style="width:100%; max-width:632px;">

                <div style="border:1px solid #B91C1C; border-radius:8px; overflow:hidden; background:#ffffff;">

                    <div style="background:#7F1D1D; text-align:center; padding:16px;">
                        <p style="color:#ffffff; font-weight:bold; margin:0;">Anda wajib membuat password baru</p>
                    </div>

                    <div style="padding:24px;">

                        <div style="text-align:center; font-size:14px; color:#8A5A00; background:#FCE9C7; border:1px solid #F0D8A8; border-radius:4px; padding:12px 16px; margin-bottom:20px;">
                            Password sementara dari Sekretaris hanya berlaku 1x. Buat password baru untuk melanjutkan.
                        </div>

                        <div style="margin-bottom:20px;">
                            <label style="display:block; color:#1F2937; margin-bottom:6px;">Password Sementara (dari Sekretaris)</label>
                            <input type="password" value="disabled" disabled
                                   style="width:100%; box-sizing:border-box; border:1px solid #D1D5DB; border-radius:4px; padding:10px 12px; background:#EEEEEE; color:#9CA3AF;">
                        </div>

                        <form method="POST" action="{{ route('ganti-password.store') }}">
                            @csrf

                            <div style="margin-bottom:20px;">
                                <label for="password" style="display:block; color:#1F2937; margin-bottom:6px;">Password Baru</label>
                                <input id="password" type="password" name="password" required
                                       placeholder="Minimal 8 karakter"
                                       style="width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                            </div>

                            <div style="margin-bottom:24px;">
                                <label for="password_confirmation" style="display:block; color:#1F2937; margin-bottom:6px;">Konfirmasi Password Baru</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                       placeholder="Ulangi password baru"
                                       style="width:100%; box-sizing:border-box; border:1px solid #B91C1C; border-radius:4px; padding:10px 12px; outline:none;">
                            </div>

                            <button type="submit"
                                    style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer; font-size:15px; margin-bottom:12px;">
                                Simpan Password Baru
                            </button>

                            <p style="text-align:center; font-size:12px; color:#6B7280; font-style:italic; margin:0;">
                                Halaman ini tidak dapat ditutup atau dilewati sebelum password baru berhasil disimpan.
                            </p>
                        </form>

                    </div>
                </div>

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

    {{-- MODAL M-01: SUCCESS (setelah password berhasil diubah) --}}
    @if (session('password_updated'))
        <div id="modal-sukses" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Password berhasil diubah.</p>
                <a href="{{ route('simpanan.setor') }}"
                   style="display:block; width:100%; box-sizing:border-box; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer; text-decoration:none;">
                    OKE
                </a>
            </div>
        </div>
    @endif
</x-full-width-layout>