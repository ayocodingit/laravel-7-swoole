<?php

namespace App\Traits;

trait OrderTrait
{
    private $waktu_status_sampel = [
        'waktu_extraction_sample_sent',
        'waktu_sample_taken',
        'waktu_extraction_sample_extracted',
        'waktu_extraction_sample_reextract',
        'waktu_pcr_sample_received',
        'waktu_pcr_sample_analyzed',
        'waktu_waiting_sample'
    ];

    public function scopeOrderPasien($query, $order, $order_direction)
    {
        $query->when($order == 'jenis_kelamin', function ($query) use ($order_direction) {
            $query->orderBy('pasien.jenis_kelamin', $order_direction);
        });
        $query->when(in_array($order, ['pasien_nama', 'nama_lengkap', 'nama_pasien']), function ($query) use ($order_direction) {
            $query->orderBy('pasien.nama_lengkap', $order_direction);
        });
        $query->when($order == 'status', function ($query) use ($order_direction) {
            $query->orderBy('pasien.status', $order_direction);
        });
        return $query;
    }

    public function scopeOrderRegister($query, $order, $order_direction)
    {
        $query->when($order == 'nomor_register', function ($query) use ($order_direction) {
            $query->orderBy('register.nomor_register', $order_direction);
        });
        $query->when($order == 'nama_rs', function ($query) use ($order_direction) {
            $query->orderBy('register.nama_rs', $order_direction);
        });
        $query->when($order == 'sumber_pasien', function ($query) use ($order_direction) {
            $query->orderBy('register.sumber_pasien', $order_direction);
        });
        $query->when($order == 'tgl_input', function ($query) use ($order_direction) {
            $query->orderBy('register.created_at', $order_direction);
        });
        $query->when($order == 'fasyankes', function ($query) use ($order_direction) {
            $query->orderBy('register.fasyankes_pengirim', $order_direction);
        });
        $query->when($order == 'kunjungan_ke', function ($query) use ($order_direction) {
            $query->orderBy('register.kunjungan_ke', $order_direction);
        });
        return $query;
    }

    public function scopeOrderSampel($query, $order, $order_direction)
    {
        $query->orderSampelAdmin($order, $order_direction);
        $query->when(in_array($order, ['nomor_sampel', 'no_sampel']), function ($query) use ($order_direction) {
            $query->orderBy('sampel.nomor_sampel', $order_direction);
        });
        $query->when($order == 'tanggal_divalidasi', function ($query) use ($order_direction) {
            $query->orderBy('sampel.waktu_sample_valid', $order_direction);
        });
        $query->when($order == 'tanggal_diverifikasi', function ($query) use ($order_direction) {
            $query->orderBy('sampel.waktu_sample_verified', $order_direction);
        });
        $query->when($order == 'status_sampel', function ($query) use ($order_direction) {
            $query->orderBy('sampel.sampel_status', $order_direction);
        });
        $query->when($order == 'tanggal_taken', function ($query) use ($order_direction) {
            $query->orderBy('sampel.waktu_sample_taken', $order_direction);
        });
        $query->when($order == 'nomor_register', function ($query) use ($order_direction) {
            $query->orderBy('sampel.nomor_register', $order_direction);
        });
        $query->when($order == 'tgl_input_sampel', function ($query) use ($order_direction) {
            $query->orderBy('sampel.created_at', $order_direction);
        });
        $query->when(in_array($order, $this->waktu_status_sampel), function ($query) use ($order_direction, $order) {
            $query->orderBy("sampel.$order", $order_direction);
        });
        return $query;
    }

    public function scopeOrderPemeriksaanSampel($query, $order, $order_direction)
    {
        $query->when($order == 'kesimpulan_pemeriksaan', function ($query) use ($order_direction) {
            $query->orderBy('pemeriksaansampel.kesimpulan_pemeriksaan', $order_direction);
        });
        return $query;
    }

    public function scopeOrderKota($query, $order, $order_direction)
    {
        $query->when(in_array($order, ['nama_kota', 'kota_nama']), function ($query) use ($order_direction) {
            $query->orderBy('kota.nama', $order_direction);
        });
        return $query;
    }

    public function scopeOrderFasyankes($query, $order, $order_direction)
    {
        $query->when($order == 'fasyankes_nama', function ($query) use ($order_direction) {
            $query->orderBy('fasyankes.nama', $order_direction);
        });
        return $query;
    }

    public function scopeOrderValidator($query, $order, $order_direction)
    {
        $query->when($order == 'validator', function ($query) use ($order_direction) {
            $query->orderBy('validator.nama', $order_direction);
        });
        return $query;
    }

    public function scopeOrder($query, $order, $order_direction, $chumber = 'sampel')
    {
        $query->when($chumber == 'sampel', function ($query) use ($order, $order_direction) {
            $query->orderPasien($order, $order_direction)
                ->orderRegister($order, $order_direction)
                ->orderSampel($order, $order_direction)
                ->orderKota($order, $order_direction)
                ->orderFasyankes($order, $order_direction)
                ->orderPemeriksaanSampel($order, $order_direction);
        });
        $query->when(in_array($chumber, ['ekstraksi', 'pcr']), function ($query) use ($order, $order_direction) {
            $query->orderEkstraksi($order, $order_direction)
                ->orderSampel($order, $order_direction)
                ->orderPemeriksaanSampel($order, $order_direction);
        });
        $query->when($chumber == 'adminSampel', function ($query) use ($order, $order_direction) {
            $query->orderSampel($order, $order_direction);
        });
        return $query;
    }

    public function scopeOrderSampelAdmin($query, $order, $order_direction)
    {
        $query->when($order == 'petugas_pengambil', function ($query) use ($order_direction) {
            $query->orderBy('sampel.petugas_pengambilan_sampel', $order_direction);
        });
        $query->when($order == 'vtm', function ($query) use ($order_direction) {
            $query->orderBy('sampel.jenis_vtm', $order_direction);
        });
        $query->when($order == 'nomor_register', function ($query) use ($order_direction) {
            $query->orderBy('sampel.nomor_register', $order_direction);
        });
        $query->when($order == 'jenis_sampel_nama', function ($query) use ($order_direction) {
            $query->orderBy('sampel.jenis_sampel_nama', $order_direction);
        });
        return $query;
    }

    public function scopeOrderEkstraksi($query, $order, $order_direction)
    {
        $query->when($order == 'catatan_pengiriman', function ($query) use ($order_direction) {
            $query->orderBy('ekstraksi.catatan_pengiriman', $order_direction);
        });
        return $query;
    }
}
