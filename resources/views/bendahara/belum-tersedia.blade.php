<x-bendahara-layout :activeMenu="$activeMenu ?? 'dashboard'" :headerTitle="$judul">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">{{ $judul }}</h2>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; padding:40px; text-align:center;">
        <h3 style="font-size:16px; color:#241412; margin:0 0 8px 0;">{{ $judul }}</h3>
        <p style="color:#6B7280; font-size:14px; margin:0;">Fitur ini belum dikerjakan — menyusul di tahap berikutnya.</p>
    </div>

</x-bendahara-layout>
