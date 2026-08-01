<x-sekretaris-layout activeMenu="kelola-data-anggota" headerTitle="Data Anggota — Sekretaris">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Data Anggota</h2>

    {{-- Tab utama --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('sekretaris.kelola-data-anggota.riwayat-perubahan') }}"
           style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">
            Riwayat Perubahan
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi') }}"
           style="padding:10px 20px; border:1px solid #B91C1C; border-radius:4px; text-decoration:none; color:#ffffff; font-weight:bold; background:#B91C1C;">
            Reset Kata Sandi
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.penghapusan') }}"
           style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">
            Penghapusan Anggota
        </a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Permintaan Reset Kata Sandi</h3>

    {{-- Sub-tab --}}
    <div style="display:flex; gap:8px; margin-bottom:16px;">
        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi') }}"
           style="padding:8px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">
            Menunggu
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi.sudah-diproses') }}"
           style="padding:8px 20px; border:1px solid #B91C1C; border-radius:4px; text-decoration:none; color:#ffffff; font-weight:bold; background:#B91C1C;">
            Sudah Diproses
        </a>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <thead>
            <tr style="background:#B91C1C; color:#ffffff;">
                <th style="text-align:left; padding:10px 12px;">ID Anggota</th>
                <th style="text-align:left; padding:10px 12px;">Nama</th>
                <th style="text-align:left; padding:10px 12px;">Tgl Diproses</th>
                <th style="text-align:left; padding:10px 12px;">Diproses Oleh</th>
                <th style="text-align:left; padding:10px 12px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $item)
                <tr style="border-bottom:1px solid #E5E7EB;">
                    <td style="padding:10px 12px;">{{ $item->id_anggota }}</td>
                    <td style="padding:10px 12px;">{{ $item->nama_lengkap }}</td>
                    <td style="padding:10px 12px;">{{ optional($item->tanggal_perubahan_terakhir)->format('d/m/y') ?? '-' }}</td>
                    <td style="padding:10px 12px;">{{ optional($item->pengurusPencatat)->nama_pengurus ?? '-' }}</td>
                    <td style="padding:10px 12px;">
                        <span style="background:#EAF7EC; color:#1E7A34; padding:4px 10px; border-radius:4px; font-size:12px;">Selesai</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:16px 12px; text-align:center; color:#6B7280;">Belum ada riwayat reset kata sandi yang diproses.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($selected)
        <p style="font-size:13px; color:#6B7280; margin:20px 0 8px;">Ditampilkan setelah klik baris — contoh: {{ $selected->id_anggota }}</p>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; max-width:600px;">
            <p style="margin:4px 0;">Nama Lengkap : {{ $selected->nama_lengkap }} | NIK : {{ $selected->nik }}</p>
            <p style="margin:4px 0;">Tanggal Diproses : {{ optional($selected->tanggal_perubahan_terakhir)->format('d/m/Y') ?? '-' }}</p>
            <p style="margin:4px 0;">Diproses Oleh : {{ optional($selected->pengurusPencatat)->nama_pengurus ?? '-' }} ({{ $selected->id_pengurus_pencatat ?? '-' }})</p>
            <p style="margin:4px 0;">Keterangan : Password baru sudah disampaikan manual ke anggota</p>
        </div>
    @endif

    <p style="font-size:13px; color:#6B7280; margin-top:24px;">
        Seluruh konten di halaman ini bersifat read-only — murni log audit trail riwayat reset password yang sudah selesai diproses, tidak ada aksi input lanjutan.
    </p>

</x-sekretaris-layout>
