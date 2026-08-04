<x-sekretaris-layout activeMenu="kelola-data-anggota" headerTitle="Data Anggota — Sekretaris">

    <h2 style="font-size:20px; font-weight:bold; color:#241412; margin:0 0 16px;">Data Anggota</h2>

    {{-- Tab utama --}}
    <div style="display:flex; gap:8px; margin-bottom:20px;">
        <a href="{{ route('sekretaris.kelola-data-anggota.riwayat-perubahan') }}"
           style="padding:10px 20px; border:1px solid {{ $tabAktif === 'riwayat-perubahan' ? '#B91C1C' : '#F3B4B4' }}; border-radius:4px; text-decoration:none; font-weight:bold;
                  {{ $tabAktif === 'riwayat-perubahan' ? 'color:#ffffff; background:#B91C1C;' : 'color:#B91C1C; background:#ffffff;' }}">
            Riwayat Perubahan
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.reset-kata-sandi') }}"
           style="padding:10px 20px; border:1px solid #F3B4B4; border-radius:4px; text-decoration:none; color:#B91C1C; font-weight:bold; background:#ffffff;">
            Reset Kata Sandi
        </a>
        <a href="{{ route('sekretaris.kelola-data-anggota.penghapusan') }}"
           style="padding:10px 20px; border:1px solid {{ $tabAktif === 'penghapusan' ? '#B91C1C' : '#F3B4B4' }}; border-radius:4px; text-decoration:none; font-weight:bold;
                  {{ $tabAktif === 'penghapusan' ? 'color:#ffffff; background:#B91C1C;' : 'color:#B91C1C; background:#ffffff;' }}">
            Penghapusan Anggota
        </a>
    </div>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; padding:40px; text-align:center; color:#6B7280;">
        <p style="font-weight:bold; margin:0 0 8px; color:#241412;">{{ $judulTab }}</p>
        <p style="margin:0;">Fitur ini belum dikerjakan — menyusul di tahap berikutnya.</p>
    </div>

</x-sekretaris-layout>
