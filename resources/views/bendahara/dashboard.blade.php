<x-bendahara-layout activeMenu="dashboard" headerTitle="Dashboard Bendahara">

    @php
        $pengurus = Auth::guard('pengurus')->user();
    @endphp

    <h2 style="font-size:24px; color:#241412; margin:0 0 4px 0;">Selamat Datang, {{ $pengurus->nama_pengurus }}</h2>
    <p style="font-size:14px; color:#6B7280; margin:0 0 24px 0;">
        ID Pengurus: {{ $pengurus->id_pengurus }} | Jabatan: {{ $pengurus->jabatan }} | Status: {{ $pengurus->status }}
    </p>

    <div style="display:flex; gap:20px; margin-bottom:32px;">
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="font-size:14px; color:#8A5A00; margin:0 0 8px 0;">Simpanan Menunggu Konfirmasi</p>
            <p style="font-size:36px; font-weight:bold; color:#B91C1C; margin:0;">{{ $simpananMenungguCount }}</p>
            <p style="font-size:13px; color:#6B7280; margin:4px 0 0 0;">transaksi</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="font-size:14px; color:#8A5A00; margin:0 0 8px 0;">Pengajuan Pinjaman Menunggu</p>
            <p style="font-size:36px; font-weight:bold; color:#B91C1C; margin:0;">{{ $pinjamanMenungguCount }}</p>
            <p style="font-size:13px; color:#6B7280; margin:4px 0 0 0;">pengajuan</p>
        </div>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Transaksi Simpanan Terbaru</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:32px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">Nama Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Jenis Transaksi</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiSimpananTerbaru as $row)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->jenis_transaksi }} {{ $row->jenis_simpanan }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px;">
                            @if ($row->jenis_transaksi === 'Setoran')
                                <a href="{{ route('bendahara.simpanan.setoran', ['detail' => $row->id_simpanan]) }}"
                                   style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Konfirmasi</a>
                            @else
                                <a href="{{ route('bendahara.simpanan.penarikan', ['detail' => $row->id_simpanan]) }}"
                                   style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Konfirmasi</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:20px 16px; text-align:center; color:#6B7280;">
                            Tidak ada transaksi menunggu konfirmasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Pengajuan Pinjaman Terbaru</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:32px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">Nama Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nominal</th>
                    <th style="text-align:left; padding:12px 16px;">Tujuan</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengajuanPinjamanTerbaru as $row)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->nama_lengkap }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->nominal_pinjaman, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->tujuan_pinjaman }}</td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.pinjaman.index') }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Tinjau</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:20px 16px; text-align:center; color:#6B7280;">
                            Tidak ada pengajuan pinjaman menunggu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Akses Cepat</h3>
    <div style="display:flex; gap:12px;">
        <a href="{{ route('bendahara.simpanan.setoran') }}"
           style="padding:14px 24px; background:#B91C1C; color:#ffffff; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">
            Konfirmasi Simpanan
        </a>
        <a href="{{ route('bendahara.pinjaman.index') }}"
           style="padding:14px 24px; background:#ffffff; color:#B91C1C; border:1px solid #B91C1C; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">
            Tinjau Pinjaman
        </a>
        <a href="{{ route('bendahara.cicilan.index') }}"
           style="padding:14px 24px; background:#ffffff; color:#B91C1C; border:1px solid #B91C1C; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">
            Konfirmasi Cicilan
        </a>
    </div>

</x-bendahara-layout>
