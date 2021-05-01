<?php

namespace App\Traits;

trait JoinTrait
{
    public function scopeJoinPemeriksaanSampel($query)
    {
        return $query->leftJoin('pemeriksaansampel', 'pemeriksaansampel.sampel_id', 'sampel.id');
    }

    public function scopeJoinRegisterSampel($query)
    {
        return $query->leftJoin('register', 'register.id', 'sampel.register_id')
                        ->whereNull('register.deleted_at');
    }

    public function scopeJoinPasienRegister($query)
    {
        return $query->leftJoin('pasien_register', 'pasien_register.register_id', 'sampel.register_id');
    }

    public function scopeJoinPasien($query)
    {
        return $query->leftJoin('pasien', 'pasien_register.pasien_id', 'pasien.id');
    }

    public function scopeJoinFasyankes($query)
    {
        return $query->leftJoin('fasyankes', 'fasyankes.id', 'register.fasyankes_id');
    }

    public function scopeJoinWilayah($query)
    {
        return $query->leftJoin('kota', 'pasien.kota_id', 'kota.id')
                        ->leftJoin('provinsi', 'pasien.provinsi_id', 'provinsi.id')
                        ->leftJoin('kecamatan', 'pasien.kecamatan_id', 'kecamatan.id')
                        ->leftJoin('kelurahan', 'pasien.kelurahan_id', 'kelurahan.id');
    }

    public function scopeJoinEkstraksi($query)
    {
        return $query->leftJoin('ekstraksi', 'sampel.id', 'ekstraksi.sampel_id');
    }

    public function scopeJoinSampel($query)
    {
        return $query->leftJoin('sampel', 'sampel.register_id', 'register.id')
                        ->whereNull('sampel.deleted_at');
    }

    public function scopeJoinRegisterFromPasienRegister($query)
    {
        return $query->leftJoin('register', 'register.id', 'pasien_register.register_id')
                        ->whereNull('register.deleted_at');
    }

    public function scopeJoinValidator($query)
    {
        return $query->leftJoin('validator', 'validator.id', 'sampel.validator_id');
    }

    public function scopeQuerySampelDefault($query)
    {
        return $query->sampelIsFromMigration()
                        ->joinPemeriksaanSampel()
                        ->joinRegisterSampel()
                        ->joinPasienRegister()
                        ->joinPasien()
                        ->joinStatusSampel()
                        ->joinFasyankes()
                        ->joinEkstraksi()
                        ->joinWilayah();
    }

    public function scopeSampelIsFromMigration($query)
    {
        return $query->where('sampel.is_from_migration', false);
    }

    public function scopeJoinJenisSampel($query)
    {
        return $query->leftJoin('jenis_sampel', 'sampel.jenis_sampel_id', 'jenis_sampel.id');
    }

    public function scopeJoinStatusSampel($query)
    {
        return $query->leftJoin('status_sampel', 'status_sampel.sampel_status', 'sampel.sampel_status');
    }

    public function scopeJoinLabPCR($query)
    {
        return $query->leftJoin('lab_pcr', 'lab_pcr.id', 'sampel.lab_pcr_id');
    }

    public function scopeJoinPengambilanSampel($query)
    {
        return $query->leftJoin('pengambilan_sampel', 'pengambilan_sampel.id', 'sampel.pengambilan_sampel_id');
    }

    public function scopeQueryEkstraksiPcr($query)
    {
        return $query->joinJenisSampel()
                        ->joinStatusSampel()
                        ->joinEkstraksi()
                        ->joinPemeriksaanSampel()
                        ->joinLabPCR()
                        ->sampelIsFromMigration();
    }

    public function scopeSampel($query, $sampel_status)
    {
        $sampel_status = is_array($sampel_status) ? $sampel_status : [$sampel_status];
        return $query->whereIn('sampel.sampel_status', $sampel_status)
                        ->querySampelDefault();
    }
}
