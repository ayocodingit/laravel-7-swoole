<?php

namespace App\Models;

use App\Traits\FilterTrait;
use App\Traits\JoinTrait;
use App\Traits\OrderTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PasienRegister extends Pivot
{
    use JoinTrait;
    use FilterTrait;
    use OrderTrait;

    protected $table = 'pasien_register';

    protected $fillable = [
        'pasien_id',
        'register_id',
        'is_from_migration',
    ];

    public $timestamps = false;

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function scopeSelectDetail($query)
    {
        return $query->select(
            'register.nomor_register',
            'pasien.kewarganegaraan',
            'pasien.nama_lengkap',
            'pasien.tempat_lahir',
            'pasien.tanggal_lahir',
            'pasien.no_hp',
            'pasien.kota_id',
            'pasien.provinsi_id',
            'pasien.kecamatan_id',
            'pasien.kelurahan_id',
            'kota.nama as kota',
            'kecamatan.nama as kecamatan',
            'kelurahan.nama as kelurahan',
            'provinsi.nama as provinsi',
            'pasien.alamat_lengkap',
            'pasien.no_rt',
            'pasien.no_rw',
            'pasien.suhu',
            'pasien.status',
            'sampel.id as sampel_id',
            'sampel.nomor_sampel',
            'pasien.keterangan_lain',
            'pasien.nik',
            'pasien.jenis_kelamin',
            'register.kunjungan_ke',
            'register.tanggal_kunjungan',
            'register.rs_kunjungan',
            'pasien.usia_bulan',
            'pasien.usia_tahun',
            'register.sumber_pasien',
            'fasyankes.nama as fasyankes_nama'
        );
    }

    public function scopeSelectCustom($query)
    {
        return $query->select(
            'register.nomor_register',
            'pasien_register.register_id',
            'pasien_register.pasien_id',
            'pasien.usia_bulan',
            'register.created_at as tgl_input',
            'register.jenis_registrasi',
            'sampel.nomor_sampel',
            'sampel.sampel_status',
            'register.sumber_pasien',
            'pasien.status',
            'pasien.nama_lengkap',
            'pasien.nik',
            'pasien.usia_tahun',
            'pasien.tanggal_lahir',
            'pasien.tempat_lahir',
            'pasien.jenis_kelamin',
            'pasien.alamat_lengkap',
            'kota.nama as nama_kota',
            'provinsi.nama as provinsi',
            'kecamatan.nama as kecamatan',
            'kelurahan.nama as kelurahan',
            'pasien.no_rt',
            'pasien.no_rw',
            'pasien.no_hp',
            'register.no_telp',
            'fasyankes.tipe as fasyankes_pengirim',
            'register.nama_dokter',
            'register.kunjungan_ke',
            'register.created_at',
            'status_sampel.deskripsi',
            'fasyankes.nama as fasyankes_nama'
        );
    }
}
