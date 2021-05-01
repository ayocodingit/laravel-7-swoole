<?php

namespace App\Traits;

trait SelectTrait
{
    public function scopeSelectSampel($query)
    {
        return $query->select(
            'sampel.id as id',
            'nomor_sampel',
            'nama_lengkap',
            'nik',
            'jenis_kelamin',
            'register.sumber_pasien as sumber_pasien',
            'jenis_registrasi',
            'kota.nama as nama_kota',
            'hasil_deteksi',
            'kesimpulan_pemeriksaan',
            'tanggal_lahir',
            'usia_tahun',
            'register.nomor_register as nomor_register',
            'waktu_sample_valid',
            'status',
            'petugas_pengambilan_sampel',
            'sampel.sampel_status',
            'waktu_sample_verified',
            'tempat_lahir',
            'provinsi.nama as provinsi',
            'kecamatan.nama as kecamatan',
            'kelurahan.nama as kelurahan',
            'pasien.no_rt',
            'pasien.no_rw',
            'pasien.no_hp',
            'register.created_at',
            'fasyankes.tipe as fasyankes_pengirim',
            'jenis_sampel_nama',
            'waktu_sample_taken',
            'tanggal_input_hasil',
            'tanggal_mulai_ekstraksi',
            'kunjungan_ke',
            'validator_id',
            'counter_print_hasil',
            'waktu_sample_print',
            'fasyankes.nama as fasyankes_nama',
            'status_sampel.deskripsi',
        );
    }

    public function scopeSelectEkstraksi($query)
    {
        return $query->select(
            'sampel.id',
            'sampel.nomor_sampel',
            'sampel.sampel_status',
            'sampel.nomor_register',
            'sampel.jenis_sampel_nama',
            'sampel.waktu_sample_taken',
            'sampel.waktu_extraction_sample_extracted',
            'status_sampel.deskripsi',
            'sampel.petugas_pengambilan_sampel',
            'ekstraksi.catatan_pengiriman',
            'ekstraksi.catatan_penerimaan',
            'ekstraksi.operator_ekstraksi',
            'sampel.waktu_extraction_sample_sent',
            'sampel.waktu_extraction_sample_reextract',
            'sampel.is_musnah_ekstraksi',
            'pemeriksaansampel.kesimpulan_pemeriksaan',
            'lab_pcr.nama as lab_pcr_nama'
        );
    }

    public function scopeSelectChumberDetailSampel($query)
    {
        return $query->select(
            'sampel.id as sampel_id',
            'sampel.id as pengambilan_sampel_id',
            'register_id',
            'sampel.sampel_status',
            'jenis_registrasi',
            'pengambilan_sampel.sumber_sampel as sumber_sampel',
            'pengambilan_sampel.penerima_sampel as penerima_sampel',
            'pengambilan_sampel.catatan',
            'petugas_pengambilan_sampel as petugas_pengambil',
            'jam_pengambilan_sampel as pukulsampel',
            'jenis_sampel_id as sam_jenis_sampel',
            'jenis_sampel_nama as sam_namadiluarjenis',
            'tanggal_pengambilan_sampel as tanggalsampel',
            'sampel.jenis_vtm',
            'nomor_sampel as nomorsampel'
        );
    }

    public function scopeSelectSampelAdmin($query)
    {
        return $query->select(
            'sampel.id',
            'sampel.nomor_sampel',
            'sampel.nomor_register',
            'sampel.register_id',
            'sampel.jenis_sampel_nama',
            'sampel.waktu_waiting_sample',
            'sampel.created_at as tgl_input_sampel',
            'sampel.waktu_sample_taken',
            'sampel.petugas_pengambilan_sampel',
            'sampel.jenis_vtm as nama_vtm',
        );
    }

    public function scopeSelectPcr($query)
    {
        return $query->select(
            'sampel.sampel_status',
            'sampel.id',
            'sampel.nomor_sampel',
            'sampel.nomor_register',
            'sampel.jenis_sampel_nama',
            'sampel.waktu_extraction_sample_sent',
            'ekstraksi.operator_ekstraksi',
            'sampel.waktu_pcr_sample_analyzed',
            'ekstraksi.catatan_pengiriman',
            'ekstraksi.catatan_penerimaan',
            'sampel.waktu_pcr_sample_received',
            'sampel.waktu_extraction_sample_sent',
            'pemeriksaansampel.kesimpulan_pemeriksaan',
            'status_sampel.deskripsi',
            'sampel.is_musnah_pcr'
        );
    }
}
