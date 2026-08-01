<x-sekretaris-layout activeMenu="verifikasi" headerTitle="Verifikasi Pendaftaran — Sekretaris">
    
    <h1 style="font-size:22px; font-weight:700; color:#241412; margin:0 0 16px 0;">Verifikasi Pendaftaran Anggota</h1>

    {{-- Tab --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('sekretaris.verifikasi') }}"
           style="padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;
                  background:#B91C1C; color:#fff;">
            Menunggu Verifikasi
        </a>
        <a href="{{ route('sekretaris.verifikasi.sudah-diproses') }}"
           style="padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:14px;
                  background:#fff; color:#B91C1C; border:1px solid #B91C1C;">
            Sudah Diproses
        </a>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('sekretaris.verifikasi') }}" style="display:flex; gap:8px; margin-bottom:16px; max-width:500px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Nama / NIK anggota..."
               style="flex:1; padding:8px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
        <button type="submit"
                style="background:#B91C1C; color:#fff; border:none; padding:8px 20px; border-radius:6px; font-weight:600; cursor:pointer;">
            Cari
        </button>
    </form>

    {{-- Tabel daftar calon anggota --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <thead>
            <tr style="background:#B91C1C; color:#fff; text-align:left;">
                <th style="padding:10px 12px; font-size:13px;">No.</th>
                <th style="padding:10px 12px; font-size:13px;">Nama Calon Anggota</th>
                <th style="padding:10px 12px; font-size:13px;">NIK</th>
                <th style="padding:10px 12px; font-size:13px;">Tgl Daftar</th>
                <th style="padding:10px 12px; font-size:13px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarCalonAnggota as $index => $calon)
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px 12px; font-size:14px;">{{ $index + 1 }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ $calon->nama_lengkap }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ $calon->nik }}</td>
                    <td style="padding:10px 12px; font-size:14px;">{{ \Carbon\Carbon::parse($calon->created_at)->format('d/m/y') }}</td>
                    <td style="padding:10px 12px; font-size:14px;">
                        <a href="{{ route('sekretaris.verifikasi', ['detail' => $calon->id_anggota]) }}"
                           style="color:#B91C1C; font-weight:600; text-decoration:underline;">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:16px 12px; text-align:center; color:#999; font-size:14px;">
                        Tidak ada antrian verifikasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Panel Detail --}}
    @if ($selected)
        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:8px; padding:20px; max-width:600px;">
            <p style="margin:4px 0; font-size:14px;"><strong>Nama Lengkap :</strong> {{ $selected->nama_lengkap }}</p>
            <p style="margin:4px 0; font-size:14px;"><strong>NIK :</strong> {{ $selected->nik }}</p>
            <p style="margin:4px 0; font-size:14px;"><strong>Jenis Kelamin :</strong> {{ $selected->jenis_kelamin }}</p>
            <p style="margin:4px 0; font-size:14px;"><strong>Tanggal Lahir :</strong> {{ \Carbon\Carbon::parse($selected->tanggal_lahir)->format('d/m/Y') }}</p>
            <p style="margin:4px 0 12px 0; font-size:14px;"><strong>Alamat Lengkap :</strong> {{ $selected->alamat_lengkap }}</p>

            @if ($nikSudahTerdaftar)
                <div style="background:#B91C1C; color:#fff; display:inline-block; padding:6px 14px; border-radius:6px; font-size:13px; font-weight:600; margin-bottom:12px;">
                    ✕ NIK sudah terdaftar
                </div>
            @else
                <div style="background:#1E7A34; color:#fff; display:inline-block; padding:6px 14px; border-radius:6px; font-size:13px; font-weight:600; margin-bottom:12px;">
                    ✓ NIK belum terdaftar
                </div>
            @endif

            <form method="POST" action="{{ route('sekretaris.verifikasi.tolak', $selected->id_anggota) }}">
                @csrf
                <label style="display:block; font-size:13px; color:#241412; margin-bottom:6px;">Catatan Penolakan (wajib diisi jika ingin menolak):</label>
                <input type="text" name="catatan_penolakan" value="{{ old('catatan_penolakan') }}" placeholder="Isi jika pendaftaran ditolak..."
                       style="width:100%; padding:8px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; margin-bottom:16px;">

                <div style="display:flex; gap:10px;">
                    <button type="submit" formaction="{{ route('sekretaris.verifikasi.setujui', $selected->id_anggota) }}"
                            {{ $nikSudahTerdaftar ? 'disabled' : '' }}
                            style="background:{{ $nikSudahTerdaftar ? '#ccc' : '#1E7A34' }}; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:600; cursor:{{ $nikSudahTerdaftar ? 'not-allowed' : 'pointer' }};">
                        ✓ Setujui
                    </button>
                    <button type="submit"
                            style="background:#fff; color:#B91C1C; border:1px solid #B91C1C; padding:10px 20px; border-radius:6px; font-weight:600; cursor:pointer;">
                        ✕ Tolak
                    </button>
                    <a href="{{ route('sekretaris.verifikasi') }}"
                       style="background:#fff; color:#241412; border:1px solid #ccc; padding:10px 20px; border-radius:6px; font-weight:600; text-decoration:none;">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- MODAL M-03: CATATAN PENOLAKAN WAJIB DIISI --}}
    @error('catatan_penolakan')
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Catatan penolakan wajib diisi.</p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @enderror

    {{-- MODAL M-01: SUCCESS (setujui / tolak berhasil diproses) --}}
    @if (session('success'))
        <div id="modal-verifikasi-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-verifikasi-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-sekretaris-layout>