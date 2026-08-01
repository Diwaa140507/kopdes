<x-sekretaris-layout activeMenu="verifikasi" headerTitle="Verifikasi Pendaftaran — Sekretaris">

    <h1 style="font-size:22px; font-weight:700; color:#241412; margin:0 0 16px 0;">Verifikasi Pendaftaran Anggota</h1>

    {{-- Tab --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('sekretaris.verifikasi') }}"
           style="padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;
                  background:#fff; color:#B91C1C; border:1px solid #B91C1C;">
            Menunggu Verifikasi
        </a>
        <a href="{{ route('sekretaris.verifikasi.sudah-diproses') }}"
           style="padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;
                  background:#B91C1C; color:#fff;">
            Sudah Diproses
        </a>
    </div>

    {{-- Search + filter --}}
    <form method="GET" action="{{ route('sekretaris.verifikasi.sudah-diproses') }}" style="display:flex; gap:8px; margin-bottom:16px; max-width:650px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Nama / NIK anggota..."
               style="flex:1; padding:8px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
        <button type="submit"
                style="background:#B91C1C; color:#fff; border:none; padding:8px 20px; border-radius:6px; font-weight:600; cursor:pointer;">
            Cari
        </button>
        <select name="filter" onchange="this.form.submit()"
                style="padding:8px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
            <option value="Semua Status" {{ $filter == 'Semua Status' || !$filter ? 'selected' : '' }}>Filter: Semua Status</option>
            <option value="Terverifikasi" {{ $filter == 'Terverifikasi' ? 'selected' : '' }}>Disetujui</option>
            <option value="Ditolak" {{ $filter == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
    </form>

    {{-- Tabel riwayat --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <thead>
            <tr style="background:#B91C1C; color:#fff; text-align:left;">
                <th style="padding:10px 12px; font-size:13px;">No.</th>
                <th style="padding:10px 12px; font-size:13px;">Nama Calon Anggota</th>
                <th style="padding:10px 12px; font-size:13px;">NIK</th>
                <th style="padding:10px 12px; font-size:13px;">Tgl Diajukan</th>
                <th style="padding:10px 12px; font-size:13px;">Status</th>
                <th style="padding:10px 12px; font-size:13px;">Diproses Oleh</th>
                <th style="padding:10px 12px; font-size:13px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $index => $item)
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px 12px; font-size:14px;">{{ $index + 1 }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ $item->nama_lengkap }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ $item->nik }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/y') }}</td>
                    <td style="padding:10px 12px; font-size:14px;">
                        @if ($item->status_keanggotaan == 'Terverifikasi')
                            <span style="background:#1E7A34; color:#fff; padding:3px 10px; border-radius:4px; font-size:12px; font-weight:600;">Disetujui</span>
                        @else
                            <span style="background:#B91C1C; color:#fff; padding:3px 10px; border-radius:4px; font-size:12px; font-weight:600;">Ditolak</span>
                        @endif
                    </td>
                    <td style="padding:10px 12px; font-size:14px;">{{ $item->pengurusPencatat->nama_pengurus ?? '-' }}</td>
                    <td style="padding:10px 12px; font-size:14px;">
                        <a href="{{ route('sekretaris.verifikasi.sudah-diproses', ['detail' => $item->id_anggota, 'cari' => $cari, 'filter' => $filter]) }}"
                           style="color:#B91C1C; font-weight:600; text-decoration:underline;">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding:16px 12px; text-align:center; color:#999; font-size:14px;">
                        Belum ada data yang diproses.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Panel Detail read-only --}}
    @if ($selected)
        @if ($selected->status_keanggotaan == 'Terverifikasi')
            <div style="background:#EAF7EC; border:1px solid #1E7A34; border-radius:8px; padding:20px; max-width:600px;">
                <p style="margin:0 0 12px 0; font-size:15px; font-weight:700; color:#1E7A34;">Pendaftaran Disetujui — ID Anggota Diterbitkan</p>
                <p style="margin:4px 0; font-size:14px;"><strong>ID Anggota Baru :</strong> {{ $selected->id_anggota }}</p>
                <p style="margin:4px 0 12px 0; font-size:14px;"><strong>Status Keanggotaan :</strong> Terverifikasi</p>

                <label style="display:block; font-size:13px; color:#241412; margin-bottom:6px;">Password Default</label>
                <div style="display:flex; gap:8px;">
                    <input type="text" readonly value="{{ $passwordDefault }}" id="passwordDefault"
                           style="flex:1; padding:8px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; background:#fff;">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('passwordDefault').value)"
                            style="background:#fff; color:#241412; border:1px solid #ccc; padding:8px 20px; border-radius:6px; font-weight:600; cursor:pointer;">
                        Salin
                    </button>
                </div>
            </div>
        @else
            <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:8px; padding:20px; max-width:600px;">
                <p style="margin:0 0 12px 0; font-size:15px; font-weight:700; color:#B91C1C;">Pendaftaran Ditolak</p>
                <p style="margin:4px 0; font-size:14px;"><strong>Nama Lengkap :</strong> {{ $selected->nama_lengkap }}</p>
                <p style="margin:4px 0; font-size:14px;"><strong>NIK :</strong> {{ $selected->nik }}</p>
                <p style="margin:4px 0; font-size:14px;"><strong>Catatan Penolakan :</strong> {{ $selected->catatan_penolakan ?? '-' }}</p>
            </div>
        @endif
    @endif

</x-sekretaris-layout>
