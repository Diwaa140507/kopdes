<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Laporan Pengurus</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Periode: {{ $periodeLabel }}</p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Pengurus Aktif</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $totalPengurusAktif }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Pergantian Pengurus Bulan Ini</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $pergantianBulanIni }}</div>
        </div>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Daftar Pengurus Aktif</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Pengurus</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Jabatan</th>
                    <th style="text-align:left; padding:12px 16px;">Tgl Menjabat</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPengurus as $row)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_pengurus }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->nama_pengurus }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->jabatan }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->tanggal_diangkat->format('d/m/y') }}</td>
                        <td style="padding:12px 16px;">
                            <span style="background:#EAF7EC; color:#1E7A34; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Menjabat</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada data pengurus.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Riwayat Perubahan Kepengurusan</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; padding:16px;">
        @forelse ($riwayatPerubahan as $item)
            @php $diangkat = str_contains($item, 'diangkat'); @endphp
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #F3F4F6; font-size:14px; color:#241412;">
                <span style="flex-shrink:0; background:{{ $diangkat ? '#EAF7EC' : '#FDEEEE' }}; color:{{ $diangkat ? '#1E7A34' : '#A5301F' }}; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:bold;">
                    {{ $diangkat ? 'Diangkat' : 'Diberhentikan' }}
                </span>
                <span>{{ $item }}</span>
            </div>
        @empty
            <p style="font-size:13px; color:#6B7280; margin:0;">
                Tidak ada perubahan kepengurusan (pengangkatan/pemberhentian) pada periode {{ $periodeLabel }}.
            </p>
        @endforelse
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:20px;">
        Laporan ini bersifat read-only, sumber data dari data pengurus. Untuk memilih jenis/periode laporan lain, gunakan menu
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold;">Laporan</a>.
    </p>

</x-ketua-layout>