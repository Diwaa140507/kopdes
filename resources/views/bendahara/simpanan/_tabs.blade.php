@php
    $tabs = [
        'setoran' => ['label' => 'Konfirmasi Setoran', 'route' => 'bendahara.simpanan.setoran'],
        'penarikan' => ['label' => 'Konfirmasi Penarikan', 'route' => 'bendahara.simpanan.penarikan'],
        'riwayat' => ['label' => 'Riwayat Simpanan', 'route' => 'bendahara.simpanan.riwayat'],
    ];
@endphp

<div style="display:flex; gap:12px; margin-bottom:20px;">
    @foreach ($tabs as $key => $tab)
        @php $isActive = $tabAktif === $key; @endphp
        <a href="{{ route($tab['route']) }}"
           style="padding:10px 20px; border:1px solid #B91C1C; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;
                  background:{{ $isActive ? '#B91C1C' : '#ffffff' }}; color:{{ $isActive ? '#ffffff' : '#B91C1C' }};">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>