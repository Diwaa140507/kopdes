<x-ketua-layout activeMenu="{{ $activeMenu ?? 'laporan' }}" headerTitle="{{ $judul ?? 'Segera Hadir' }} — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 20px 0;">{{ $judul ?? 'Segera Hadir' }}</h2>

    <div style="background:#FCE9C7; border:1px solid #8A5A00; color:#8A5A00; padding:20px; border-radius:6px; font-size:14px;">
        Halaman ini masih dalam pengerjaan tahap berikutnya. Kembali ke
        <a href="{{ route('ketua.laporan.pilih') }}" style="color:#8A5A00; font-weight:bold;">Pilih Laporan</a>.
    </div>

</x-ketua-layout>
