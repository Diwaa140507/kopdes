<x-sekretaris-layout activeMenu="dashboard" headerTitle="Dashboard Sekretaris">

    <h2 style="margin:0 0 4px 0; color:#241412;">Selamat Datang, {{ Auth::guard('pengurus')->user()->nama_pengurus }}</h2>
    <p style="margin:0 0 24px 0; color:#6B7280; font-size:14px;">
        ID Pengurus: {{ Auth::guard('pengurus')->user()->id_pengurus }} | Jabatan: {{ Auth::guard('pengurus')->user()->jabatan }} | Status: {{ Auth::guard('pengurus')->user()->status }}
    </p>

    <div style="display:flex; gap:20px; margin-bottom:32px;">
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="margin:0 0 8px 0; color:#7F1D1D; font-size:14px;">Pendaftaran Menunggu Verifikasi</p>
            <p style="margin:0; color:#B91C1C; font-size:32px; font-weight:bold;">{{ $menungguVerifikasiCount }}</p>
            <p style="margin:4px 0 0 0; color:#9CA3AF; font-size:12px;">calon anggota</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="margin:0 0 8px 0; color:#7F1D1D; font-size:14px;">Reset Kata Sandi Menunggu</p>
            <p style="margin:0; color:#B91C1C; font-size:32px; font-weight:bold;">{{ $resetMenungguCount }}</p>
            <p style="margin:4px 0 0 0; color:#9CA3AF; font-size:12px;">permintaan reset</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="margin:0 0 8px 0; color:#7F1D1D; font-size:14px;">Pengajuan Penghapusan Akun</p>
            <p style="margin:0; color:#B91C1C; font-size:32px; font-weight:bold;">{{ $penghapusanCount }}</p>
            <p style="margin:4px 0 0 0; color:#9CA3AF; font-size:12px;">pengajuan</p>
        </div>
    </div>

    <h3 style="color:#241412; margin-bottom:12px;">Antrian Verifikasi Terbaru</h3>
    <table style="width:100%; border-collapse:collapse; margin-bottom:32px;">
        <thead>
            <tr style="background:#B91C1C; color:#ffffff;">
                <th style="text-align:left; padding:10px 12px; font-size:14px;">Nama Calon Anggota</th>
                <th style="text-align:left; padding:10px 12px; font-size:14px;">NIK</th>
                <th style="text-align:left; padding:10px 12px; font-size:14px;">Tgl Daftar</th>
                <th style="text-align:left; padding:10px 12px; font-size:14px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($antrianVerifikasi as $calon)
                <tr style="border-bottom:1px solid #E5E7EB;">
                    <td style="padding:10px 12px; font-size:14px;">{{ $calon->nama_lengkap }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ $calon->nik }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ \Carbon\Carbon::parse($calon->tanggal_daftar)->format('d/m/y') }}</td>
                    <td style="padding:10px 12px; font-size:14px;">
                        <a href="{{ route('sekretaris.verifikasi') }}" style="color:#B91C1C;">Tinjau</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:16px 12px; font-size:14px; color:#9CA3AF; text-align:center;">Tidak ada antrian verifikasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="color:#241412; margin-bottom:12px;">Akses Cepat</h3>
    <div style="display:flex; gap:16px;">
        <a href="{{ route('sekretaris.verifikasi') }}"
           style="text-decoration:none; background:#B91C1C; color:#ffffff; font-weight:bold; padding:12px 24px; border-radius:6px; font-size:14px;">
            Verifikasi Anggota
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.index') }}"
           style="text-decoration:none; background:#ffffff; color:#B91C1C; font-weight:bold; padding:12px 24px; border-radius:6px; border:1px solid #B91C1C; font-size:14px;">
            Kelola Data Anggota
        </a>
    </div>

</x-sekretaris-layout>
