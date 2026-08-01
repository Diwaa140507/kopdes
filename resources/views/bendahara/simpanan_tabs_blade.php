@php
    $tabs = [
        'setoran' => ['label' => 'Konfirmasi Setoran', 'route' => 'bendahara.simpanan.setoran'],
        'penarikan' => ['label' => 'Konfirmasi Penarikan', 'route' => 'bendahara.simpanan.penarikan'],
    ];
@endphp

<div style="display:flex; gap:12px; margin-bottom:20px;">
    @foreach ($tabs as $key => $tab)
        @php $isActive = $tabAktif === $key; @endphp
        <a href="{{ route($tab['route']) }}"
           style="padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;
                  {{ $isActive
                        ? 'background:#B91C1C; color:#ffffff; border:1px solid #B91C1C;'
                        : 'background:#ffffff; color:#B91C1C; border:1px solid #F3B4B4;' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
