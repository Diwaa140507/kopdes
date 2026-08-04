<x-ketua-layout activeMenu="pengurus" headerTitle="Tambah Pengurus — Ketua Koperasi">

    <div style="max-width:600px; background:#ffffff; border:1px solid #F3B4B4; border-radius:8px; padding:24px;">
        <h2 style="font-size:20px; color:#241412; margin:0 0 4px 0;">Tambah Pengurus Baru</h2>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">Isi data pengurus yang akan didaftarkan ke dalam sistem.</p>
        <hr style="border:none; border-top:1px solid #E5E7EB; margin-bottom:20px;">

        <form method="POST" action="{{ route('ketua.pengurus.store') }}">
            @csrf

            <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Nama Lengkap</label>
            <input type="text" name="nama_pengurus" value="{{ old('nama_pengurus') }}" placeholder="[Masukkan nama lengkap pengurus]"
                   style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-size:14px; margin-bottom:16px; box-sizing:border-box;">

            <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Jabatan</label>
            <select name="jabatan" style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-size:14px; margin-bottom:4px; box-sizing:border-box;">
                <option value="Sekretaris" {{ old('jabatan') === 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                <option value="Bendahara" {{ old('jabatan') === 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
            </select>
            <p style="font-size:12px; color:#6B7280; margin:0 0 16px 0;">(Sekretaris / Bendahara)</p>

            <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="[nama@gmail.com]"
                   style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-size:14px; margin-bottom:16px; box-sizing:border-box;">

            <label style="display:block; font-size:14px; color:#241412; margin-bottom:6px;">Password</label>
            <input type="text" name="password" placeholder="••••••••"
                   style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-size:14px; margin-bottom:16px; box-sizing:border-box;">

            

            <div style="display:flex; gap:12px;">
                <button type="submit"
                        style="padding:12px 24px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                    Simpan
                </button>
                <a href="{{ route('ketua.pengurus.index') }}"
                   style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; background:#ffffff; text-decoration:none; display:inline-block;">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- MODAL M-03: DATA TIDAK VALID (field kosong) --}}
    @if ($errors->any())
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Harap semua kolom diisi.</p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

</x-ketua-layout>