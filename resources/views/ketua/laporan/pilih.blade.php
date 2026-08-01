<x-ketua-layout activeMenu="laporan" headerTitle="Laporan — Ketua Koperasi">

    <h2 style="font-size:22px; color:#241412; margin:0 0 4px 0;">Buat Laporan</h2>
    <p style="font-size:13px; color:#6B7280; margin:0 0 20px 0;">Pilih jenis laporan dan periode, lalu klik Tampilkan Laporan</p>

    @if ($errors->any())
        <div style="background:#FADBD8; border:1px solid #A5301F; color:#A5301F; padding:12px 16px; border-radius:4px; margin-bottom:16px; font-size:14px;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('ketua.laporan.tampilkan') }}">
        @csrf

        <h3 style="font-size:14px; font-weight:bold; color:#241412; margin:0 0 4px 0;">Jenis Laporan</h3>
        <p style="font-size:12px; color:#6B7280; margin:0 0 12px 0;">Pilih salah satu jenis laporan yang ingin ditampilkan:</p>

        @php
            $jenisList = [
                'Anggota' => 'Laporan Anggota',
                'Simpanan' => 'Laporan Simpanan',
                'Pinjaman' => 'Laporan Pinjaman',
                'Cicilan' => 'Laporan Cicilan',
                'Pengurus' => 'Laporan Pengurus',
                'Keseluruhan' => 'Laporan Keseluruhan',
            ];
        @endphp

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px;">
            @foreach ($jenisList as $val => $label)
                <label style="display:flex; align-items:center; gap:10px; padding:12px 16px; border:1px solid #D1D5DB; border-radius:4px; cursor:pointer; font-size:14px; color:#241412;">
                    <input type="radio" name="jenis_laporan" value="{{ $val }}" {{ old('jenis_laporan') === $val ? 'checked' : '' }} required>
                    {{ $label }}
                </label>
            @endforeach
        </div>

        <h3 style="font-size:14px; font-weight:bold; color:#241412; margin:0 0 4px 0;">Periode Laporan</h3>
        <p style="font-size:12px; color:#6B7280; margin:0 0 12px 0;">Tentukan rentang waktu laporan yang ingin ditampilkan:</p>

        <div style="display:flex; gap:16px; margin-bottom:24px;">
            <div>
                <label style="display:block; font-size:13px; color:#241412; margin-bottom:6px;">Bulan :</label>
                <select name="periode_bulan" style="padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px; width:160px;">
                    @php
                        $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    @endphp
                    @foreach ($namaBulan as $i => $nama)
                        <option value="{{ $i + 1 }}" {{ $bulanSekarang === $i + 1 ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; font-size:13px; color:#241412; margin-bottom:6px;">Tahun :</label>
                <select name="periode_tahun" style="padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; font-size:14px; width:120px;">
                    @for ($t = $tahunSekarang; $t >= $tahunSekarang - 3; $t--)
                        <option value="{{ $t }}" {{ $tahunSekarang === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-bottom:32px;">
            <button type="submit"
                    style="padding:12px 24px; background:#B91C1C; color:#ffffff; border:none; border-radius:4px; font-weight:bold; font-size:14px; cursor:pointer;">
                Tampilkan Laporan
            </button>
            <button type="reset"
                    style="padding:12px 24px; border:1px solid #D1D5DB; border-radius:4px; font-weight:bold; font-size:14px; color:#241412; background:#ffffff; cursor:pointer;">
                Reset
            </button>
        </div>
    </form>

    <h3 style="font-size:16px; color:#241412; margin:0 0 12px 0;">Riwayat Laporan Dibuat</h3>
    <div style="background:#ffffff; border:1px solid #E5E7EB; border-radius:6px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#B91C1C; color:#ffffff;">
                    <th style="text-align:left; padding:12px 16px;">ID Laporan</th>
                    <th style="text-align:left; padding:12px 16px;">Jenis Laporan</th>
                    <th style="text-align:left; padding:12px 16px;">Periode</th>
                    <th style="text-align:left; padding:12px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $namaBulanSingkat = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp
                @forelse ($riwayat as $row)
                    <tr style="border-bottom:1px solid #F3F4F6;">
                        <td style="padding:12px 16px; color:#241412;">{{ $row->id_laporan }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $row->jenis_laporan }}</td>
                        <td style="padding:12px 16px; color:#241412;">{{ $namaBulanSingkat[$row->periode_bulan] }} {{ $row->periode_tahun }}</td>
                        <td style="padding:12px 16px;">
                            <a href="{{ route('ketua.laporan.lihat', ['id' => $row->id_laporan]) }}"
                               style="color:#B91C1C; text-decoration:underline; font-weight:bold;">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:24px 16px; text-align:center; color:#6B7280;">
                            Belum ada laporan yang pernah dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-ketua-layout>
