@props(['tabAktif' => 'tinjau'])

@php
    $tabs = [
        'tinjau' => ['label' => 'Tinjau Pengajuan', 'route' => 'bendahara.pinjaman.tinjau'],
        'pencairan' => ['label' => 'Proses Pencairan', 'route' => 'bendahara.pinjaman.pencairan'],
        'riwayat' => ['label' => 'Riwayat Pinjaman', 'route' => 'bendahara.pinjaman.riwayat'],
    ];
@endphp

<div style="display:flex; gap:8px; margin-bottom:20px;">
    @foreach ($tabs as $key => $tab)
        @php $isActive = $tabAktif === $key; @endphp
        <a href="{{ route($tab['route']) }}"
           style="padding:10px 20px; border-radius:4px; text-decoration:none; font-weight:bold; font-size:14px;
                  {{ $isActive ? 'background:#B91C1C; color:#ffffff;' : 'background:#ffffff; color:#B91C1C; border:1px solid #B91C1C;' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
