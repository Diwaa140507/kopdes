<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Laporan Anggota</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Periode: {{ $periodeLabel }}</p>

    <div style="display:flex; gap:16px; margin-bottom:24px;">
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Total Anggota</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $totalAnggota }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Anggota Aktif</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $anggotaAktif }}</div>
        </div>
        <div style="flex:1; border:1px solid #E5E7EB; border-radius:6px; padding:20px; text-align:center; background:#ffffff;">
            <div style="font-size:12px; color:#6B7280; margin-bottom:8px;">Anggota Baru Bulan Ini</div>
            <div style="font-size:24px; font-weight:bold; color:#241412;">{{ $anggotaBaru }}</div>
        </div>
    </div>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Tgl Bergabung</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Total Simpanan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarAnggota as $row)
                    @php $aktif = $row->status_keanggotaan === 'Terverifikasi'; @endphp
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->nama_lengkap }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ \Carbon\Carbon::parse($row->tanggal_daftar)->format('d/m/y') }}</td>
                        <td style="padding:12px 16px;">
                            @if ($aktif)
                                <span style="background:#EAF7EC; color:#1E7A34; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Aktif</span>
                            @else
                                <span style="background:#FADBD8; color:#A5301F; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Terhapus</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->total_simpanan_computed, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada data anggota.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:20px;">
        Laporan ini bersifat read-only, sumber data dari master data anggota. Untuk memilih jenis/periode laporan lain, gunakan menu
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#B91C1C; font-weight:bold;">Laporan</a>.
    </p>

</x-ketua-layout>
