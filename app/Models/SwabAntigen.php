<?php

namespace App\Models;

use App\Enums\StatusAntigenEnum;
use App\Enums\TujuanPemeriksaanEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SwabAntigen extends Model
{
    use SoftDeletes;

    protected $table = 'swab_antigen';

    protected $with = ['provinsi', 'kota', 'kecamatan', 'kelurahan', 'validator'];

    protected $fillable = [
        'nama_pasien',
        'nomor_registrasi',
        'tanggal_lahir',
        'usia_tahun',
        'usia_bulan',
        'jenis_kelamin',
        'no_telp',
        'warganegara',
        'negara_asal',
        'jenis_identitas',
        'no_identitas',
        'kategori',
        'alamat',
        'kode_provinsi',
        'kode_kota_kabupaten',
        'kode_kecamatan',
        'kode_kelurahan',
        'rt',
        'rw',
        'kondisi_pasien',
        'tanggal_gejala',
        'jenis_gejala',
        'tujuan_pemeriksaan',
        'tujuan_pemeriksaan_lainnya',
        'jenis_antigen',
        'no_hasil',
        'tanggal_periksa',
        'hasil_periksa',
        'tanggal_validasi',
        'validator_id',
        'valid_file_id',
        'status_swab_antigen',
        'waktu_sampel_print',
        'counter_print_hasil',
        'status',
    ];

    const NO_HASIL = '[0-9999]{4}[0-1]{1}[1-9]{1}';

    public function scopeFilter($query, $params)
    {
        foreach ($params as $key => $value) {
            if ($value) {
                $query->when($key == 'nama_pasien', function ($query) use ($value) {
                    $query->where('nama_pasien', 'ilike', '%' . $value . '%');
                });
                $query->when($key == 'kategori', function ($query) use ($value) {
                    $query->where('kategori', 'ilike', '%' . $value . '%');
                });
                $query->when($key == 'hasil_periksa', function ($query) use ($value) {
                    $query->where('hasil_periksa', 'ilike', '%' . $value . '%');
                });
                $query->when($key == 'status', function ($query) use ($value) {
                    $query->where('status', $value);
                });
                $query->filterTanggal($key, $value);
            }
        }
        return $query;
    }

    public function scopeFilterTanggal($query, $key, $value)
    {
        $query->when($key == 'tanggal_periksa_start', function ($query) use ($value) {
            $query->whereDate('tanggal_periksa', '>=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_periksa_end', function ($query) use ($value) {
            $query->whereDate('tanggal_periksa', '<=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_validasi_start', function ($query) use ($value) {
            $query->whereDate('tanggal_validasi', '>=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_validasi_end', function ($query) use ($value) {
            $query->whereDate('tanggal_validasi', '<=', Carbon::parse($value));
        });
        return $query;
    }

    public function getIsAllowDeleteAttribute()
    {
        return $this->status_swab_antigen == StatusAntigenEnum::registrasi()->getValue();
    }

    public function scopeOrder($query, $order, $order_direction)
    {
        $orderAllow = [
            'nama_pasien',
            'tanggal_periksa',
            'hasil_periksa',
            'tanggal_lahir',
            'kategori',
            'nomor_registrasi',
            'tanggal_validasi',
            'no_hasil'
        ];
        if (in_array($order, $orderAllow)) {
            $query->orderBy($order, $order_direction);
        }
        return $query;
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'kode_provinsi');
    }

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'kode_kota_kabupaten');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kode_kecamatan');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kode_kelurahan');
    }

    public function validator()
    {
        return $this->belongsTo(Validator::class, 'validator_id');
    }

    public function validFile()
    {
        return $this->belongsTo(File::class, 'valid_file_id');
    }

    public function getTujuanPemeriksaanAttribute()
    {
        return TujuanPemeriksaanEnum::make($this->attributes['tujuan_pemeriksaan'])->getValue();
    }

    public function getStoragePathAttribute()
    {
        return config('app.env') . '/surat_swab_antigen';
    }

    public function setJenisIdentitasAttribute($value)
    {
        $this->attributes['jenis_identitas'] = strtolower($value);
    }

    public function swabAntigenLogs()
    {
        return $this->hasMany(SwabAntigenLog::class, 'swab_antigen_id', 'id');
    }
}
