<x-sekretaris-layout activeMenu="kelola-data-anggota" headerTitle="Data Anggota — Sekretaris">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Data Anggota</h2>

    @include('sekretaris.kelola-data-anggota._tabs', ['tabAktif' => 'penghapusan'])

    @if (session('error'))
        <div style="background:#FADBD8; border:1px solid #A5301F; color:#A5301F; padding:12px 16px; border-radius:4px; margin-bottom:16px; font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    <h3 style="font-size:16px; color:#241412; margin:0 0 16px 0;">Antrian Pengajuan Penghapusan Akun</h3>

    <div style="display:flex; gap:8px; margin-bottom:16px;">
        <a href="{{ route('sekretaris.kelola-data-anggota.penghapusan') }}"
           style="padding:8px 18px; border-radius:4px; font-weight:bold; font-size:13px; text-decoration:none;
                  {{ $toggleAktif === 'menunggu'
                      ? 'background:#B91C1C; color:#ffffff;'
                      : 'background:#ffffff; color:#B91C1C; border:1px solid #B91C1C;' }}">
            Menunggu
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.penghapusan.sudah-diproses') }}"
           style="padding:8px 18px; border-radius:4px; font-weight:bold; font-size:13px; text-decoration:none;
                  {{ $toggleAktif === 'selesai'
                      ? 'background:#B91C1C; color:#ffffff;'
                      : 'background:#ffffff; color:#B91C1C; border:1px solid #B91C1C;' }}">
            Sudah Diproses
        </a>
    </div>

    @if ($toggleAktif === 'menunggu')
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Alasan Penghapusan</th>
                    <th style="text-align:left; padding:12px 16px;">Tgl Pengajuan</th>
                    <th style="text-align:left; padding:12px 16px;">Status Syarat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antrian as $row)
                    @php
                        $rowMemenuhi = $row->id_anggota === optional($selected)->id_anggota
                            ? optional($syarat)['semua_terpenuhi']
                            : null;
                        $isRowSelected = optional($selected)->id_anggota === $row->id_anggota;
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->nama_lengkap }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->alasan_penghapusan }}</td>
                        <td style="padding:12px 16px; color:#241412;">
                            {{ \Carbon\Carbon::parse($row->tanggal_perubahan_terakhir)->format('d/m/y') }}
                        </td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('sekretaris.kelola-data-anggota.penghapusan', ['detail' => $row->id_anggota]) }}"
                               style="text-decoration:none;">
                                <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:bold;
                                             {{ $isRowSelected && $rowMemenuhi
                                                    ? 'background:#EAF7EC; color:#1E7A34;'
                                                    : ($isRowSelected
                                                        ? 'background:#FADBD8; color:#A5301F;'
                                                        : 'background:#FDEEEE; color:#B91C1C;') }}">
                                    Tinjau
                                </span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada pengajuan penghapusan akun.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Alasan Penghapusan</th>
                    <th style="text-align:left; padding:12px 16px;">Tgl Dihapus</th>
                    <th style="text-align:left; padding:12px 16px;">Diproses Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarSelesai as $row)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->nama_lengkap }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->alasan_penghapusan }}</td>
                        <td style="padding:12px 16px; color:#241412;">
                            {{ \Carbon\Carbon::parse($row->tanggal_perubahan_terakhir)->format('d/m/y') }}
                        </td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->pengurusPencatat->nama_pengurus ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada akun yang dihapus.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if ($selected)
        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px;">
            <h3 style="font-size:16px; color:#241412; margin:0 0 4px 0;">Panel Tinjau — {{ $selected->id_anggota }}</h3>
            <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
                Ditampilkan setelah klik "Tinjau" pada salah satu baris.
            </p>

            <p style="font-size:14px; color:#241412; margin:0 0 4px 0;">
                <strong>Nama Lengkap :</strong> {{ $selected->nama_lengkap }}
                &nbsp;|&nbsp;
                <strong>NIK :</strong> {{ $selected->nik }}
            </p>
            <p style="font-size:14px; color:#241412; margin:0 0 16px 0;">
                <strong>Alasan Penghapusan :</strong> {{ $selected->alasan_penghapusan }}
            </p>

            <p style="font-size:14px; font-weight:bold; color:#241412; margin:0 0 8px 0;">Cek Otomatis 3 Syarat</p>
            <ul style="list-style:none; padding:0; margin:0 0 16px 0; font-size:14px;">
                <li style="margin-bottom:6px; color:{{ $syarat['tidak_ada_pinjaman_aktif'] ? '#1E7A34' : '#A5301F' }};">
                    {{ $syarat['tidak_ada_pinjaman_aktif'] ? '✓' : '✗' }} Tidak ada pinjaman aktif
                </li>
                <li style="margin-bottom:6px; color:{{ $syarat['tidak_ada_saldo_tersisa'] ? '#1E7A34' : '#A5301F' }};">
                    {{ $syarat['tidak_ada_saldo_tersisa'] ? '✓' : '✗' }} Tidak ada simpanan tersisa (saldo Rp 0)
                </li>
                <li style="margin-bottom:6px; color:{{ $syarat['tidak_ada_cicilan_tertunggak'] ? '#1E7A34' : '#A5301F' }};">
                    {{ $syarat['tidak_ada_cicilan_tertunggak'] ? '✓' : '✗' }} Tidak ada cicilan tertunggak
                </li>
            </ul>

            @if ($syarat['semua_terpenuhi'])
                <div style="background:#EAF7EC; color:#1E7A34; padding:10px 14px; border-radius:4px; font-size:13px; font-weight:bold; margin-bottom:16px;">
                    Semua syarat terpenuhi
                </div>
            @else
                <div style="background:#FCE9C7; color:#8A5A00; padding:10px 14px; border-radius:4px; font-size:13px; font-weight:bold; margin-bottom:16px;">
                    Belum bisa dihapus — masih ada syarat yang belum terpenuhi.
                </div>
            @endif

            <div style="display:flex; gap:12px;">
                <form method="POST"
                      action="{{ route('sekretaris.kelola-data-anggota.penghapusan.hapus', ['id' => $selected->id_anggota]) }}"
                      onsubmit="return confirm('Yakin hapus akun {{ $selected->nama_lengkap }} ({{ $selected->id_anggota }})? Tindakan ini permanen.');">
                    @csrf
                    <button type="submit" {{ $syarat['semua_terpenuhi'] ? '' : 'disabled' }}
                            style="padding:12px 24px; border:none; border-radius:4px; font-weight:bold; font-size:14px;
                                   {{ $syarat['semua_terpenuhi']
                                        ? 'background:#B91C1C; color:#ffffff; cursor:pointer;'
                                        : 'background:#D1D5DB; color:#6B7280; cursor:not-allowed;' }}">
                        Hapus Anggota
                    </button>
                </form>
                <a href="{{ route('sekretaris.kelola-data-anggota.penghapusan') }}"
                   style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; text-decoration:none; background:#ffffff;">
                    Batal
                </a>
            </div>
        </div>
    @endif

    {{-- MODAL M-01: SUCCESS (data berhasil dihapus) --}}
    @if (session('success'))
        <div id="modal-hapus-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-hapus-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-sekretaris-layout>