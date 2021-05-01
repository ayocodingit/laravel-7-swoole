<?php

namespace App\Models;

use App\Enums\JenisRegistrasiEnum;
use App\Enums\StatusPasienEnum;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TesMasif extends Model
{
    protected $table = 'tes_masif';

    protected $fillable = [
        "registration_code",
        "jenis_registrasi",
        "nama_pasien",
        "nomor_sampel",
        "fasyankes_id",
        "kewarganeraan",
        "kategori",
        "kriteria",
        "nik",
        "tempat_lahir",
        "tanggal_lahir",
        "jenis_kelamin",
        "provinsi_id",
        "kota_id",
        "kecamatan_id",
        "kelurahan_id",
        "alamat",
        "rt",
        "rw",
        "no_hp",
        "suhu",
        "keterangan",
        "hasil_rdt",
        "usia_tahun",
        "usia_bulan",
        "dokter",
        "telp_fasyankes",
        "kunjungan",
        "tanggal_kunjungan",
        "rs_kunjungan",
        "available"
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function fasyankes()
    {
        return $this->belongsTo(Fasyankes::class);
    }

    public function register()
    {
        return $this->hasMany(Register::class);
    }

    public function setProvinsiIdAttribute($value)
    {
        $this->attributes['provinsi_id'] = getConvertCodeDagri($value);
    }

    public function setKotaIdAttribute($value)
    {
        $this->attributes['kota_id'] = getConvertCodeDagri($value);
    }

    public function setKecamatanIdAttribute($value)
    {
        $this->attributes['kecamatan_id'] = Kecamatan::where('nama', strtoupper($value))->value('id');
    }

    public function setKelurahanIdAttribute($value)
    {
        $this->attributes['kelurahan_id'] = Kelurahan::where('nama', strtoupper($value))->value('id');
    }

    public function setKriteriaAttribute($value)
    {
        $this->attributes['kriteria'] = StatusPasienEnum::make($value)->getIndex();
    }

    public function scopeFilter($query, $params)
    {
        foreach ($params as $key => $value) {
            if ($value) {
                $query->when($key == 'nama_nik', function ($query) use ($value) {
                    $query->where(function ($query) use ($value) {
                        $query->where('nama_pasien', 'ilike', '%' . $value . '%')
                                ->orWhere('nik', 'ilike', '%' . $value . '%');
                    });
                });
                $query->when($key == 'tanggal_kunjungan_mulai', function ($query) use ($value) {
                    $query->whereDate('tanggal_kunjungan', '>=', Carbon::parse($value));
                });
                $query->when($key == 'tanggal_kunjungan_akhir', function ($query) use ($value) {
                    $query->whereDate('tanggal_kunjungan', '<=', Carbon::parse($value));
                });
                $query->when($key == 'kategori', function ($query) use ($value) {
                    $query->where('kategori', 'ilike', '%' . $value . '%');
                });
            }
        }
        return $query;
    }

    public function scopeJenisRegistrasiMandiri($query)
    {
        return $query->where('jenis_registrasi', JenisRegistrasiEnum::mandiri());
    }

    public function scopeJenisRegistrasiRujukan($query)
    {
        return $query->where('jenis_registrasi', JenisRegistrasiEnum::rujukan());
    }

    public function scopeOrder($query, $order, $order_direction)
    {
        $query->when($order == 'nama_pasien', function ($query) use ($order_direction) {
            $query->orderBy('nama_pasien', $order_direction);
        });
        $query->when($order == 'sumber_pasien', function ($query) use ($order_direction) {
            $query->orderBy('kategori', $order_direction);
        });
        $query->when($order == 'tgl_input', function ($query) use ($order_direction) {
            $query->orderBy('tanggal_kunjungan', $order_direction);
        });
        $query->when($order == 'nama_kota', function ($query) use ($order_direction) {
            $query->orderBy('kota.nama', $order_direction);
        });
        $query->when($order == 'no_sampel', function ($query) use ($order_direction) {
             $query->orderBy('nomor_sampel', $order_direction);
        });
        return $query;
    }

    public function scopeJoinKota($query)
    {
        return $query->leftJoin('kota', 'tes_masif.kota_id', 'kota.id');
    }

    public function scopeStatus($query, $status)
    {
        $query->when($status == 'taken', function ($query) use ($status) {
            $query->where('available', false);
        }, function ($query) {
            $query->where('available', true);
        });
        return $query;
    }
}
