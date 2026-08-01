<x-ketua-layout activeMenu="pengurus" headerTitle="Berhentikan Pengurus — Ketua Koperasi">

    <div style="max-width:600px; background:#ffffff; border:1px solid #F3B4B4; border-radius:8px; padding:24px;">
        <h2 style="font-size:20px; color:#241412; margin:0 0 4px 0;">Berhentikan Pengurus</h2>
        <p style="font-size:13px; color:#6B7280; margin:0 0 16px 0;">Tinjau data pengurus sebelum memberhentikan. Tindakan ini tidak dapat dibatalkan.</p>

        <div style="background:#F9FAFB; border:1px solid #E5E7EB; border-radius:6px; padding:16px 20px; margin-bottom:20px;">
            <div style="font-size:14px; font-weight:bold; color:#241412; margin-bottom:12px;">Data Pengurus</div>
            <div style="font-size:14px; color:#241412; margin-bottom:8px;">ID Pengurus &nbsp;: {{ $pengurus->id_pengurus }}</div>
            <div style="font-size:14px; color:#241412; margin-bottom:8px;">Nama &nbsp;: {{ $pengurus->nama_pengurus }}</div>
            <div style="font-size:14px; color:#241412; margin-bottom:8px;">Jabatan &nbsp;: {{ $pengurus->jabatan }}</div>
            <div style="font-size:14px; color:#241412; display:flex; align-items:center; gap:8px;">
                Status Saat Ini &nbsp;:
                <span style="background:#DFF3E4; color:#1E7A34; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Menjabat</span>
            </div>
        </div>

        <div style="background:#FDEEEE; border:1px solid #B91C1C; border-radius:6px; padding:14px 16px; margin-bottom:24px;">
            <div style="color:#B91C1C; font-weight:bold; font-size:13px; margin-bottom:4px;">⚠ Perhatian</div>
            <div style="color:#7F1D1D; font-size:13px;">
                Pengurus yang diberhentikan tidak dapat lagi mengakses sistem. Status akan berubah menjadi Diberhentikan.
            </div>
        </div>

        <form method="POST" action="{{ route('ketua.pengurus.berhentikan', ['id' => $pengurus->id_pengurus]) }}">
            @csrf
            <div style="display:flex; gap:12px;">
                <button type="submit"
                        style="padding:12px 24px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                    Ya, Berhentikan
                </button>
                <a href="{{ route('ketua.pengurus.index') }}"
                   style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; background:#ffffff; text-decoration:none; display:inline-block;">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-ketua-layout>
