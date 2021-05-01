<?php

namespace App\Models;

use App\Enums\StatusPasienEnum;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    const STATUSES = STATUSES;

    protected $appends = ['status_name'];

    protected $table = 'pasien';

    protected $fillable = [
        'nama_lengkap',
        'kewarganegaraan',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'kecamatan',
        'kelurahan',
        'provinsi_id',
        'alamat_lengkap',
        'no_rt',
        'no_rw',
        'suhu',
        'jenis_kelamin',
        'keterangan_lain',
        'usia_tahun',
        'usia_bulan',
        'status',
        //other
        'no_telp',
        'pekerjaan',
        'usia_tahun',
        'sumber_pasien',
        'is_from_migration',
        'usia_bulan',
        'pelaporan_id',
        'pelaporan_id_case'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function getStatusNameAttribute()
    {
        return $this->status ? self::STATUSES[$this->status] : null;
    }

    public function registers()
    {
        return $this->belongsToMany(Register::class, 'pasien_register', 'pasien_id', 'register_id')
            ->using(PasienRegister::class);
    }

    public function setSuhuAttribute($value)
    {
        $this->attributes['suhu'] = parseDecimal($value);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value ? StatusPasienEnum::make($value)->getIndex() : StatusPasienEnum::tanpa_kriteria()->getIndex();
    }
}
