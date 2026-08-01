<x-bendahara-layout activeMenu="simpanan" headerTitle="Simpanan — Bendahara">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">Simpanan</h2>

    @include('bendahara.simpanan._tabs', ['tabAktif' => 'penarikan'])

    @if (session('error'))
        <div style="background:#FADBD8; border:1px solid #A5301F; color:#A5301F; padding:12px 16px; border-radius:4px; margin-bottom:16px; font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
        Keterangan: Simpanan Sukarela bisa ditarik sebagian oleh anggota kapan saja. Simpanan Wajib/Pokok tidak bisa
        ditarik manual — baris "Wajib" di sini hanya muncul otomatis dari pengajuan Penghapusan Akun anggota.
    </p>

    <form method="GET" action="{{ route('bendahara.simpanan.penarikan') }}"
          style="display:flex; gap:8px; align-items:center; margin-bottom:16px;">
        <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari: Nama / ID anggota"
               style="flex:0 0 320px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px;">
        <button type="submit"
                style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
            Cari
        </button>
        @if ($cari)
            <a href="{{ route('bendahara.simpanan.penarikan') }}" style="font-size:13px; color:#6B7280; text-decoration:underline;">Reset</a>
        @endif
    </form>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden; margin-bottom:24px;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">No.</th>
                    <th style="text-align:left; padding:12px 16px;">Nama Anggota</th>
                    <th style="text-align:left; padding:12px 16px;">Jenis</th>
                    <th style="text-align:left; padding:12px 16px;">Jumlah Penarikan</th>
                    <th style="text-align:left; padding:12px 16px;">Saldo Terkait</th>
                    <th style="text-align:left; padding:12px 16px;">Tanggal</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antrian as $i => $row)
                    @php
                        $isRowSelected = optional($selected)->id_simpanan === $row->id_simpanan;
                        $isRowWajib = $row->jenis_simpanan === 'Wajib';
                        $saldoTerkaitRow = $row->saldo_terkait_live;
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $isRowSelected ? 'background:#FDEEEE;' : ($isRowWajib ? 'background:#FCE9C7;' : '') }}">
                        <td style="padding:12px 16px; color:#241412;">{{ $i + 1 }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->anggota->nama_lengkap ?? '-' }}</td>
                        <td style="padding:12px 16px;">
                            @if ($isRowWajib)
                                <span style="display:inline-block; padding:3px 10px; border-radius:4px; font-size:12px; font-weight:bold; background:#FCE9C7; color:#8A5A00;">
                                    Wajib — Penghapusan Akun
                                </span>
                            @else
                                <span style="display:inline-block; padding:3px 10px; border-radius:4px; font-size:12px; font-weight:bold; background:#E0EAFE; color:#1D4ED8;">
                                    Sukarela
                                </span>
                            @endif
                        </td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">Rp {{ number_format($saldoTerkaitRow, 0, ',', '.') }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->created_at->format('d/m/y') }}</td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('bendahara.simpanan.penarikan', ['detail' => $row->id_simpanan]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Tidak ada penarikan yang menunggu konfirmasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selected)
        @php
            $isSelectedWajib = $selected->jenis_simpanan === 'Wajib';
            $saldoSebelum = $selected->saldo_terkait_live;
            $saldoSetelah = $saldoSebelum - $selected->jumlah;
        @endphp

        <h3 style="font-size:16px; color:#241412; margin:0 0 4px 0;">Detail Pengajuan Penarikan</h3>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">
            Ditampilkan setelah Bendahara klik "Detail" — contoh: {{ $selected->anggota->nama_lengkap ?? '-' }}
        </p>

        @if ($isSelectedWajib)
            <div style="background:#FCE9C7; border-radius:6px; padding:12px 16px; margin-bottom:16px; color:#8A5A00; font-size:14px;">
                ⚠ Penarikan ini berasal dari pengajuan <strong>Penghapusan Akun</strong> anggota — sisa Simpanan Wajib
                akan ditransfer penuh. Setelah dikonfirmasi, syarat "Tidak ada simpanan tersisa" di panel Sekretaris
                akan otomatis terpenuhi.
            </div>
        @endif

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; margin-bottom:16px; font-size:14px; color:#241412; line-height:1.9;">
            <div>ID Simpanan : {{ $selected->id_simpanan }}</div>
            <div>ID Anggota : {{ $selected->id_anggota }} | Nama : {{ $selected->anggota->nama_lengkap ?? '-' }}</div>
            <div>Jenis Simpanan : {{ $isSelectedWajib ? 'Wajib (Penghapusan Akun)' : 'Sukarela' }}</div>
            <div>Metode Penarikan : {{ $selected->metode_penarikan ?? '-' }}</div>
            @if ($selected->nama_bank_ewallet || $selected->no_rekening_tujuan)
                <div>Tujuan : {{ $selected->nama_bank_ewallet }} — {{ $selected->no_rekening_tujuan }} a.n. {{ $selected->nama_pemilik_rekening ?? '-' }}</div>
            @endif
            <div>Saldo Sebelum Penarikan : Rp {{ number_format($saldoSebelum, 0, ',', '.') }}</div>
            <div>Jumlah Diajukan : Rp {{ number_format($selected->jumlah, 0, ',', '.') }}</div>
            <div style="font-weight:bold;">Saldo Setelah Penarikan (estimasi) : Rp {{ number_format($saldoSetelah, 0, ',', '.') }}</div>
        </div>

        <form method="POST" action="{{ route('bendahara.simpanan.penarikan.tolak', ['id' => $selected->id_simpanan]) }}" id="form-tolak-penarikan">
            @csrf
            <label style="font-size:14px; color:#241412; display:block; margin-bottom:6px;">Catatan Penolakan (wajib diisi jika ingin menolak):</label>
            <input type="text" name="catatan_penolakan" value="{{ old('catatan_penolakan') }}" placeholder="Isi jika penarikan ditolak..."
                   style="width:100%; max-width:600px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px; margin-bottom:16px; box-sizing:border-box;">
        </form>

        <div style="display:flex; gap:12px;">
            <form method="POST" action="{{ route('bendahara.simpanan.penarikan.konfirmasi', ['id' => $selected->id_simpanan]) }}"
                  onsubmit="return confirm('Konfirmasi penarikan ini? Saldo {{ $isSelectedWajib ? 'Wajib' : 'Sukarela' }} anggota akan otomatis berkurang.');">
                @csrf
                <button type="submit"
                        style="padding:12px 24px; background:#1E7A34; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                    ✓ Konfirmasi
                </button>
            </form>
            <button type="submit" form="form-tolak-penarikan"
                    onclick="return confirm('Tolak penarikan ini? Saldo tidak akan berubah.')"
                    style="padding:12px 24px; background:#ffffff; color:#A5301F; border:1px solid #A5301F; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                ✗ Tolak
            </button>
            <a href="{{ route('bendahara.simpanan.penarikan') }}"
               style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; text-decoration:none; background:#ffffff;">
                Kembali
            </a>
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
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Harap semua kolom diisi.</p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @enderror

    {{-- MODAL M-01: SUCCESS (konfirmasi/tolak berhasil diproses) --}}
    @if (session('success'))
        <div id="modal-penarikan-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-penarikan-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-bendahara-layout>