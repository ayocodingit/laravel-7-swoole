<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Register extends Model
{
    use SoftDeletes;

    const RUMAH_SAKIT = 'rumah sakit';
    const DINKES = 'dinkes';
    const PUSKESMAS = 'puskesmas';
    const PENDAFTARAN = 'pendaftaran';

    public static $instansi_pengirim_types = [self::RUMAH_SAKIT, self::DINKES, self::PUSKESMAS];

    protected $table = 'register';

    protected $fillable = [
        'nomor_register',
        'register_uuid',
        'creator_user_id',
        'jenis_registrasi',
        'tanggal_kunjungan',
        'kunjungan_ke',
        'rs_kunjungan',
        'sumber_pasien',
        //other
        'hasil_rdt',
        'fasyankes_id',
        'nomor_rekam_medis',
        'nama_dokter',
        'no_telp',
        'dinkes_pengirim',
        'other_dinas_pengirim',
        'nama_rs',
        'other_nama_rs',
        'fasyankes_pengirim',
        'is_from_migration',
        'registration_code',
        'tes_masif_id'
        // 'keterangan_lain'
    ];

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class);
    }

    public function pasienRegister()
    {
        return $this->belongsTo(pasienRegister::class, 'id', 'register_id');
    }

    public function pasiens()
    {
        return $this->belongsToMany(Pasien::class, 'pasien_register', 'register_id', 'pasien_id')
            ->using(PasienRegister::class);
    }

    public function logs()
    {
        return $this->hasMany(RegisterLog::class, 'register_id', 'id');
    }

    public function tes_masif()
    {
        return $this->belongsTo(TesMasif::class);
    }

    public function sampel()
    {
        return $this->hasOne(Sampel::class);
    }

    public static function boot()
    {
        parent::boot();
        self::deleting(function ($register) {
            if ($register->sampel) {
                $register->sampel()->delete();
            }
        });
    }
}
