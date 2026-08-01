<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Laporan Keseluruhan</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Periode: {{ $periodeLabel }}</p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center; background:#FDEEEE;">
            <div style="font-size:12px; color:#B91C1C; margin-bottom:8px;">Saldo Kas Koperasi</div>
            <div style="font-size:22px; font-weight:bold; color:#241412;">Rp {{ number_format($saldoKasKoperasi, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center; background:#FDEEEE;">
            <div style="font-size:12px; color:#B91C1C; margin-bottom:8px;">Simpanan Masuk</div>
            <div style="font-size:22px; font-weight:bold; color:#241412;">Rp {{ number_format($simpananMasuk, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center; background:#FDEEEE;">
            <div style="font-size:12px; color:#B91C1C; margin-bottom:8px;">Pinjaman Dicairkan</div>
            <div style="font-size:22px; font-weight:bold; color:#241412;">Rp {{ number_format($pinjamanDicairkan, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center; background:#FDEEEE;">
            <div style="font-size:12px; color:#B91C1C; margin-bottom:8px;">Cicilan Masuk</div>
            <div style="font-size:22px; font-weight:bold; color:#241412;">Rp {{ number_format($cicilanMasuk, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center; background:#FDEEEE;">
            <div style="font-size:12px; color:#B91C1C; margin-bottom:8px;">Denda Masuk</div>
            <div style="font-size:22px; font-weight:bold; color:#241412;">Rp {{ number_format($dendaMasuk, 0, ',', '.') }}</div>
        </div>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Ringkasan Per Modul</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">Modul</th>
                    <th style="text-align:left; padding:12px 16px;">Indikator Utama</th>
                    <th style="text-align:left; padding:12px 16px;">Nilai</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:12px 16px; color:#241412;">Anggota</td>
                    <td style="padding:12px 16px; color:#241412;">Anggota Aktif</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $anggotaAktif }}</td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('ketua.laporan.anggota', ['bulan' => $bulan, 'tahun' => $tahun]) }}" style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat</a>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #F3F4F6; background:#FDEEEE;">
                    <td style="padding:12px 16px; color:#241412;">Simpanan</td>
                    <td style="padding:12px 16px; color:#241412;">Total Terkumpul</td>
                    <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('ketua.laporan.simpanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat</a>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:12px 16px; color:#241412;">Pinjaman</td>
                    <td style="padding:12px 16px; color:#241412;">Pinjaman Aktif</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $pinjamanAktifCount }}</td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('ketua.laporan.pinjaman', ['bulan' => $bulan, 'tahun' => $tahun]) }}" style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat</a>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #F3F4F6; background:#FDEEEE;">
                    <td style="padding:12px 16px; color:#241412;">Cicilan</td>
                    <td style="padding:12px 16px; color:#241412;">Ketepatan Bayar</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $ketepatanBayar }}%</td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('ketua.laporan.cicilan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 16px; color:#241412;">Pengurus</td>
                    <td style="padding:12px 16px; color:#241412;">Total Aktif</td>
                    <td style="padding:12px 16px; color:#241412;">{{ $totalPengurusAktif }}</td>
                    <td style="padding:12px 16px;">
                        <a href="{{ route('ketua.laporan.pengurus', ['bulan' => $bulan, 'tahun' => $tahun]) }}" style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:20px;">
        Kartu Simpanan/Pinjaman/Cicilan/Denda Masuk dihitung per periode terpilih, sedangkan kartu Saldo Kas Koperasi bersifat kumulatif (all-time, dari modal awal koperasi). Kolom "Nilai" pada tabel Ringkasan Per Modul juga bersifat kumulatif (all-time), cocok jadi laporan bulanan resmi ke rapat anggota. Untuk memilih jenis/periode laporan lain, gunakan menu
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold;">Laporan</a>.
    </p>

</x-ketua-layout>