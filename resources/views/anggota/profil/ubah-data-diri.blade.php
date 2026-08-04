<x-anggota-layout activeMenu="profil" headerTitle="Profil — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Profil Saya</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('profil.detail') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Detail Profil</a>
        <a href="{{ route('profil.ubah-data-diri') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Ubah Data Diri</a>
        <a href="{{ route('profil.ajukan-penghapusan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Ajukan Penghapusan Akun</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Ubah Data Diri</h3>

    <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:14px 18px; margin-bottom:20px; max-width:600px;">
        <p style="margin:0 0 8px; color:#B91C1C; font-size:13px; font-weight:bold;">Data berikut bersifat tetap dan tidak dapat diubah melalui sistem:</p>
        <p style="margin:0; color:#7F1D1D; font-size:13px;">
            NIK: {{ $anggota->nik }} &nbsp;|&nbsp; Nama Lengkap: {{ $anggota->nama_lengkap }}<br>
            Jenis Kelamin: {{ $anggota->jenis_kelamin }} &nbsp;|&nbsp; Tanggal Lahir: {{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d/m/Y') }}
        </p>
    </div>

    <p style="font-size:14px; font-weight:bold; color:#241412; margin:0 0 12px;">Data yang Dapat Diperbarui</p>

    <form method="POST" action="{{ route('profil.ubah-data-diri.store') }}" enctype="multipart/form-data" style="max-width:500px;">
        @csrf

        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Foto Profil</label>
            <div style="display:flex; align-items:center; gap:16px;">
                @if ($anggota->foto_profil)
                    <img src="{{ Storage::url($anggota->foto_profil) }}" alt="Foto Profil"
                         style="width:64px; height:64px; border-radius:6px; object-fit:cover; border:1px solid #F3B4B4;">
                @else
                    <div style="width:64px; height:64px; border-radius:6px; background:#FDEEEE; border:1px solid #F3B4B4; display:flex; align-items:center; justify-content:center; color:#B91C1C; font-size:11px; text-align:center;">
                        Foto<br>Profil
                    </div>
                @endif
                <input type="file" name="foto_profil" accept="image/*"
                       style="flex:1; padding:8px; border:1px solid #B91C1C; border-radius:6px; font-size:13px; box-sizing:border-box; background:#ffffff;">
            </div>
            <p style="margin:6px 0 0; font-size:12px; color:#6B7280;">Format gambar, maksimal 2MB. Kosongkan jika tidak ingin mengganti foto.</p>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Email Aktif</label>
            <input type="email" name="email" value="{{ old('email', $anggota->email) }}"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Alamat Lengkap</label>
            <input type="text" name="alamat_lengkap" value="{{ old('alamat_lengkap', $anggota->alamat_lengkap) }}"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Kata Sandi Lama</label>
            <input type="password" name="kata_sandi_lama" placeholder="(wajib diisi jika ingin mengganti kata sandi)"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Kata Sandi Baru</label>
            <input type="password" name="kata_sandi_baru"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>

        <div style="margin-bottom:8px;">
            <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Konfirmasi Kata Sandi</label>
            <input type="password" name="konfirmasi_kata_sandi"
                   style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
        </div>

        <p style="margin:0 0 4px; font-size:12px; color:#6B7280;">* Kosongkan ketiga field kata sandi jika tidak ingin mengubah kata sandi.</p>
        <p style="margin:0 0 20px; font-size:12px; color:#8A5A00;">Lupa kata sandi lama? Keluar dari akun dan gunakan "Lupa Kata Sandi".</p>

        <button type="submit" style="padding:12px 24px; border:none; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px; cursor:pointer; margin-right:12px;">
            Simpan Perubahan
        </button>
        <a href="{{ route('profil.ubah-data-diri') }}" style="display:inline-block; padding:12px 24px; border:1px solid #999999; border-radius:6px; color:#999999; font-weight:bold; font-size:14px; text-decoration:none;">
            Batal
        </a>
    </form>

    {{-- MODAL M-03: DATA TIDAK VALID (field kosong / password tidak cocok atau <8 karakter) --}}
    @if ($errors->any())
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">
                    @if ($errors->has('kata_sandi_baru') || $errors->has('konfirmasi_kata_sandi'))
                        Password tidak valid atau tidak cocok.
                    @elseif ($errors->has('foto_profil'))
                        {{ $errors->first('foto_profil') }}
                    @else
                        Harap semua kolom yang wajib diisi.
                    @endif
                </p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-01: SUCCESS (data berhasil diubah) --}}
    @if (session('success'))
        <div id="modal-ubah-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-ubah-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-anggota-layout>
