<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Laporan Pinjaman</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Periode: {{ $periodeLabel }}</p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Dicairkan</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalDicairkan, 0, ',', '.') }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Pinjaman Aktif</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $pinjamanAktif }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Sisa Hutang</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Nominal</th>
                    <th style="text-align:left; padding:12px 16px;">Tenor</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Sisa Hutang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeColor = [
                        'Aktif' => ['bg' => '#FDEEEE', 'text' => '#B91C1C'],
                        'Lunas' => ['bg' => '#E7F5EA', 'text' => '#1E7A34'],
                        'Ditolak' => ['bg' => '#FADBD8', 'text' => '#A5301F'],
                    ];
                @endphp
                @forelse ($daftarPinjaman as $row)
                    @php $warna = $badgeColor[$row->status_pinjaman] ?? ['bg' => '#F3F4F6', 'text' => '#241412']; @endphp
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->nominal_pinjaman, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->tenor_bulan }} bln</td>
                        <td style="padding:12px 16px;">
                            <span style="background:{{ $warna['bg'] }}; color:{{ $warna['text'] }}; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                {{ $row->status_pinjaman }}
                            </span>
                        </td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->sisa_hutang_computed, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada data pinjaman dicairkan pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:20px;">
        Laporan ini bersifat read-only, sumber data dari transaksi Bendahara. Untuk memilih jenis/periode laporan lain, gunakan menu
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold;">Laporan</a>.
    </p>

</x-ketua-layout>
