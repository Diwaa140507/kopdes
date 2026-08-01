<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Laporan Simpanan</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Periode: {{ $periodeLabel }}</p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Simpanan Wajib</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalSimpananWajib, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Simpanan Sukarela</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalSimpananSukarela, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Penarikan</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Jenis</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $row)
                    @php
                        $labelJenis = $row->jenis_transaksi === 'Setoran'
                            ? 'Setoran ' . $row->jenis_simpanan
                            : 'Penarikan ' . $row->jenis_simpanan;
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $labelJenis }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ \Carbon\Carbon::parse($row->tanggal_transaksi)->format('d/m/y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada transaksi simpanan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:20px;">
        Laporan ini bersifat read-only, sumber data dari transaksi Bendahara yang sudah dikonfirmasi. Untuk memilih jenis/periode laporan lain, gunakan menu
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold;">Laporan</a>.
    </p>

</x-ketua-layout>
