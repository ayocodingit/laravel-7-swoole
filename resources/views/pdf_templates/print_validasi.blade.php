@extends('pdf_templates.template', [
  'logo_watermark' => $logo_watermark,
  'kop_surat' => $kop_surat,
  'validator' => $validator
])
@section('title', 'Cetak Validasi')
@section('content')
<table style="margin-top: 2%" width="100%">
  <tbody>
    <tr>
      <td width="20%"><b>Nama</b></td>
      <td width="2%">:</td>
      <td width="28%"><span><b>{{$pasien->nama_lengkap}}</b></span></td>

      <td style="min-width:500px"></td>

      <td width=20%><b>Tanggal Registrasi</b></td>
      <td width="2%">:</td>
      <td width="28%">{{ $tanggal_registrasi}}</td>
    </tr>
    <tr>
      <td width="20%"><b>Umur</b></td>
      <td width="2%">:</td>
      <td width="28%"><span>{{ $umur_pasien }} Tahun</span></td>

      <td style="min-width:500px"></td>

      <td width=20%><b>Tanggal Periksa</b></td>
      <td width="2%">:</td>
      <td width="28%">{{ $tanggal_periksa }}</td>
    </tr>
    <tr>
      <td width="20%"><b>Jenis Kelamin</b></td>
      <td width="2%">:</td>
      <td width="28%">
        @if ($pasien->jenis_kelamin == 'L')
        <span>Laki-laki</span>
        @endif
        @if ($pasien->jenis_kelamin == 'P')
        <span>Perempuan</span>
        @endif
      </td>

      <td style="min-width:500px"></td>

      <td width=20%><b>Dokter Pengirim</b></td>
      <td width="2%">:</td>
      <td width="28%">{{ $register->nama_dokter ?? '-'}}</td>
    </tr>
    <tr>
      <td width="20%"><b>Alamat</b></td>
      <td width="2%">:</td>
      <td width="28%">
        @if ($pasien->alamat_lengkap)
        <span>{{ $pasien->alamat_lengkap }}</span>
        @endif
        @if ($pasien->no_rt && $pasien->no_rw)
        <span>RT/RW {{ $pasien->no_rt }}/{{ $pasien->no_rw }} </span>
        @endif
        @if ($pasien->provinsi)
        <span> {{ $pasien->provinsi->nama }} </span>
        @endif
        @if ($pasien->kota)
        <span> {{ $pasien->kota->nama }} </span>
        @endif
        @if ($pasien->kecamatan)
        <span>Kecamatan. {{ $pasien->kecamatan }} </span>
        @endif
        @if ($pasien->kelurahan)
        <span>Kelurahan. {{ $pasien->kelurahan }} </span>
        @endif
      </td>
      <td style="min-width:500px"></td>
      <td width="20%"><b>Instansi</b></td>
      <td width="2%">:</td>
      <td width="28%">
        @if ($register->jenis_registrasi === 'mandiri')
        {{ 'Labkes Provinsi Jawa Barat' }}
        @endif

        @if ($register->jenis_registrasi === 'rujukan' && $register->fasyankes)
        {{ $register->fasyankes ? $register->fasyankes->nama : '' }}
        @endif

        @if ($register->jenis_registrasi === 'rujukan' && !$register->fasyankes)
        {{ $register->fasyankes_pengirim }}
        @endif
      </td>
    </tr>
    <tr>
      <td width="20%"></td>
      <td width="2%"></td>
      <td width="28%"></td>

      <td style="min-width:500px"></td>

      <td width="20%"><b>Kategori</b></td>
      <td width="2%">:</td>
      <td width="28%">{{ $register->sumber_pasien }}</td>
    </tr>
  </tbody>
</table>

<table id="tabel-ct-scan" style="width:100%; margin-top: 2%">
  <thead>
    <tr>
      <th width="30%"><b>Pemeriksaan</b></th>
      <th width="30%"><b>No Sampel</b></th>
      <th width="30%"><b>Hasil</b></th>
      <th width="30%"><b>CT Value</b></th>
      <th width="30%"><b>Nilai Rujukan</b></th>
      <th width="30%"><b>Metode</b></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>SARS-CoV-2 (COVID-19)</td>
      <td>{{ $nomor_sampel }}</td>
      <td>{{ ucfirst($hasil_pemeriksaan) }}</td>
      <td>{{ $ct_value }}</td>
      <td>
        <span>negatif CT >= {{ $ct_normal ?? 40 }}</span>
        <span>positif CT < {{ $ct_normal ?? 40}}</span>
      </td>
      <td>RTq-PCR</td>
    </tr>
  </tbody>
</table>
@endsection
