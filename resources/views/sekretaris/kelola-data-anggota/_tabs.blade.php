@php
    // $tabAktif dikirim dari masing-masing view: 'riwayat-perubahan' | 'reset-kata-sandi' | 'penghapusan'
    $tabs = [
        'riwayat-perubahan' => ['label' => 'Riwayat Perubahan', 'route' => 'sekretaris.kelola-data-anggota.riwayat-perubahan'],
        'reset-kata-sandi' => ['label' => 'Reset Kata Sandi', 'route' => 'sekretaris.kelola-data-anggota.reset-kata-sandi'],
        'penghapusan' => ['label' => 'Penghapusan Anggota', 'route' => 'sekretaris.kelola-data-anggota.penghapusan'],
    ];
@endphp

<div style="display:flex; gap:12px; margin-bottom:20px;">
    @foreach ($tabs as $key => $tab)
        @php $isActive = $tabAktif === $key; @endphp
        <a href="{{ route($tab['route']) }}"
           style="padding:12px 20px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;
                  {{ $isActive
                        ? 'background:#B91C1C; color:#ffffff; border:1px solid #B91C1C;'
                        : 'background:#ffffff; color:#B91C1C; border:1px solid #F3B4B4;' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
