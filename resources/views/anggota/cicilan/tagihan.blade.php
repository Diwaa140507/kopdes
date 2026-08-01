<x-anggota-layout activeMenu="cicilan" headerTitle="Cicilan — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Cicilan</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('cicilan.tagihan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Tagihan</a>
        <a href="{{ route('cicilan.bayar') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Bayar Cicilan</a>
    </div>

    @if (session('info'))
        <div style="background:#FCE9C7; border-radius:6px; padding:12px 16px; margin-bottom:16px; color:#8A5A00; font-size:14px;">
            {{ session('info') }}
        </div>
    @endif
    @if (session('success'))
        <div style="background:#EAF7EC; border-radius:6px; padding:12px 16px; margin-bottom:16px; color:#1E7A34; font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($pinjamanAktif)
        <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Form Tagihan</h3>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:16px 20px; margin-bottom:16px;">
            <p style="margin:0 0 6px; font-size:14px; color:#241412;">
                <strong>ID Pinjaman:</strong> {{ $pinjamanAktif->id_pinjaman }} &nbsp;|&nbsp;
                <strong>Nominal Awal:</strong> Rp {{ number_format($pinjamanAktif->nominal_pinjaman, 0, ',', '.') }} &nbsp;|&nbsp;
                <strong>Tenor:</strong> {{ $pinjamanAktif->tenor_bulan }} bulan
            </p>
            <p style="margin:0 0 6px; font-size:14px; color:#241412;">
                <strong>Cicilan/Bulan:</strong> Rp {{ number_format($pinjamanAktif->cicilan_per_bulan, 0, ',', '.') }} &nbsp;|&nbsp;
                <strong>Sisa Hutang:</strong> Rp {{ number_format($sisaHutang, 0, ',', '.') }} &nbsp;|&nbsp;
                <strong>Status:</strong> {{ $pinjamanAktif->status_pinjaman }}
            </p>
            @if ($tagihanBerjalan)
                <p style="margin:0; font-size:14px; color:#B91C1C; font-weight:bold;">
                    Jatuh Tempo Berikutnya: {{ $tagihanBerjalan['jatuh_tempo']->format('d/m/Y') }}
                </p>
            @endif
        </div>

        @if ($tagihanBerjalan && $tagihanBerjalan['denda'] > 0)
            <div style="background:#FCE9C7; border-radius:6px; padding:12px 16px; margin-bottom:20px;">
                <p style="margin:0; color:#8A5A00; font-size:14px;">⚠ Denda keterlambatan: Rp {{ number_format($tagihanBerjalan['denda'], 0, ',', '.') }}</p>
            </div>
        @endif

        <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Jadwal Angsuran</h3>
        <table style="width:100%; border-collapse:collapse; font-size:14px; margin-bottom:24px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:10px 12px;">No.</th>
                    <th style="text-align:left; padding:10px 12px;">Jatuh Tempo</th>
                    <th style="text-align:left; padding:10px 12px;">Cicilan</th>
                    <th style="text-align:left; padding:10px 12px;">Denda</th>
                    <th style="text-align:left; padding:10px 12px;">Total Bayar</th>
                    <th style="text-align:left; padding:10px 12px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jadwalAngsuran as $baris)
                    @php
                        $isTerlambat = $baris['status'] === 'Terlambat';
                        $isLunas = $baris['status'] === 'Lunas';
                        $warnaStatus = match($baris['status']) {
                            'Lunas' => '#1E7A34',
                            'Terlambat' => '#B91C1C',
                            'Menunggu Konfirmasi' => '#8A5A00',
                            default => '#9CA3AF',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #E5E7EB; {{ $isTerlambat ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:10px 12px;">{{ $baris['no_angsuran'] }}</td>
                        <td style="padding:10px 12px; {{ $isTerlambat ? 'color:#B91C1C; font-weight:bold;' : '' }}">
                            {{ $baris['jatuh_tempo']->format('d/m/y') }}{{ $isTerlambat ? ' ◄' : '' }}
                        </td>
                        <td style="padding:10px 12px;">Rp {{ number_format($baris['cicilan'], 0, ',', '.') }}</td>
                        <td style="padding:10px 12px;">{{ $baris['denda'] > 0 ? 'Rp ' . number_format($baris['denda'], 0, ',', '.') : 'Rp 0' }}</td>
                        <td style="padding:10px 12px; {{ $isLunas ? 'color:#9CA3AF;' : '' }}">Rp {{ number_format($baris['total_bayar'], 0, ',', '.') }}</td>
                        <td style="padding:10px 12px; color:{{ $warnaStatus }}; font-weight:bold;">{{ $baris['status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Riwayat Pembayaran</h3>
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:10px 12px;">No.</th>
                    <th style="text-align:left; padding:10px 12px;">Tgl Bayar</th>
                    <th style="text-align:left; padding:10px 12px;">Jumlah</th>
                    <th style="text-align:left; padding:10px 12px;">Denda</th>
                    <th style="text-align:left; padding:10px 12px;">Sisa Hutang</th>
                    <th style="text-align:left; padding:10px 12px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayatPembayaran as $bayar)
                    <tr style="border-bottom:1px solid #E5E7EB; background:#FDEEEE;">
                        <td style="padding:10px 12px;">{{ $bayar->no_angsuran }}</td>
                        <td style="padding:10px 12px;">{{ \Carbon\Carbon::parse($bayar->tanggal_pembayaran)->format('d/m/y') }}</td>
                        <td style="padding:10px 12px;">Rp {{ number_format($bayar->jumlah_pembayaran, 0, ',', '.') }}</td>
                        <td style="padding:10px 12px;">Rp {{ number_format($bayar->jumlah_denda, 0, ',', '.') }}</td>
                        <td style="padding:10px 12px;">Rp {{ number_format($bayar->sisa_hutang, 0, ',', '.') }}</td>
                        <td style="padding:10px 12px;">
                            <span style="display:inline-block; padding:3px 12px; border-radius:4px; font-size:12px; font-weight:bold; background:#EAF7EC; color:#1E7A34;">Terverifikasi</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:16px 12px; text-align:center; color:#6B7280;">Belum ada pembayaran terverifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <div style="border:1px dashed #B0B0B0; border-radius:6px; padding:40px 20px; text-align:center; max-width:700px;">
            <p style="margin:0 0 16px; color:#6B7280; font-size:14px;">Anda tidak memiliki pinjaman aktif, jadi belum ada tagihan cicilan.</p>
            <a href="{{ route('pinjaman.cek-kelayakan') }}" style="text-decoration:none; padding:10px 22px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px;">
                Ajukan Pinjaman
            </a>
        </div>
    @endif

</x-anggota-layout>
