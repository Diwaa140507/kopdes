<x-anggota-layout activeMenu="profil" headerTitle="Profil — Dashboard Anggota">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Profil Saya</h2>

    {{-- Tab navigasi --}}
    <div style="display:flex; gap:12px; margin-bottom:20px;">
        <a href="{{ route('profil.detail') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Detail Profil</a>
        <a href="{{ route('profil.ubah-data-diri') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#ffffff; color:#B91C1C; font-weight:bold; font-size:14px;">Ubah Data Diri</a>
        <a href="{{ route('profil.ajukan-penghapusan') }}" style="text-decoration:none; padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px;">Ajukan Penghapusan Akun</a>
    </div>

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 16px;">Ajukan Penghapusan Akun</h3>

    @if ($sedangDiajukan)
        {{-- Sudah pernah mengajukan dan masih dalam proses: tampilkan status saja,
             form TIDAK ditampilkan sama sekali (bukan cuma disabled) supaya
             halaman tidak "nge-spam" form kosong di atas pesan status. --}}
        <div style="background:#FCE9C7; border-radius:6px; padding:20px 24px; max-width:700px;">
            <p style="margin:0 0 8px; color:#8A5A00; font-size:15px; font-weight:bold;">
                ⏳ Pengajuan penghapusan akun Anda sedang diproses.
            </p>
            <p style="margin:0; color:#8A5A00; font-size:14px;">
                Menunggu konfirmasi Bendahara (penarikan sisa simpanan wajib, jika ada) dan persetujuan akhir dari Sekretaris.
                Anda akan menerima notifikasi begitu prosesnya selesai.
            </p>
        </div>
    @else
        @php $bannerHijau = $memenuhiSyarat; @endphp
        <div style="background:{{ $bannerHijau ? '#EAF7EC' : '#FDEEEE' }}; border:1px solid {{ $bannerHijau ? '#1E7A34' : '#F3B4B4' }}; border-radius:6px; padding:16px 20px; margin-bottom:20px; max-width:700px;">
            <p style="margin:0 0 10px; color:{{ $bannerHijau ? '#1E7A34' : '#B91C1C' }}; font-size:14px; font-weight:bold;">
                {{ $bannerHijau ? '✔ Syarat Penghapusan Akun:' : '⚠ Perhatian — Syarat Penghapusan Akun:' }}
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:6px 24px; font-size:13px; color:#241412; margin-bottom:10px;">
                <span>{{ $tidakAdaPinjamanAktif ? '✔' : '✘' }} Tidak ada pinjaman aktif</span>
                <span>{{ $saldoNol ? '✔' : '✘' }} Simpanan sukarela = Rp 0</span>
                <span>{{ $tidakAdaTunggakan ? '✔' : '✘' }} Tidak ada tunggakan cicilan</span>
                <span>• Diproses oleh Sekretaris</span>
            </div>
            <span style="display:inline-block; padding:3px 14px; border-radius:4px; font-size:12px; font-weight:bold; background:{{ $bannerHijau ? '#1E7A34' : '#FCE9C7' }}; color:{{ $bannerHijau ? '#ffffff' : '#8A5A00' }};">
                Status: {{ $bannerHijau ? 'Memenuhi Syarat' : 'Belum Memenuhi Syarat' }}
            </span>
            @if (! $bannerHijau)
                <span style="margin-left:10px; font-size:12px; color:#7F1D1D;">
                    @if (! $tidakAdaPinjamanAktif)
                        (pinjaman aktif Rp {{ number_format($sisaHutang, 0, ',', '.') }} belum lunas)
                    @elseif (! $saldoNol)
                        (saldo simpanan sukarela belum ditarik ke Rp 0)
                    @endif
                </span>
            @endif
        </div>

        @if ($memenuhiSyarat)
            <form method="POST" action="{{ route('profil.ajukan-penghapusan.store') }}" style="max-width:600px;">
                @csrf

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Alasan Penghapusan</label>
                    <textarea name="alasan_penghapusan" rows="4" placeholder="Contoh: pindah domisili ke luar wilayah Desa Merah Putih."
                              style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box; resize:vertical;">{{ old('alasan_penghapusan') }}</textarea>
                </div>

                @if (($saldoWajib ?? 0) > 0)
                    <div style="background:#FCE9C7; border-radius:6px; padding:14px 18px; margin-bottom:16px;">
                        <p style="margin:0 0 10px; color:#8A5A00; font-size:13px; font-weight:bold;">
                            Sisa Simpanan Wajib Rp {{ number_format($saldoWajib, 0, ',', '.') }} akan otomatis ditarik dan dikonfirmasi oleh Bendahara.
                        </p>

                        <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">Metode Penarikan</label>
                        <select name="metode_penarikan_wajib"
                                style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; margin-bottom:12px; box-sizing:border-box;">
                            <option value="">-- Pilih --</option>
                            <option value="Transfer Bank" {{ old('metode_penarikan_wajib') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="E-Wallet" {{ old('metode_penarikan_wajib') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>

                        <label style="display:block; margin-bottom:6px; font-size:14px; color:#241412;">No. Rekening / No. HP Tujuan</label>
                        <input type="text" name="no_rekening_tujuan_wajib" value="{{ old('no_rekening_tujuan_wajib') }}"
                               style="width:100%; padding:10px 12px; border:1px solid #B91C1C; border-radius:6px; font-size:14px; box-sizing:border-box;">
                    </div>
                @endif

                <button type="submit"
                        style="padding:12px 24px; border:1px solid #B91C1C; border-radius:6px; background:#B91C1C; color:#ffffff; font-weight:bold; font-size:14px; cursor:pointer; margin-right:12px;">
                    Ajukan Penghapusan
                </button>
                <a href="{{ route('profil.ajukan-penghapusan') }}" style="display:inline-block; padding:12px 24px; border:1px solid #999999; border-radius:6px; color:#999999; font-weight:bold; font-size:14px; text-decoration:none;">
                    Batal
                </a>
            </form>
        @else
            <p style="font-size:12px; color:#9CA3AF;">* Form pengajuan baru muncul setelah semua syarat di atas terpenuhi.</p>
        @endif
    @endif

    {{-- MODAL M-03: ALASAN PENGHAPUSAN WAJIB DIISI --}}
    @if ($errors->has('alasan_penghapusan') || $errors->has('metode_penarikan_wajib') || $errors->has('no_rekening_tujuan_wajib'))
        <div id="modal-error-validasi" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:420px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:30px; font-weight:bold; line-height:1;">!</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Data Tidak Valid</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">
                    @if ($errors->has('alasan_penghapusan'))
                        Alasan penghapusan wajib diisi.
                    @else
                        Harap semua kolom diisi.
                    @endif
                </p>
                <button type="button" onclick="document.getElementById('modal-error-validasi').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    MENGERTI
                </button>
            </div>
        </div>
    @endif

    {{-- MODAL M-06: INFO (pengajuan terkirim, menunggu Sekretaris) --}}
    @if (session('success'))
        <div id="modal-pengajuan-terkirim" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#FDEEEE; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#B91C1C; font-size:26px; font-weight:bold; font-style:italic; line-height:1;">i</span>
                </div>
                <h3 style="color:#B91C1C; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Menunggu Diproses</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-pengajuan-terkirim').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-anggota-layout>