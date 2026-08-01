<x-anggota-layout activeMenu="dashboard" headerTitle="Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 4px;">Selamat Datang, {{ $anggota->nama_lengkap }}</h2>
    <p style="margin:0 0 24px; color:#6B7280; font-size:14px;">ID Anggota: {{ $anggota->id_anggota }} | Status: {{ $anggota->status_keanggotaan }}</p>

    {{-- Kartu ringkasan --}}
    <div style="display:flex; gap:20px; margin-bottom:24px;">
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="margin:0 0 8px; color:#7F1D1D; font-size:14px;">Saldo Simpanan Wajib</p>
            <p style="margin:0; color:#B91C1C; font-size:24px; font-weight:bold;">Rp {{ number_format($saldoWajib, 0, ',', '.') }}</p>
            <p style="margin:4px 0 0; color:#9CA3AF; font-size:12px;">diperbarui otomatis</p>
        </div>
        <div style="flex:1; background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center;">
            <p style="margin:0 0 8px; color:#7F1D1D; font-size:14px;">Saldo Simpanan Sukarela</p>
            <p style="margin:0; color:#B91C1C; font-size:24px; font-weight:bold;">Rp {{ number_format($saldoSukarela, 0, ',', '.') }}</p>
            <p style="margin:4px 0 0; color:#9CA3AF; font-size:12px;">diperbarui otomatis</p>
        </div>
        <a href="{{ route('pinjaman.detail') }}" style="flex:1; text-decoration:none; display:block;">
        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; text-align:center; cursor:pointer;">
            <p style="margin:0 0 8px; color:#7F1D1D; font-size:14px;">Pinjaman Aktif</p>
            @if ($pinjamanAktif)
                <p style="margin:0; color:#B91C1C; font-size:24px; font-weight:bold;">Rp {{ number_format($sisaHutang, 0, ',', '.') }}</p>
                <p style="margin:4px 0 0; color:#9CA3AF; font-size:12px;">Sisa Hutang</p>
            @else
                <p style="margin:0; color:#9CA3AF; font-size:16px;">Tidak ada</p>
                <p style="margin:4px 0 0; color:#9CA3AF; font-size:12px;">belum ada pinjaman aktif</p>
            @endif
        </div>
        </a>
    </div>

    {{-- Pengingat cicilan --}}
    @if ($pengingatCicilan)
        <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Pengingat Cicilan</h3>
        <div style="background:{{ $pengingatCicilan['terlambat'] ? '#FDEEEE' : '#FCE9C7' }}; border-radius:6px; padding:16px 20px; margin-bottom:24px;">
            <p style="margin:0; color:{{ $pengingatCicilan['terlambat'] ? '#B91C1C' : '#8A5A00' }}; font-size:14px;">
                ⚠ Cicilan angsuran ke-{{ $pengingatCicilan['angsuran_ke'] }} dari {{ $pengingatCicilan['total_angsuran'] }}
                {{ $pengingatCicilan['terlambat'] ? 'sudah lewat jatuh tempo' : 'jatuh tempo' }} pada {{ $pengingatCicilan['jatuh_tempo']->format('d/m/Y') }}
            </p>
            <p style="margin:4px 0 0; color:{{ $pengingatCicilan['terlambat'] ? '#B91C1C' : '#8A5A00' }}; font-size:14px;">
                Nominal: Rp {{ number_format($pengingatCicilan['nominal'], 0, ',', '.') }}
            </p>
        </div>
    @endif

    {{-- Riwayat transaksi --}}
    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Riwayat Transaksi Terakhir</h3>
    <table style="width:100%; border-collapse:collapse; font-size:14px; margin-bottom:24px;">
        <thead>
            <tr style="background:#B91C1C; color:#ffffff;">
                <th style="text-align:left; padding:10px 12px;">Tanggal</th>
                <th style="text-align:left; padding:10px 12px;">Jenis</th>
                <th style="text-align:left; padding:10px 12px;">Keterangan</th>
                <th style="text-align:left; padding:10px 12px;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayatTransaksi as $item)
                <tr style="border-bottom:1px solid #E5E7EB;">
                    <td style="padding:10px 12px;">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/y') }}</td>
                    <td style="padding:10px 12px;">{{ $item['jenis'] }}</td>
                    <td style="padding:10px 12px;">{{ $item['keterangan'] }}</td>
                    <td style="padding:10px 12px;">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:16px 12px; text-align:center; color:#6B7280;">Belum ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Akses cepat --}}
    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Akses Cepat</h3>
    <div style="display:flex; gap:16px;">
        <a href="{{ route('simpanan.setor') }}" style="text-decoration:none; padding:12px 24px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px;">Setor Simpanan</a>
        <a href="{{ route('pinjaman.ajukan') }}" style="text-decoration:none; padding:12px 24px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px;">Ajukan Pinjaman</a>
        <a href="{{ route('cicilan.bayar') }}" style="text-decoration:none; padding:12px 24px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px;">Bayar Cicilan</a>
        <a href="{{ route('profil.detail') }}" style="text-decoration:none; padding:12px 24px; border:1px solid #B91C1C; border-radius:6px; color:#B91C1C; font-weight:bold; font-size:14px;">Profil Saya</a>
    </div>

    {{-- MODAL M-01: SUCCESS (login berhasil) --}}
    @if (session('login_success'))
        <div id="modal-login-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Login berhasil. Selamat datang kembali.</p>
                <button type="button" onclick="document.getElementById('modal-login-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-anggota-layout>