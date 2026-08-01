<x-ketua-layout activeMenu="pengurus" headerTitle="Pengurus — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Kelola Pengurus</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Daftar seluruh pengurus koperasi. Hanya Ketua Koperasi yang dapat mengelola data pengurus.</p>

    @if (session('error'))
        <div style="background:#FADBD8; border:1px solid #A5301F; color:#A5301F; padding:12px 16px; border-radius:4px; margin-bottom:16px; font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($jabatanKosong->isNotEmpty())
        <div style="background:#FCE9C7; border:1px solid #E0A63A; color:#8A5A00; padding:12px 16px; border-radius:4px; margin-bottom:16px; font-size:14px;">
            ⚠ Jabatan {{ $jabatanKosong->implode(' dan ') }} sedang kosong sementara.
            Ketua Koperasi (KET-001) mengambil alih tanggung jawab dan pencatatan transaksi jabatan tersebut
            hingga pengurus baru terdaftar.
        </div>
    @endif

    <a href="{{ route('ketua.pengurus.create') }}"
       style="display:inline-block; padding:12px 20px; background:#B91C1C; color:#ffffff; border-radius:4px; font-weight:bold; font-size:14px; text-decoration:none; margin-bottom:20px;">
        + Tambah Pengurus
    </a>

    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Pengurus</th>
                    <th style="text-align:left; padding:12px 16px;">Nama Lengkap</th>
                    <th style="text-align:left; padding:12px 16px;">Jabatan</th>
                    <th style="text-align:left; padding:12px 16px;">Status</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPengurus as $row)
                    @php $diberhentikan = $row->status === 'Diberhentikan'; @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; {{ $diberhentikan ? 'background:#F4F4F4; color:#8A8A8A;' : '' }}">
                        <td style="padding:12px 16px; color:{{ $diberhentikan ? '#8A8A8A' : '#241412' }};">{{ $row->id_pengurus }}</td>
                        <td style="padding:12px 16px; color:{{ $diberhentikan ? '#8A8A8A' : '#241412' }};">{{ $row->nama_pengurus }}</td>
                        <td style="padding:12px 16px; color:{{ $diberhentikan ? '#8A8A8A' : '#241412' }};">{{ $row->jabatan }}</td>
                        <td style="padding:12px 16px;">
                            @if ($diberhentikan)
                                <span style="background:#F4F4F4; color:#8A8A8A; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Diberhentikan</span>
                            @else
                                <span style="background:#DFF3E4; color:#1E7A34; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Menjabat</span>
                            @endif
                        </td>
                        <td style="padding:12px 16px;">
                            @if ($diberhentikan)
                                <span style="color:#8A8A8A;">— (riwayat)</span>
                            @elseif ($row->jabatan === 'Ketua Koperasi')
                                <span style="color:#8A8A8A;">— (tetap)</span>
                            @else
                                <a href="{{ route('ketua.pengurus.berhentikan.confirm', ['id' => $row->id_pengurus]) }}"
                                   style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Berhentikan</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada data pengurus.
                        </td>
                    </tr>
                @endforelse

                @foreach ($jabatanKosong as $jabatan)
                    <tr style="border-bottom:1px solid #F3F4F6; background:#FFF8EC;">
                        <td style="padding:12px 16px; color:#8A5A00; font-style:italic;">—</td>
                        <td style="padding:12px 16px; color:#8A5A00; font-style:italic;">Diambil alih Ketua Koperasi (KET-001)</td>
                        <td style="padding:12px 16px; color:#8A5A00;">{{ $jabatan }}</td>
                        <td style="padding:12px 16px;">
                            <span style="background:#FCE9C7; color:#8A5A00; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">Kosong Sementara</span>
                        </td>
                        <td style="padding:12px 16px; color:#8A8A8A;">—</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#6B7280; margin-top:16px;">
        Catatan: Ketua Koperasi (KET-001) tidak dapat diberhentikan. Pengurus dengan status Diberhentikan ditampilkan sebagai riwayat historis dan tidak memiliki opsi aksi.
    </p>

    {{-- MODAL M-01: SUCCESS (pengurus berhasil ditambahkan / diberhentikan) --}}
    @if (session('success'))
        <div id="modal-pengurus-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
            <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                    <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                </div>
                <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Berhasil</h3>
                <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">{{ session('success') }}</p>
                <button type="button" onclick="document.getElementById('modal-pengurus-berhasil').style.display='none'"
                        style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                    OKE
                </button>
            </div>
        </div>
    @endif

</x-ketua-layout>