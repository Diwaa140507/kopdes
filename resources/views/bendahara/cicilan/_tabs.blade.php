{{-- Simpan di: resources/views/bendahara/cicilan/_tabs.blade.php --}}
<div style="display:flex; gap:8px; margin-bottom:20px;">
    <a href="{{ route('bendahara.cicilan.index') }}"
       style="padding:10px 20px; border-radius:4px; font-weight:bold; font-size:14px; text-decoration:none;
              {{ $tabAktif === 'konfirmasi'
                  ? 'background:#B91C1C; color:#ffffff;'
                  : 'background:#ffffff; color:#B91C1C; border:1px solid #B91C1C;' }}">
        Konfirmasi Pembayaran
    </a>
    <a href="{{ route('bendahara.cicilan.riwayat') }}"
       style="padding:10px 20px; border-radius:4px; font-weight:bold; font-size:14px; text-decoration:none;
              {{ $tabAktif === 'riwayat'
                  ? 'background:#B91C1C; color:#ffffff;'
                  : 'background:#ffffff; color:#B91C1C; border:1px solid #B91C1C;' }}">
        Riwayat Cicilan
    </a>
</div>
