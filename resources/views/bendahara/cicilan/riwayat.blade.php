{{-- Simpan di: resources/views/bendahara/cicilan/riwayat.blade.php --}}
<x-bendahara-layout activeMenu="cicilan" headerTitle="Cicilan — Bendahara">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Cicilan</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Riwayat seluruh pembayaran cicilan (semua status)</p>

    @include('bendahara.cicilan._tabs', ['tabAktif' => 'riwayat'])

    <form method="GET" action="{{ route('bendahara.cicilan.riwayat') }}"
          style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="ID Anggota / Nama"
               style="flex:0 0 280px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
        
        </select>
        <button type="submit"
                style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
            Cari
        </button>
    </form>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Angsuran</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px;">Denda</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeColor = [
                        'Terverifikasi' => ['bg' => '#DFF3E4', 'text' => '#1E7A34'],
                        'Ditolak' => ['bg' => '#FADBD8', 'text' => '#A5301F'],
                        'Menunggu Konfirmasi' => ['bg' => '#FCE9C7', 'text' => '#8A5A00'],
                    ];
                @endphp
                @forelse ($daftar as $row)
                    @php
                        $isRowSelected = optional($selected)->id_cicilan === $row->id_cicilan;
                        $warna = $badgeColor[$row->status_pembayaran] ?? ['bg' => '#F3F4F6', 'text' => '#241412'];
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->no_angsuran }} dari {{ $row->pinjaman->tenor_bulan ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah_pembayaran, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah_denda, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px;">
                            <span style="background:{{ $warna['bg'] }}; color:{{ $warna['text'] }}; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                {{ $row->status_pembayaran }}
                            </span>
                        </td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.cicilan.riwayat', ['detail' => $row->id_cicilan, 'cari' => $cari, 'status' => $status]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada data cicilan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selected)
        <h3 style="font-size:16px; color:#241412; margin:0 0 4px 0;">Detail Riwayat — {{ $selected->id_anggota }}</h3>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
            (ditampilkan setelah klik "Detail" pada salah satu baris)
        </p>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px;">
            <div style="font-size:14px; color:#241412; line-height:1.9;">
                <div>ID Cicilan: {{ $selected->id_cicilan }} &nbsp;|&nbsp; ID Pinjaman: {{ $selected->id_pinjaman }}</div>
                <div>Nama Anggota: {{ $selected->anggota->nama_lengkap ?? '-' }} &nbsp;|&nbsp; Angsuran ke-: {{ $selected->no_angsuran }} dari {{ $selected->pinjaman->tenor_bulan ?? '-' }}</div>
                <div>Jumlah Bayar: Rp {{ number_format($selected->jumlah_pembayaran, 0, ',', '.') }} &nbsp;|&nbsp; Denda: Rp {{ number_format($selected->jumlah_denda, 0, ',', '.') }}</div>
                <div>Sisa Hutang (setelah bayar ini): {{ is_null($selected->sisa_hutang) ? '—' : 'Rp ' . number_format($selected->sisa_hutang, 0, ',', '.') }}</div>
                <div>Status: {{ $selected->status_pembayaran }}</div>
                @if ($selected->status_pembayaran === 'Ditolak')
                    <div>Catatan Penolakan: {{ $selected->catatan_penolakan ?? '-' }}</div>
                @endif
            </div>
        </div>
    @endif

</x-bendahara-layout>
