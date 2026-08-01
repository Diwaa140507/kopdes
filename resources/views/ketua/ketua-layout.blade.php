@props(['activeMenu' => 'dashboard', 'headerTitle' => 'Dashboard Ketua Koperasi'])

@php
    $pengurus = Auth::guard('pengurus')->user();
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $headerTitle }} — Koperasi Merah Putih</title>
</head>
<body style="margin:0; font-family: Arial, sans-serif; background:#F3F4F6;">

    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 32px; background:linear-gradient(to right, #7F1D1D, #B91C1C);">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="background:#ffffff; color:#B91C1C; font-weight:bold; font-size:12px; padding:10px 14px; border-radius:4px;">
                LOGO
            </div>
            <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">{{ $headerTitle }}</h1>
        </div>
        <div style="color:#ffffff; font-size:14px;">
            Halo, {{ $pengurus->nama_pengurus }} ({{ $pengurus->id_pengurus }})
            &nbsp;|&nbsp;
            <form method="POST" action="{{ route('pengurus.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ffffff; text-decoration:underline; cursor:pointer; font-size:14px; padding:0;">Keluar</button>
            </form>
        </div>
    </div>

    <div style="display:flex; min-height:calc(100vh - 60px);">

        <div style="width:220px; background:#ffffff; border-right:1px solid #E5E7EB; padding:20px 0;">
            @php
                $menuItems = [
                    'dashboard' => ['label' => 'Dashboard', 'route' => 'ketua.dashboard'],
                    'laporan' => ['label' => 'Laporan', 'route' => 'ketua.laporan.pilih'],
                    'pengurus' => ['label' => 'Pengurus', 'route' => 'ketua.pengurus.index'],
                ];
            @endphp

            @foreach ($menuItems as $key => $item)
                @php $isActive = $activeMenu === $key; @endphp
                <a href="{{ route($item['route']) }}"
                   style="display:block; padding:12px 20px; margin:2px 12px; border-radius:4px; text-decoration:none;
                          {{ $isActive ? 'background:#B91C1C; color:#ffffff; font-weight:bold;' : 'background:transparent; color:#241412;' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <div style="flex:1; padding:32px;">
            {{ $slot }}
        </div>

    </div>

</body>
</html>
