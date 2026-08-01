<x-anggota-layout activeMenu="profil" headerTitle="Profil — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Profil Saya</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('profil.detail') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Detail Profil</a>
        <a href="{{ route('profil.ubah-data-diri') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Ubah Data Diri</a>
        <a href="{{ route('profil.ajukan-penghapusan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Ajukan Penghapusan Akun</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Detail Profil Anggota</h3>

    <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px;">
        @if ($anggota->dokumen_pendukung)
            <img src="{{ asset('storage/' . $anggota->dokumen_pendukung) }}" alt="Foto Profil"
                 style="width:90px; height:90px; border-radius:6px; object-fit:cover; border:1px solid #F3B4B4;">
        @else
            <div style="width:90px; height:90px; border-radius:6px; background:#FDEEEE; border:1px solid #F3B4B4; display:flex; align-items:center; justify-content:center; color:#B91C1C; font-size:12px; text-align:center;">
                Foto<br>Profil
            </div>
        @endif
        <div>
            <p style="margin:0 0 4px; font-size:18px; font-weight:bold; color:#241412;">{{ $anggota->nama_lengkap }}</p>
            <p style="margin:0 0 8px; font-size:13px; color:#6B7280;">ID Anggota: {{ $anggota->id_anggota }}</p>
            <span style="display:inline-block; padding:3px 14px; border-radius:4px; font-size:12px; font-weight:bold; background:#EAF7EC; color:#1E7A34;">Status: {{ $anggota->status_keanggotaan }}</span>
        </div>
    </div>

    <h3 style="font-size:15px; font-weight:bold; color:#241412; margin:0 0 10px;">Informasi Identitas</h3>
    <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px 20px; margin-bottom:20px; max-width:600px;">
        <p style="margin:0 0 8px; font-size:14px; color:#241412;">NIK &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $anggota->nik }}</p>
        <p style="margin:0 0 8px; font-size:14px; color:#241412;">Jenis Kelamin &nbsp;: {{ $anggota->jenis_kelamin }}</p>
        <p style="margin:0 0 8px; font-size:14px; color:#241412;">Tanggal Lahir &nbsp;: {{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d/m/Y') }}</p>
        <p style="margin:0 0 8px; font-size:14px; color:#241412;">No. HP Aktif &nbsp;&nbsp;: {{ $anggota->no_hp }}</p>
        <p style="margin:0; font-size:14px; color:#241412;">Alamat Lengkap &nbsp;: {{ $anggota->alamat_lengkap }}</p>
    </div>

    <h3 style="font-size:15px; font-weight:bold; color:#241412; margin:0 0 10px;">Informasi Keanggotaan</h3>
    <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px 20px; margin-bottom:20px; max-width:600px;">
        <p style="margin:0 0 8px; font-size:14px; color:#241412;">Tanggal Bergabung &nbsp;: {{ \Carbon\Carbon::parse($anggota->tanggal_daftar)->format('d/m/Y') }}</p>
        <p style="margin:0; font-size:14px; color:#241412;">
            Perubahan Terakhir &nbsp;: {{ $anggota->tanggal_perubahan_terakhir ? \Carbon\Carbon::parse($anggota->tanggal_perubahan_terakhir)->format('d/m/Y') : '-' }}
        </p>
    </div>

    <h3 style="font-size:15px; font-weight:bold; color:#241412; margin:0 0 10px;">Ringkasan Keuangan</h3>
    <div style="display:flex; gap:20px; max-width:700px;">
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#8A5A00; font-size:13px;">Total Simpanan</p>
            <p style="margin:0; color:#241412; font-size:18px; font-weight:bold;">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#8A5A00; font-size:13px;">Status Pinjaman</p>
            <p style="margin:0; color:#B91C1C; font-size:18px; font-weight:bold;">{{ $statusPinjaman }}</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#8A5A00; font-size:13px;">Sisa Hutang</p>
            <p style="margin:0; color:#241412; font-size:18px; font-weight:bold;">Rp {{ number_format($sisaHutang, 0, ',', '.') }}</p>
        </div>
    </div>

</x-anggota-layout>