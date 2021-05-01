@extends('pdf_templates.template', [
  'logo_watermark' => $logo_watermark,
  'kop_surat' => $kop_surat,
  'validator' => $validator
])
@section('title', 'Cetak Swab Antigen')
@section('content')
<div style="text-align: center;text-transform: uppercase;">
  <h3>
    Hasil Pemeriksaan <br>
    Tes Pro Aktif COVID-19 <br>
    {{ $swab_antigen->nomor_registrasi }}
  </h3>
</div>

<table style="margin-top: 2%" width="50%">
  <tbody>
    <tr>
      <td width="20%"><b>Nama</b></td>
      <td width="2%">:</td>
      <td width="28%">{{ $swab_antigen->nama_pasien }}</td>
    </tr>
    <tr>
      <td width="20%"><b>Umur</b></td>
      <td width="2%">:</td>
      <td width="28%"><span>{{ $umur_pasien }} Tahun</span></td>
    </tr>
    <tr>
      <td width="20%"><b>Alamat</b></td>
      <td width="2%">:</td>
      <td width="28%"><span>{{ $swab_antigen->alamat }}</span></td>
    </tr>
    <tr>
      <td width="20%"><b>Bahan Pemeriksaan</b></td>
      <td width="2%">:</td>
      <td width="28%">{{ $swab_antigen->jenis_antigen }}</td>
    </tr>
  </tbody>
</table>
<table id="tabel-ct-scan" style="width:100%; margin-top: 2%">
  <thead>
    <tr>
      <th width="10%"><b>No</b></th>
      <th width="40%"><b>Jenis Pemeriksaan</b></th>
      <th width="20%"><b>Metoda</b></th>
      <th width="20%"><b>Hasil Pemeriksaan</b></th>
      <th width="20%"><b>Nilai Normal</b></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Rapid Antigen Tes <br> <i>Corona Virus Disease-2019 (Covid-19)</i></td>
      <td><i>Antigen</i></td>
      <td>{{ ucfirst($swab_antigen->hasil_periksa)}}</td>
      <td>Negatif</td>
    </tr>
  </tbody>
</table>
@endsection
