<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Laporan Cicilan</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Periode: {{ $periodeLabel }}</p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Cicilan Masuk</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalCicilanMasuk, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Denda Masuk</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalDendaMasuk, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Cicilan Terlambat</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $cicilanTerlambat }}</div>
        </div>
    </div>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Angsuran ke</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px;">Denda</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Tgl Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cicilan as $row)
                    @php $terlambat = $row->jumlah_denda > 0; @endphp
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->no_angsuran }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah_pembayaran - $row->jumlah_denda, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah_denda, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px;">
                            @if ($terlambat)
                                <span style="background:#FCE9C7; color:#8A5A00; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Terlambat</span>
                            @else
                                <span style="background:#EAF7EC; color:#1E7A34; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Tepat Waktu</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px; color:#241412;">{{ \Carbon\Carbon::parse($row->tanggal_pembayaran)->format('d/m/y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada pembayaran cicilan terkonfirmasi pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:20px;">
        Laporan ini bersifat read-only, sumber data dari pembayaran cicilan yang sudah dikonfirmasi Bendahara. Untuk memilih jenis/periode laporan lain, gunakan menu
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold;">Laporan</a>.
    </p>

</x-ketua-layout>
