<x-ketua-layout activeMenu="dashboard" headerTitle="Dashboard — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Selamat Datang, {{ $pengurus->nama_pengurus }}</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">
        ID Pengurus: {{ $pengurus->id_pengurus }} | Jabatan: {{ $pengurus->jabatan }} | Status: {{ $pengurus->status }}
    </p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Anggota Aktif</div>
            <div style="font-size:28px; font-weight:bold; color:#241412;">{{ $totalAnggotaAktif }}</div>
            <div style="font-size:12px; color:#6B7280;">anggota</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Pengurus Aktif</div>
            <div style="font-size:28px; font-weight:bold; color:#241412;">{{ $totalPengurusAktif }}</div>
            <div style="font-size:12px; color:#6B7280;">pengurus</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Laporan Bulan Ini</div>
            <div style="font-size:28px; font-weight:bold; color:#241412;">{{ $laporanBulanIni }}</div>
            <div style="font-size:12px; color:#6B7280;">laporan dibuat</div>
        </div>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Rekap Keuangan Bulan Ini</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:12px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">Keterangan</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px;">Periode</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:12px 16px; color:#241412;">Total Simpanan Masuk</td>
                    <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($totalSimpananMasuk, 0, ',', '.') }}</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $periodeLabel }}</td>
                </tr>
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:12px 16px; color:#241412;">Total Pinjaman Dicairkan</td>
                    <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($totalPinjamanDicairkan, 0, ',', '.') }}</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $periodeLabel }}</td>
                </tr>
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:12px 16px; color:#241412;">Total Cicilan Masuk</td>
                    <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($totalCicilanMasuk, 0, ',', '.') }}</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $periodeLabel }}</td>
                </tr>
                <tr>
                    <td style="padding:12px 16px; color:#241412;">Total Denda Masuk</td>
                    <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($totalDendaMasuk, 0, ',', '.') }}</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $periodeLabel }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="text-align:right; margin-bottom:24px;">
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold; font-size:13px; text-decoration:none;">Lihat Laporan Lengkap ›</a>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Daftar Pengurus Aktif</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:8px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Pengurus</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Jabatan</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarPengurus as $p)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $p->id_pengurus }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $p->nama_pengurus }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $p->jabatan }}</td>
                        <td style="padding:12px 16px;">
                            <span style="background:#DFF3E4; color:#1E7A34; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Menjabat</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="text-align:right; margin-bottom:24px;">
        <a href="{{ route('ketua.pengurus.index') }}" style="color:#B91C1C; font-weight:bold; font-size:13px; text-decoration:none;">Kelola Pengurus ›</a>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Akses Cepat</h3>
    <div style="display:flex; gap:12px;">
        <a href="{{ route('ketua.laporan.pilih') }}"
           style="flex:1; text-align:center; padding:14px; background:#B91C1C; color:#ffffff; border-radius:4px; font-weight:bold; font-size:14px; text-decoration:none;">
            Buat Laporan
        </a>
        <a href="{{ route('ketua.pengurus.index') }}"
           style="flex:1; text-align:center; padding:14px; border:1px solid #B91C1C; color:#B91C1C; border-radius:4px; font-weight:bold; font-size:14px; text-decoration:none; background:#ffffff;">
            Tambah Pengurus
        </a>
        <a href="{{ route('ketua.pengurus.index') }}"
           style="flex:1; text-align:center; padding:14px; border:1px solid #D1D5DB; color:#241412; border-radius:4px; font-weight:bold; font-size:14px; text-decoration:none; background:#ffffff;">
            Berhentikan Pengurus
        </a>
    </div>

</x-ketua-layout>
