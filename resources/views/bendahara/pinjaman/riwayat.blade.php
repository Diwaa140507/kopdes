<x-bendahara-layout activeMenu="pinjaman" headerTitle="Pinjaman — Bendahara">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Pinjaman</h2>

    @include('bendahara.pinjaman._tabs', ['tabAktif' => 'riwayat'])

    <form method="GET" action="{{ route('bendahara.pinjaman.riwayat') }}"
          style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="ID Anggota / Nama"
               style="flex:0 0 280px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
        <select name="status" style="padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
            @foreach (['Semua', 'Aktif', 'Lunas', 'Ditolak', 'Menunggu Persetujuan', 'Menunggu Pencairan'] as $opt)
                <option value="{{ $opt }}" {{ $status === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
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
                    <th style="text-align:left; padding:12px 16px;">Nominal</th>
                    <th style="text-align:left; padding:12px 16px;">Tenor</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Sisa Hutang</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeColor = [
                        'Aktif' => ['bg' => '#DCEAFB', 'text' => '#1E5FA5'],
                        'Lunas' => ['bg' => '#DFF3E4', 'text' => '#1E7A34'],
                        'Ditolak' => ['bg' => '#FADBD8', 'text' => '#A5301F'],
                        'Menunggu Persetujuan' => ['bg' => '#FCE9C7', 'text' => '#8A5A00'],
                        'Menunggu Pencairan' => ['bg' => '#FCE9C7', 'text' => '#8A5A00'],
                    ];
                @endphp
                @forelse ($daftar as $row)
                    @php
                        $isRowSelected = optional($selected)->id_pinjaman === $row->id_pinjaman;
                        $warna = $badgeColor[$row->status_pinjaman] ?? ['bg' => '#F3F4F6', 'text' => '#241412'];
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->nominal_pinjaman, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->tenor_bulan }} bln</td>
                        <td style="padding:12px 16px;">
                            <span style="background:{{ $warna['bg'] }}; color:{{ $warna['text'] }}; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                {{ $row->status_pinjaman }}
                            </span>
                        </td>
                        <td style="padding:12px 16px; color:#241412;">
                            {{ is_null($row->sisa_hutang_computed) ? '—' : 'Rp ' . number_format($row->sisa_hutang_computed, 0, ',', '.') }}
                        </td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.pinjaman.riwayat', ['detail' => $row->id_pinjaman, 'cari' => $cari, 'status' => $status]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada data pinjaman.
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

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; display:flex; justify-content:space-between; gap:24px;">
            <div style="font-size:14px; color:#241412; line-height:1.9;">
                <div>ID Pinjaman: {{ $selected->id_pinjaman }} &nbsp;|&nbsp; Nama Anggota: {{ $selected->anggota->nama_lengkap ?? '-' }} &nbsp;|&nbsp; Tujuan: {{ $selected->tujuan_pinjaman }}</div>
                <div>Nominal: Rp {{ number_format($selected->nominal_pinjaman, 0, ',', '.') }} &nbsp;|&nbsp; Tenor: {{ $selected->tenor_bulan }} bulan &nbsp;|&nbsp; Cicilan/Bulan: Rp {{ number_format($selected->cicilan_per_bulan, 0, ',', '.') }}</div>
                <div>Persentase Jasa: {{ $selected->persentase_jasa }}% &nbsp;|&nbsp; Jumlah Jasa: Rp {{ number_format($selected->jumlah_jasa, 0, ',', '.') }}</div>
                <div>Total Kembali: Rp {{ number_format($selected->total_pengembalian, 0, ',', '.') }} &nbsp;|&nbsp; Sisa Hutang (computed): {{ is_null($selected->sisa_hutang_computed) ? '—' : 'Rp ' . number_format($selected->sisa_hutang_computed, 0, ',', '.') }}</div>
                <div>Tanggal Disetujui: {{ $selected->updated_at->format('d/m/Y') }} &nbsp;|&nbsp; Tanggal Dicairkan: {{ $selected->tanggal_pencairan ? \Carbon\Carbon::parse($selected->tanggal_pencairan)->format('d/m/Y') : '—' }}</div>
                <div>Diproses Oleh: {{ $selected->pengurusPencatat->nama_pengurus ?? '-' }} ({{ $selected->id_pengurus_pencatat ?? '-' }})</div>
                @if ($selected->status_pinjaman === 'Ditolak')
                    <div>Alasan Penolakan: {{ $selected->alasan_penolakan ?? '-' }}</div>
                @endif
            </div>
            <div style="flex-shrink:0;">
                @if ($selected->bukti_pencairan)
                    <a href="{{ asset('storage/' . $selected->bukti_pencairan) }}" target="_blank"
                       style="display:inline-block; padding:10px 16px; background:#B91C1C; color:#ffffff; border-radius:4px; font-size:13px; font-weight:bold; text-decoration:none; white-space:nowrap;">
                        Lihat Bukti Pencairan
                    </a>
                @else
                    <span style="display:inline-block; padding:10px 16px; background:#F3F4F6; color:#9CA3AF; border-radius:4px; font-size:13px; white-space:nowrap;">
                        Belum Ada Bukti
                    </span>
                @endif
            </div>
        </div>
    @endif

</x-bendahara-layout>