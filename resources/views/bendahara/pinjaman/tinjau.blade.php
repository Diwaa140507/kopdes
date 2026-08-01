<x-bendahara-layout activeMenu="pinjaman" headerTitle="Pinjaman — Dashboard Bendahara">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Pinjaman</h2>

    @include('bendahara.pinjaman._tabs', ['tabAktif' => 'tinjau'])

    @if (session('error'))
        <div style="background:#FADBD8; border:1px solid #A5301F; color:#A5301F; padding:12px 16px; border-radius:4px; margin-bottom:16px; font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Daftar Pengajuan Pinjaman</h3>

    <form method="GET" action="{{ route('bendahara.pinjaman.tinjau') }}"
          style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="ID Anggota / Nama..."
               style="flex:0 0 320px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
        <button type="submit"
                style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
            Cari
        </button>
        @if ($cari)
            <a href="{{ route('bendahara.pinjaman.tinjau') }}" style="font-size:13px; color:#6B7280; text-decoration:underline;">Reset</a>
        @endif
    </form>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Nominal</th>
                    <th style="text-align:left; padding:12px 16px;">Tenor</th>
                    <th style="text-align:left; padding:12px 16px;">Kelayakan</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antrian as $i => $row)
                    @php
                        $isRowSelected = optional($selected)->id_pinjaman === $row->id_pinjaman;
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->nominal_pinjaman, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->tenor_bulan }} bln</td>
                        <td style="padding:12px 16px;">
                            @if ($isRowSelected && $kelayakan)
                                @if ($kelayakan['layak'])
                                    <span style="color:#1E7A34; font-weight:bold;">Layak</span>
                                @else
                                    <span style="color:#A5301F; font-weight:bold;">Tdk Layak</span>
                                @endif
                            @else
                                <span style="color:#6B7280;">—</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.pinjaman.tinjau', ['detail' => $row->id_pinjaman]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Tinjau</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada pengajuan pinjaman yang menunggu persetujuan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selected && $kelayakan)
        <h3 style="font-size:16px; color:#241412; margin:0 0 4px 0;">Detail Pengajuan Pinjaman</h3>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
            Ditampilkan setelah Bendahara klik "Tinjau"
        </p>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; margin-bottom:16px; display:flex; justify-content:space-between; gap:24px;">
            <div style="font-size:14px; color:#241412; line-height:1.8;">
                <div>ID Anggota: {{ $selected->id_anggota }}</div>
                <div>Nama: {{ $selected->anggota->nama_lengkap ?? '-' }}</div>
                <div>Tujuan Pinjaman: {{ $selected->tujuan_pinjaman }}</div>
                <div>Nominal: Rp {{ number_format($selected->nominal_pinjaman, 0, ',', '.') }}</div>
                <div>Tenor: {{ $selected->tenor_bulan }} bulan &nbsp;|&nbsp; Cicilan/Bulan: Rp {{ number_format($selected->cicilan_per_bulan, 0, ',', '.') }}</div>
            </div>
            <div style="font-size:14px; color:#241412; line-height:1.8; text-align:right;">
                <div>Jasa Flat: Rp {{ number_format($selected->jumlah_jasa, 0, ',', '.') }}</div>
                <div>Total Kembali: Rp {{ number_format($selected->total_pengembalian, 0, ',', '.') }}</div>
                <div>Saldo Simpanan: Rp {{ number_format($kelayakan['saldo_simpanan'], 0, ',', '.') }}</div>
                <div>Tunggakan: {{ $kelayakan['ada_tunggakan'] ? 'Ada' : 'Tidak Ada' }}</div>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            @if ($kelayakan['layak'])
                <span style="display:inline-block; padding:10px 20px; background:#1E7A34; color:#ffffff; border-radius:4px; font-weight:bold; font-size:14px;">✓ Layak</span>
            @else
                <span style="display:inline-block; padding:10px 20px; background:#A5301F; color:#ffffff; border-radius:4px; font-weight:bold; font-size:14px;">✗ Tidak Layak</span>
            @endif
        </div>

        <form method="POST" action="{{ route('bendahara.pinjaman.tolak', ['id' => $selected->id_pinjaman]) }}" id="form-tolak-pinjaman">
            @csrf
            <label style="font-size:14px; color:#241412; display:block; margin-bottom:6px;">Catatan Penolakan (wajib diisi jika ingin menolak):</label>
            <input type="text" name="alasan_penolakan" value="{{ old('alasan_penolakan') }}" placeholder="Isi jika pengajuan ditolak..."
                   style="width:100%; max-width:600px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px; margin-bottom:16px; box-sizing:border-box;">
        </form>

        <div style="display:flex; gap:12px;">
            <form method="POST" action="{{ route('bendahara.pinjaman.setujui', ['id' => $selected->id_pinjaman]) }}"
                  onsubmit="return confirm('Setujui pengajuan ini? Pinjaman akan masuk antrian Proses Pencairan.');">
                @csrf
                <button type="submit" {{ $kelayakan['layak'] ? '' : 'disabled' }}
                        style="padding:12px 24px; background:{{ $kelayakan['layak'] ? '#1E7A34' : '#9CA3AF' }}; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:{{ $kelayakan['layak'] ? 'pointer' : 'not-allowed' }};">
                    ✓ Setujui
                </button>
            </form>
            <button type="submit" form="form-tolak-pinjaman"
                    onclick="return confirm('Tolak pengajuan ini?')"
                    style="padding:12px 24px; background:#ffffff; color:#A5301F; border:1px solid #A5301F; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                ✗ Tolak
            </button>
            <a href="{{ route('bendahara.pinjaman.tinjau') }}"
               style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; text-decoration:none; background:#ffffff;">
                Kembali
            </a>
        </div>
    @endif

    {{-- MODAL M-03: CATATAN PENOLAKAN WAJIB DIISI --}}
    @error('alasan_penolakan')
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Harap semua kolom diisi.</p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @enderror

    {{-- MODAL M-01: SUCCESS (setujui/tolak berhasil diproses) --}}
    @if (session('success'))
        <div id="modal-pinjaman-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-pinjaman-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-bendahara-layout>