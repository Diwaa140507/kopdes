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

    <h3 style="font-size:16px; font-weight:bold; color:#241412; margin:0 0 12px;">Antrian Permintaan Reset Kata Sandi</h3>

    {{-- Sub-tab --}}
    <div style="display:flex; gap:8px; margin-bottom:16px;">
        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi') }}"
           style="padding:8px 20px; border:1px solid #B91C1C; border-radius:4px; text-decoration:none; color:#ffffff; font-weight:bold; background:#B91C1C;">
            Menunggu
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi.sudah-diproses') }}"
           style="padding:8px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">
            Sudah Diproses
        </a>
    </div>

    @if (session('success'))
        <div style="background:#EAF7EC; color:#1E7A34; border:1px solid #1E7A34; border-radius:4px; padding:12px 16px; margin-bottom:16px; font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width:100%; border-collapse:collapse; font-size:14px;">
        <thead>
            <tr style="background:#B91C1C; color:#ffffff;">
                <th style="text-align:left; padding:10px 12px;">ID Anggota</th>
                <th style="text-align:left; padding:10px 12px;">Nama</th>
                <th style="text-align:left; padding:10px 12px;">Tgl Pengajuan</th>
                <th style="text-align:left; padding:10px 12px;">Status</th>
                <th style="text-align:left; padding:10px 12px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($antrian as $item)
                <tr style="border-bottom:1px solid #E5E7EB;">
                    <td style="padding:10px 12px;">{{ $item->id_anggota }}</td>
                    <td style="padding:10px 12px;">{{ $item->nama_lengkap }}</td>
                    <td style="padding:10px 12px;">{{ optional($item->tanggal_perubahan_terakhir)->format('d/m/y') ?? '-' }}</td>
                    <td style="padding:10px 12px;">
                        <span style="background:#FCE9C7; color:#8A5A00; padding:4px 10px; border-radius:4px; font-size:12px;">Menunggu</span>
                    </td>
                    <td style="padding:10px 12px;">
                        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi', ['detail' => $item->id_anggota]) }}"
                           style="color:#B91C1C; text-decoration:underline;">Proses</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:16px 12px; text-align:center; color:#6B7280;">Tidak ada permintaan reset kata sandi yang menunggu.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($selected)
        <p style="font-size:13px; color:#6B7280; margin:20px 0 8px;">Panel berikut tampil setelah klik "Proses" pada salah satu baris — {{ $selected->id_anggota }}</p>

        <div style="background:#FDEEEE; border:1px solid #F3B4B4; border-radius:6px; padding:20px; max-width:600px;">
            <p style="font-weight:bold; margin:0 0 8px;">Detail Permintaan — {{ $selected->id_anggota }}</p>
            <p style="margin:4px 0;">Nama : {{ $selected->nama_lengkap }} | NIK : {{ $selected->nik }}</p>
            <p style="margin:4px 0 16px;">Email Terdaftar : {{ $selected->email }}</p>

            <form method="POST" action="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi.konfirmasi', $selected->id_anggota) }}">
                @csrf
                <div style="display:flex; gap:8px; align-items:flex-start;">
                    <div style="flex:1;">
                        <input type="text" name="password_baru" id="password_baru" placeholder="Password baru"
                               style="width:100%; padding:10px 12px; border:1px solid #F3B4B4; border-radius:4px; font-family:Arial, sans-serif; box-sizing:border-box;">
                        @error('password_baru')
                            <p style="color:#B91C1C; font-size:12px; margin:4px 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="button" onclick="generatePassword()"
                            style="padding:10px 18px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">
                        Generate
                    </button>
                </div>

                <div style="display:flex; gap:8px; margin-top:16px;">
                    <button type="submit"
                            onclick="return confirm('Password baru sudah dicatat untuk disampaikan manual ke anggota? Aksi ini tidak bisa dibatalkan.')"
                            style="padding:10px 20px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">
                        Konfirmasi Reset
                    </button>
                    <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi') }}"
                       style="padding:10px 20px; background:#ffffff; color:#241412; border:1px solid #F3B4B4; border-radius:4px; font-weight:bold; text-decoration:none;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    <script>
        function generatePassword() {
            const charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#';
            let pw = '';
            for (let i = 0; i < 10; i++) {
                pw += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            document.getElementById('password_baru').value = pw;
        }
    </script>

</x-sekretaris-layout>
