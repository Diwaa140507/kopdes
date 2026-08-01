<x-anggota-layout activeMenu="simpanan" headerTitle="Simpanan — Dashboard Anggota">

    <div style="display:flex; gap:20px; margin-bottom:24px;">
        <div style="flex:1; background:#FDEEEE; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#7F1D1D; font-size:13px;">Saldo Simpanan Wajib</p>
            <p style="margin:0; color:#241412; font-size:22px; font-weight:bold;">Rp {{ number_format($saldoWajib, 0, ',', '.') }}</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border-radius:6px; padding:16px; text-align:center;">
            <p style="margin:0 0 6px; color:#7F1D1D; font-size:13px;">Saldo Simpanan Sukarela</p>
            <p style="margin:0; color:#241412; font-size:22px; font-weight:bold;">Rp {{ number_format($saldoSukarela, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tab --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('simpanan.setor') }}" style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">Setoran</a>
        <a href="{{ route('simpanan.tarik') }}" style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">Penarikan</a>
        <a href="{{ route('simpanan.riwayat') }}" style="padding:10px 20px; border:1px solid #B91C1C; border-radius:4px; text-decoration:none; color:#ffffff; font-weight:bold; background:#B91C1C;">Riwayat</a>
    </div>

    @if (session('success'))
        <div style="background:#EAF7EC; color:#1E7A34; border:1px solid #1E7A34; border-radius:4px; padding:12px 16px; margin-bottom:16px; font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('simpanan.riwayat') }}" style="display:flex; gap:8px; margin-bottom:16px; max-width:650px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari transaksi..."
               style="flex:1; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-family:Arial, sans-serif;">
        <select name="filter" onchange="this.form.submit()"
                style="padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-family:Arial, sans-serif;">
            <option value="Semua" {{ !$filter || $filter == 'Semua' ? 'selected' : '' }}>Semua</option>
            <option value="Wajib" {{ $filter == 'Wajib' ? 'selected' : '' }}>Wajib</option>
            <option value="Sukarela" {{ $filter == 'Sukarela' ? 'selected' : '' }}>Sukarela</option>
        </select>
        <button type="submit" style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Cari</button>
    </form>

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <thead>
            <tr style="background:#B91C1C; color:#ffffff;">
                <th style="text-align:left; padding:10px 12px;">Tanggal</th>
                <th style="text-align:left; padding:10px 12px;">Jenis</th>
                <th style="text-align:left; padding:10px 12px;">Transaksi</th>
                <th style="text-align:left; padding:10px 12px;">Nominal</th>
                <th style="text-align:left; padding:10px 12px;">Status</th>
                <th style="text-align:left; padding:10px 12px;">Saldo Akhir</th>
                <th style="text-align:left; padding:10px 12px;">Bukti</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $item)
                <tr style="border-bottom:1px solid #E5E7EB;">
                    <td style="padding:10px 12px;">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/y') }}</td>
                    <td style="padding:10px 12px;">{{ $item->jenis_simpanan }}</td>
                    <td style="padding:10px 12px;">{{ $item->jenis_transaksi }}</td>
                    <td style="padding:10px 12px;">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td style="padding:10px 12px;">
                        @if ($item->status_transaksi == 'Berhasil')
                            <span style="background:#DFF3E4; color:#1E7A34; padding:4px 10px; border-radius:4px; font-size:12px;">Berhasil</span>
                        @elseif ($item->status_transaksi == 'Menunggu')
                            <span style="background:#FCE9C7; color:#8A5A00; padding:4px 10px; border-radius:4px; font-size:12px;">Menunggu</span>
                        @else
                            <span style="background:#FADBD8; color:#A5301F; padding:4px 10px; border-radius:4px; font-size:12px;">Ditolak</span>
                        @endif
                    </td>
                    <td style="padding:10px 12px;">
                        @if ($item->status_transaksi == 'Berhasil')
                            Rp {{ number_format($item->jenis_simpanan == 'Wajib' ? $item->saldo_simpanan_wajib : $item->saldo_simpanan_sukarela, 0, ',', '.') }}
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding:10px 12px;">
                        @if ($item->bukti_transaksi)
                            <a href="{{ asset('storage/' . $item->bukti_transaksi) }}" target="_blank"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat Bukti</a>
                        @else
                            <span style="color:#9CA3AF;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:16px 12px; text-align:center; color:#6B7280;">Belum ada transaksi simpanan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="font-size:13px; color:#6B7280; margin-top:16px;">Menampilkan {{ $riwayat->count() }} dari {{ $riwayat->count() }} transaksi</p>

</x-anggota-layout>