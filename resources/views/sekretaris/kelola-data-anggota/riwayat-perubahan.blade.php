<x-sekretaris-layout activeMenu="kelola-data-anggota" headerTitle="Data Anggota — Sekretaris">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Data Anggota</h2>

    @include('sekretaris.kelola-data-anggota._tabs', ['tabAktif' => 'riwayat-perubahan'])

    <h3 style="font-size:16px; color:#241412; margin:0 0 16px 0;">Riwayat Perubahan Data Anggota</h3>

    <form method="GET" action="{{ route('sekretaris.kelola-data-anggota.riwayat-perubahan') }}"
          style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
        <label style="font-size:14px; color:#241412;">Cari:</label>
        <input type="text" name="cari" value="{{ $cari }}" placeholder="ID / Nama anggota..."
               style="flex:0 0 320px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
        <button type="submit"
                style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
            Cari
        </button>
        @if ($cari)
            <a href="{{ route('sekretaris.kelola-data-anggota.riwayat-perubahan') }}"
               style="font-size:13px; color:#6B7280; text-decoration:underline;">Reset</a>
        @endif
    </form>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Jenis Perubahan</th>
                    <th style="text-align:left; padding:12px 16px;">Data Lama → Baru</th>
                    <th style="text-align:left; padding:12px 16px;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $row)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->nama_lengkap }}</td>
                        <td style="padding:12px 16px; color:#241412;">
                            {{ str_replace('_', ' ', $row->jenis_perubahan) }}
                        </td>
                        <td style="padding:12px 16px; color:#241412;">
                            @if ($row->jenis_perubahan === 'Kata_Sandi')
                                <span style="color:#6B7280;">(Kata sandi diubah — tidak ditampilkan)</span>
                            @else
                                {{ $row->data_lama }} → {{ $row->data_baru }}
                            @endif
                        </td>
                        <td style="padding:12px 16px; color:#241412;">
                            {{ \Carbon\Carbon::parse($row->tanggal_perubahan)->format('d/m/y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            @if ($cari)
                                Tidak ada riwayat perubahan yang cocok dengan kata kunci "{{ $cari }}".
                            @else
                                Belum ada riwayat perubahan data anggota.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-sekretaris-layout>
