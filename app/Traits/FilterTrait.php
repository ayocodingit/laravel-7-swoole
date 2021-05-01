<?php

namespace App\Traits;

use App\Models\Sampel;
use Carbon\Carbon;

trait FilterTrait
{
    use FilterPcrTrait;

    private $sampel_sent_status = [
        'sample_taken',
        'extraction_sample_extracted',
        'extraction_sample_reextract',
        'extraction_sample_sent',
        'pcr_sample_received',
        'pcr_sample_analyzed',
        'sample_verified',
        'sample_valid',
        'sample_invalid',
        'swab_ulang'
    ];

    public function scopeFilterNomorSampel($query, $operator, $nomorSampel)
    {
        if (preg_match('{^' . Sampel::NUMBER_FORMAT . '$}', $nomorSampel)) {
            $str = $nomorSampel;
            $n = 1;
            $start = $n - strlen($str);
            $str1 = substr($str, $start);
            $str2 = substr($str, 0, $n);
            $query->whereRaw("sampel.nomor_sampel ilike '%$str2%'");
            $query->whereRaw("right(sampel.nomor_sampel,-1)::bigint $operator $str1");
        } else {
            $query->whereNull('sampel.nomor_sampel');
        }
        return $query;
    }

    public function scopeFilterPasien($query, $key, $value)
    {
        $query->when($key == 'nama_pasien', function ($query) use ($value) {
            $query->where('pasien.nama_lengkap', 'ilike', '%' . $value . '%');
        });
        $query->when(in_array($key, ['kota_domisili', 'kota']), function ($query) use ($value) {
            $query->where('pasien.kota_id', $value);
        });
        $query->when($key == 'nik', function ($query) use ($value) {
            $query->where('pasien.nik', $value);
        });
        return $query;
    }

    public function scopeFilterRegister($query, $key, $value)
    {
        $query->filterRegisterDate($key, $value);
        $query->when($key == 'kategori' && $value != 'isian', function ($query) use ($value) {
            $query->where('register.sumber_pasien', 'ilike', '%' . $value . '%');
        });
        $query->when(in_array($key, ['kategori_isian', 'sumber_pasien']), function ($query) use ($value) {
            $query->where('register.sumber_pasien', 'ilike', '%' . $value . '%');
        });
        $query->when($key == 'fasyankes', function ($query) use ($value) {
            $query->where('register.fasyankes_id', $value);
        });
        $query->when($key == 'jenis_registrasi', function ($query) use ($value) {
            $query->where('register.jenis_registrasi', $value);
        });
        $query->when($key == 'nomor_register', function ($query) use ($value) {
            $query->where('register.nomor_register', 'ilike', '%' . $value . '%');
        });
        return $query;
    }

    public function scopeFilterRegisterDate($query, $key, $value)
    {
        $query->when($key == 'start_date', function ($query) use ($value) {
            $query->whereDate('register.created_at', '>=', Carbon::parse($value));
        });
        $query->when($key == 'end_date', function ($query) use ($value) {
            $query->whereDate('register.created_at', '<=', Carbon::parse($value));
        });
        return $query;
    }

    public function scopeFilterSampel($query, $key, $value)
    {
        $query->filterSampelDate($key, $value);
        $query->when(in_array($key, ['no_sampel_start', 'start_nomor_sampel']), function ($query) use ($value) {
            $query->filterNomorSampel(">=", $value);
        });
        $query->when(in_array($key, ['no_sampel_end', 'end_nomor_sampel']), function ($query) use ($value) {
            $query->filterNomorSampel("<=", $value);
        });
        $query->when(in_array($key, ['is_musnah_ekstraksi', 'is_musnah_pcr']), function ($query) use ($key, $value) {
            $query->where("sampel.$key", $value);
        });
        $query->when($key == 'petugas_pengambil', function ($query) use ($value) {
            $query->where("sampel.petugas_pengambilan_sampel", $value);
        });
        $query->when($key == 'status_pemeriksaan', function ($query) use ($value) {
            $query->when(!in_array($value, ['extraction_sample_sent', 'extraction_sent', 'semua']), function ($query) use ($value) {
                $query->where('sampel.sampel_status', $value);
            });
        });
        $query->when($key == 'jenis_sampel_nama', function ($query) use ($value) {
            $isValueInt = filter_var($value, FILTER_VALIDATE_INT);
            $query->when($isValueInt, function ($query) use ($value) {
                $query->where('sampel.jenis_sampel_id', (int) $value);
            }, function ($query) use ($value) {
                $query->where('sampel.jenis_sampel_nama', 'ilike', '%' . $value . '%');
            });
        });
        $query->filterSampelLengkap($key, $value);
        return $query;
    }

    public function scopeFilterSampelLengkap($query, $key, $value)
    {
        $query->when($key == 'sampel_lengkap', function ($query) use ($value) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            $query->when($value, function ($query) {
                $query->whereNotNull('sampel.nomor_register');
            }, function ($query) {
                $query->whereNull('sampel.nomor_register');
            });
        });
        return $query;
    }

    public function scopeFilterPemeriksaanSampel($query, $key, $value)
    {
        $query->when($key == 'kesimpulan_pemeriksaan' && $value != 'semua', function ($query) use ($value) {
            $query->where('pemeriksaansampel.kesimpulan_pemeriksaan', 'ilike', '%' . $value . '%');
        });
        return $query;
    }

    public function scopeFilterFasyankes($query, $key, $value)
    {
        $query->when($key == 'fasyankes_tipe', function ($query) use ($value) {
            $query->where('fasyankes.tipe', $value);
        });
        return $query;
    }

    public function scopeFilterSampelDate($query, $key, $value)
    {
        $query->when($key == 'tanggal_verifikasi_start', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_sample_verified', '>=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_verifikasi_end', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_sample_verified', '<=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_validasi_start', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_sample_valid', '>=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_validasi_end', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_sample_valid', '<=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_taken_start', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_sample_taken', '>=', Carbon::parse($value));
        });
        $query->when($key == 'tanggal_taken_end', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_sample_taken', '<=', Carbon::parse($value));
        });
        $query->when(in_array($key, ['waktu_extraction_sample_sent', 'waktu_sample_taken']), function ($query) use ($value, $key) {
            $query->whereDate("sampel.$key", Carbon::parse($value));
        });
        $query->when($key == 'tanggal_mulai_pemeriksaan', function ($query) use ($value) {
            $query->whereDate('sampel.waktu_pcr_sample_analyzed', Carbon::parse($value));
        });
        return $query;
    }

    public function scopeFilter($query, $params, $chumber = 'sampel')
    {
        foreach ($params as $key => $value) {
            if (!$value) {
                continue;
            }
            $query->when($chumber == 'sampel', function ($query) use ($key, $value) {
                $query->filterChumberSampel($key, $value);
            });
            $query->when($chumber == 'ekstraksi', function ($query) use ($key, $value) {
                $query->filterChumberEkstraksi($key, $value);
            });
            $query->when($chumber == 'sampel_admin', function ($query) use ($key, $value) {
                $query->filterChumberSampelAdmin($key, $value);
            });
            $query->when($chumber == 'pcr', function ($query) use ($key, $value, $params) {
                $filter_inconclusive = isset($params['filter_inconclusive']) ?? null;
                $query->filterChumberPcr($key, $value, $filter_inconclusive);
            });
        }
        return $query;
    }

    public function scopeFilterChumberSampel($query, $key, $value)
    {
        return $query->filterPasien($key, $value)
                        ->filterRegister($key, $value)
                        ->filterSampel($key, $value)
                        ->filterFasyankes($key, $value)
                        ->filterPemeriksaanSampel($key, $value);
    }

    public function scopeFilterChumberEkstraksi($query, $key, $value)
    {
        return $query->filterSampel($key, $value)
                        ->filterPemeriksaanSampel($key, $value)
                        ->filterStatusSampel($key, $value);
    }

    public function scopeFilterStatusSampel($query, $key, $value)
    {
        $sampel_status = $value;
        $query->when($key == 'sampel_status', function ($query) use ($sampel_status) {
            $query->when(in_array($sampel_status, ['sample_taken', 'extraction_sample_extracted']), function ($query) use ($sampel_status) {
                $query->where('sampel.sampel_status', $sampel_status);
            });
            $query->when($sampel_status == 'extraction_sample_reextract', function ($query) {
                $query->whereNotNull('sampel.waktu_extraction_sample_reextract')
                        ->where('sampel.sampel_status', 'extraction_sample_reextract');
            });
            $query->when($sampel_status == 'extraction_sent', function ($query) {
                $query->whereNotNull('waktu_extraction_sample_extracted')
                        ->whereNotNull('waktu_extraction_sample_sent');
            });
        });
        return $query;
    }

    public function scopeFilterChumberSampelAdmin($query, $key, $value)
    {
        return $query->filterSampel($key, $value)
                        ->filterSampelStatusAdmin($key, $value);
    }

    public function scopeFilterSampelStatusAdmin($query, $key, $value)
    {
        $query->when($key == 'sampel_status', function ($query) use ($value) {
            $query->when($value == 'sample_sent', function ($query) {
                $query->whereIn('sampel.sampel_status', $this->sampel_sent_status);
            }, function ($query) use ($value) {
                $query->where('sampel.sampel_status', $value);
            });
        });
        return $query;
    }
}
