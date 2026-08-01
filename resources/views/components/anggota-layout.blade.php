@props(['activeMenu' => 'simpanan', 'headerTitle' => 'Simpanan Anggota'])
@php
    $anggota = Auth::guard('web')->user();
    $inisial = collect(explode(' ', $anggota->nama_lengkap))->map(fn($k) => strtoupper(substr($k, 0, 1)))->take(2)->join('');
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
            <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi"
                 style="height:40px; width:auto; background:#ffffff; padding:6px 10px; border-radius:4px;">

            <h1 style="color:#ffffff; font-size:20px; font-weight:bold; margin:0;">{{ $headerTitle }}</h1>
        </div>
        <div style="display:flex; align-items:center; gap:12px; color:#ffffff; font-size:14px;">
            @if ($anggota->dokumen_pendukung)
                <img src="{{ Storage::url($anggota->dokumen_pendukung) }}" alt="Foto Profil"
                     style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid #F3B4B4;">
            @else
                <div style="width:32px; height:32px; border-radius:50%; background:#F3B4B4; color:#7F1D1D; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:12px;">
                    {{ $inisial }}
                </div>
            @endif
            <span>Halo, {{ $anggota->nama_lengkap }}</span>
            &nbsp;|&nbsp;
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="background:none; border:none; color:#ffffff; text-decoration:underline; cursor:pointer; font-size:14px; padding:0;">Keluar</button>
            </form>
        </div>
    </div>
    <div style="display:flex; min-height:calc(100vh - 60px);">
        <div style="width:220px; background:#FDEEEE; padding:20px 0;">
            <p style="text-align:center; font-size:12px; font-weight:bold; color:#7F1D1D; letter-spacing:1px; margin:0 0 12px;">MENU</p>
            @php
                $menuItems = [
                    'simpanan' => ['label' => 'Simpanan', 'route' => 'simpanan.setor'],
                    'pinjaman' => ['label' => 'Pinjaman', 'route' => 'pinjaman.cek-kelayakan'],
                    'cicilan' => ['label' => 'Cicilan', 'route' => 'cicilan.tagihan'],
                    'profil' => ['label' => 'Profil Saya', 'route' => 'profil.detail'],
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

            @if (session('login_success'))
                <div id="modal-login-berhasil" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:1000; padding:16px;">
                    <div style="width:100%; max-width:400px; background:#ffffff; border-radius:10px; padding:32px 24px; text-align:center; font-family: Arial, sans-serif;">
                        <div style="width:64px; height:64px; border-radius:50%; background:#E7F6EA; display:flex; align-items:center; justify-content:center; margin:0 auto 16px auto;">
                            <span style="color:#1E7A34; font-size:28px; font-weight:bold; line-height:1;">&#10003;</span>
                        </div>
                        <h3 style="color:#1E7A34; font-size:20px; font-weight:bold; margin:0 0 8px 0;">Login Berhasil</h3>
                        <p style="color:#4B5563; font-size:14px; margin:0 0 24px 0;">Selamat datang kembali, {{ $anggota->nama_lengkap }}!</p>
                        <button type="button" onclick="document.getElementById('modal-login-berhasil').style.display='none'"
                                style="width:100%; font-weight:bold; color:#ffffff; padding:12px 0; border-radius:6px; background-color:#B91C1C; border:none; cursor:pointer;">
                            OKE
                        </button>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</body>
</html>