<x-bendahara-layout activeMenu="pinjaman" headerTitle="Pinjaman — Bendahara">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Pinjaman</h2>

    @include('bendahara.pinjaman._tabs', ['tabAktif' => 'pencairan'])

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Pinjaman Disetujui — Menunggu Pencairan</h3>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Nama</th>
                    <th style="text-align:left; padding:12px 16px;">Nominal Disetujui</th>
                    <th style="text-align:left; padding:12px 16px;">Tgl Disetujui</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antrian as $row)
                    @php $isRowSelected = optional($selected)->id_pinjaman === $row->id_pinjaman; @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : '' }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_anggota }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->nominal_pinjaman, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->updated_at->format('d/m/y') }}</td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.pinjaman.pencairan', ['detail' => $row->id_pinjaman]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Cairkan</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada pinjaman yang menunggu pencairan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selected)
        <h3 style="font-size:16px; color:#241412; margin:0 0 4px 0;">Panel Pencairan — {{ $selected->id_anggota }}</h3>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
            Ditampilkan setelah klik "Cairkan" pada salah satu baris
        </p>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; margin-bottom:16px;">
            <div style="font-size:14px; color:#241412; margin-bottom:16px;">
                Nama Anggota: {{ $selected->anggota->nama_lengkap ?? '-' }} &nbsp;|&nbsp;
                Nominal Disetujui: Rp {{ number_format($selected->nominal_pinjaman, 0, ',', '.') }}
            </div>
            <div style="font-size:14px; color:#241412; margin-bottom:16px;">
                Rekening Tujuan: {{ $selected->rekening_tujuan }}
            </div>

            <form method="POST" action="{{ route('bendahara.pinjaman.cairkan', ['id' => $selected->id_pinjaman]) }}" enctype="multipart/form-data">
                @csrf

                <label style="font-size:14px; font-weight:bold; color:#241412; display:block; margin-bottom:8px;">Bukti Pencairan (wajib diunggah):</label>
                <div style="border:2px dashed #B0B0B0; border-radius:6px; padding:24px; text-align:center; margin-bottom:8px; background:#ffffff;">
                    <input type="file" name="bukti_pencairan" accept=".jpg,.jpeg,.png,.pdf" required style="font-size:14px;">
                    <div style="font-size:12px; color:#6B7280; margin-top:8px;">Format: JPG, PNG, PDF — maks 5MB</div>
                </div>

                <label style="font-size:14px; color:#241412; display:block; margin:16px 0 6px 0;">Catatan Pencairan (opsional):</label>
                <input type="text" name="catatan_pencairan" value="{{ old('catatan_pencairan') }}"
                       style="width:100%; max-width:600px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px; margin-bottom:20px; box-sizing:border-box;">

                <div style="display:flex; gap:12px;">
                    <button type="submit"
                            onclick="return confirm('Konfirmasi pencairan? Status pinjaman akan menjadi Aktif dan jadwal cicilan mulai berjalan.');"
                            style="padding:12px 24px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                        Konfirmasi Pencairan
                    </button>
                    <a href="{{ route('bendahara.pinjaman.pencairan') }}"
                       style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; text-decoration:none; background:#ffffff;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- MODAL M-04: BUKTI PENCAIRAN WAJIB DIUNGGAH --}}
    @error('bukti_pencairan')
        <div id="modal-bukti-wajib" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#C0392B; font-size:28px; font-weight:bold; line-height:1;">&times;</span>
                </div>
                <h3 style="color:#C0392B; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Bukti Pencairan Wajib</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Bukti pencairan wajib diupload.</p>
                <button type="button" onclick="document.getElementById('modal-bukti-wajib').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @elseif ($errors->any())
        {{-- MODAL M-03: DATA TIDAK VALID (validasi lain, bukan bukti_pencairan) --}}
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ $errors->first() }}</p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-01: SUCCESS (pencairan berhasil) --}}
    @if (session('success'))
        <div id="modal-pencairan-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-pencairan-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-bendahara-layout>