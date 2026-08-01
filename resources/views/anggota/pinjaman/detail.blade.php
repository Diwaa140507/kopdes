<x-anggota-layout activeMenu="pinjaman" headerTitle="Pinjaman — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Pinjaman</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('pinjaman.cek-kelayakan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Cek Kelayakan</a>
        <a href="{{ route('pinjaman.ajukan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Ajukan Pinjaman</a>
        <a href="{{ route('pinjaman.detail') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Detail Pinjaman Aktif</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Detail Pinjaman Aktif</h3>

    @if (session('success'))
        <div style="background:#EAF7EC; border-radius:6px; padding:12px 16px; margin-bottom:20px; color:#1E7A34; font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($pinjamanAktif)
        {{-- Kondisi A: ada pinjaman aktif --}}
        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; margin-bottom:24px; max-width:700px;">
            <p style="margin:0 0 10px; font-size:14px; color:#241412;"><strong>ID Pinjaman</strong> &nbsp;:&nbsp; {{ $pinjamanAktif->id_pinjaman }}</p>
            <p style="margin:0 0 10px; font-size:14px; color:#241412;"><strong>Tujuan Pinjaman</strong> &nbsp;:&nbsp; {{ $pinjamanAktif->tujuan_pinjaman }}</p>
            <p style="margin:0 0 10px; font-size:14px; color:#241412;"><strong>Tanggal Pencairan</strong> &nbsp;:&nbsp; {{ $pinjamanAktif->tanggal_pencairan ? \Carbon\Carbon::parse($pinjamanAktif->tanggal_pencairan)->format('d/m/Y') : '-' }}</p>
            <p style="margin:0 0 10px; font-size:14px; color:#241412;">
                <strong>Nominal Pinjaman:</strong> Rp {{ number_format($pinjamanAktif->nominal_pinjaman, 0, ',', '.') }}
                &nbsp;|&nbsp; <strong>Tenor:</strong> {{ $pinjamanAktif->tenor_bulan }} bulan
            </p>
            <p style="margin:0 0 10px; font-size:14px; color:#241412;">
                <strong>Cicilan / bulan:</strong> Rp {{ number_format($pinjamanAktif->cicilan_per_bulan, 0, ',', '.') }}
                &nbsp;|&nbsp; <strong>Total Pengembalian:</strong> Rp {{ number_format($pinjamanAktif->total_pengembalian, 0, ',', '.') }}
            </p>
            <p style="margin:0 0 10px; font-size:15px; color:#B91C1C; font-weight:bold;">
                Sisa Hutang: Rp {{ number_format($sisaHutang, 0, ',', '.') }}
                <span style="font-weight:normal; font-size:12px; color:#7F1D1D;">(dihitung: Total Pengembalian – akumulasi cicilan terbayar)</span>
            </p>
            <p style="margin:0 0 10px; font-size:14px; color:#241412;">
                <strong>Jadwal Jatuh Tempo Berikutnya</strong> &nbsp;:&nbsp; {{ $jatuhTempoBerikutnya ? $jatuhTempoBerikutnya->format('d/m/Y') : '-' }}
            </p>
            <p style="margin:0; font-size:15px; color:#1E7A34; font-weight:bold;">
                Status Pinjaman: {{ $pinjamanAktif->status_pinjaman }}
            </p>
        </div>

        <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Riwayat Pembayaran Cicilan</h3>
        <table style="width:100%; max-width:700px; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:10px 12px;">Angsuran ke-</th>
                    <th style="text-align:left; padding:10px 12px;">Tanggal Bayar</th>
                    <th style="text-align:left; padding:10px 12px;">Nominal</th>
                    <th style="text-align:left; padding:10px 12px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayatCicilan as $cicilan)
                    <tr style="border-bottom:1px solid #E5E7EB; background:#FDEEEE;">
                        <td style="padding:10px 12px;">{{ $cicilan->no_angsuran }}</td>
                        <td style="padding:10px 12px;">{{ \Carbon\Carbon::parse($cicilan->tanggal_pembayaran)->format('d/m/Y') }}</td>
                        <td style="padding:10px 12px;">Rp {{ number_format($cicilan->jumlah_pembayaran, 0, ',', '.') }}</td>
                        <td style="padding:10px 12px;">
                            <span style="display:inline-block; padding:3px 12px; border-radius:4px; font-size:12px; font-weight:bold; background:#EAF7EC; color:#1E7A34;">Lunas</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:16px 12px; text-align:center; color:#6B7280;">Belum ada cicilan terbayar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        {{-- Kondisi B: tidak ada pinjaman aktif --}}
        <div style="border:1px dashed #B0B0B0; border-radius:6px; padding:40px 20px; text-align:center; max-width:700px;">
            <p style="margin:0 0 16px; color:#6B7280; font-size:14px;">Anda belum memiliki pinjaman aktif</p>
            <a href="{{ route('pinjaman.cek-kelayakan') }}" style="text-decoration:none; padding:10px 22px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px;">
                Ajukan Pinjaman
            </a>
        </div>
    @endif

</x-anggota-layout>
