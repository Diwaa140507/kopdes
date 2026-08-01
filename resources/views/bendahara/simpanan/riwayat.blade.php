<x-bendahara-layout activeMenu="simpanan" headerTitle="Simpanan — Bendahara">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Simpanan</h2>

    @include('bendahara.simpanan._tabs', ['tabAktif' => 'riwayat'])

    <form method="GET" action="{{ route('bendahara.simpanan.riwayat') }}"
          style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Nama / ID anggota..."
               style="flex:0 0 320px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
        <select name="filter" onchange="this.form.submit()"
                style="padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
            <option value="Semua" {{ !$filter || $filter == 'Semua' ? 'selected' : '' }}>Semua</option>
            <option value="Setoran" {{ $filter == 'Setoran' ? 'selected' : '' }}>Setoran</option>
            <option value="Penarikan" {{ $filter == 'Penarikan' ? 'selected' : '' }}>Penarikan</option>
        </select>
        <button type="submit"
                style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
            Cari
        </button>
        @if ($cari || ($filter && $filter !== 'Semua'))
            <a href="{{ route('bendahara.simpanan.riwayat') }}" style="font-size:13px; color:#6B7280; text-decoration:underline;">Reset</a>
        @endif
    </form>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">Tanggal</th>
                    <th style="text-align:left; padding:12px 16px;">Nama Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Jenis</th>
                    <th style="text-align:left; padding:12px 16px;">Transaksi</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                    @php
                        $isRowSelected = optional($selected)->id_simpanan === $item->id_simpanan;
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $item->created_at->format('d/m/y') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $item->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $item->jenis_simpanan }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $item->jenis_transaksi }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px;">
                            @if ($item->status_transaksi == 'Berhasil')
                                <span style="background:#DFF3E4; color:#1E7A34; padding:4px 10px; border-radius:4px; font-size:12px;">Berhasil</span>
                            @else
                                <span style="background:#FADBD8; color:#A5301F; padding:4px 10px; border-radius:4px; font-size:12px;">Ditolak</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.simpanan.riwayat', ['detail' => $item->id_simpanan, 'cari' => $cari, 'filter' => $filter]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada riwayat transaksi simpanan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selected)
        <h3 style="font-size:16px; color:#241412; margin:20px 0 4px 0;">Detail Transaksi — {{ $selected->id_anggota }}</h3>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
            (ditampilkan setelah klik "Detail" pada salah satu baris)
        </p>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; display:flex; justify-content:space-between; gap:24px;">
            <div style="font-size:14px; color:#241412; line-height:1.9;">
                <div>ID Simpanan: {{ $selected->id_simpanan }} &nbsp;|&nbsp; Nama Anggota: {{ $selected->anggota->nama_lengkap ?? '-' }}</div>
                <div>Jenis: {{ $selected->jenis_simpanan }} &nbsp;|&nbsp; Transaksi: {{ $selected->jenis_transaksi }} &nbsp;|&nbsp; Jumlah: Rp {{ number_format($selected->jumlah, 0, ',', '.') }}</div>

                @if ($selected->jenis_transaksi === 'Setoran')
                    <div>Saluran Pembayaran: {{ $selected->saluran_pembayaran ?? '-' }}</div>
                @else
                    <div>Metode Penarikan: {{ $selected->metode_penarikan ?? '-' }}</div>
                    <div>Rekening Tujuan: {{ $selected->rekening_tujuan ?? '-' }} a.n. {{ $selected->nama_pemilik_rekening ?? '-' }}</div>
                @endif

                <div>Saldo Wajib Setelah Transaksi: Rp {{ number_format($selected->saldo_simpanan_wajib, 0, ',', '.') }} &nbsp;|&nbsp; Saldo Sukarela Setelah Transaksi: Rp {{ number_format($selected->saldo_simpanan_sukarela, 0, ',', '.') }}</div>
                <div>Status: {{ $selected->status_transaksi }}</div>
                @if ($selected->status_transaksi === 'Ditolak')
                    <div>Catatan Penolakan: {{ $selected->catatan_penolakan ?? '-' }}</div>
                @endif
            </div>
            <div style="flex-shrink:0;">
                @php
                    $buktiFile = $selected->bukti_transaksi;
                @endphp
                @if ($buktiFile)
                    <a href="{{ asset('storage/' . $buktiFile) }}" target="_blank"
                       style="display:inline-block; padding:10px 16px; background:#B91C1C; color:#ffffff; border-radius:4px; font-size:13px; font-weight:bold; text-decoration:none; white-space:nowrap;">
                        Lihat Bukti
                    </a>
                @else
                    <span style="display:inline-block; padding:10px 16px; background:#F3F4F6; color:#9CA3AF; border-radius:4px; font-size:13px; white-space:nowrap;">
                        Tidak Ada Bukti
                    </span>
                @endif
            </div>
        </div>
    @endif

</x-bendahara-layout>