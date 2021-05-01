<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\DashboardChart;
use App\Models\DashboardCounter;

class DashboardController extends Controller
{
    public function trackingProgress()
    {
        $data['registration'] = DashboardCounter::where('nama', 'tracking_progress_registration')->first()->total;
        $data['sampel'] = DashboardCounter::where('nama', 'tracking_progress_sampel')->first()->total;
        $data['ekstraksi'] = DashboardCounter::where('nama', 'tracking_progress_ekstraksi')->first()->total;
        $data['rtpcr'] = DashboardCounter::where('nama', 'tracking_progress_rtpcr')->first()->total;
        $data['verifikasi'] = DashboardCounter::where('nama', 'tracking_progress_verifikasi')->first()->total;
        $data['validasi'] = DashboardCounter::where('nama', 'tracking_progress_validasi')->first()->total;

        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function positifNegatif()
    {
        $data['positif'] = DashboardCounter::where('nama', 'pasien_positif')->first()->total;
        $data['negatif'] = DashboardCounter::where('nama', 'pasien_negatif')->first()->total;

        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function pasienDiperiksa()
    {
        $data = [];
        foreach (STATUSES as $key => $value) {
            $data[str_replace(' ', '_', strtolower($value))] = DashboardCounter::where('nama', 'pasien_diperiksa_' . $key)->first()->total;
        }

        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function registrasi()
    {
        $data['mandiri'] = DashboardCounter::where('nama', 'registrasi_mandiri')->first()->total;
        $data['rujukan'] = DashboardCounter::where('nama', 'registrasi_rujukan')->first()->total;
        $data['total'] = DashboardCounter::where('nama', 'registrasi_total')->first()->total;
        $data['jumlah_perhari_mandiri'] = DashboardCounter::where('nama', 'registrasi_jumlah_perhari_mandiri')->first()->total;
        $data['jumlah_perhari_rujukan'] = DashboardCounter::where('nama', 'registrasi_jumlah_perhari_rujukan')->first()->total;
        $data['data_belum_lengkap_mandiri'] = DashboardCounter::where('nama', 'registrasi_data_belum_lengkap_mandiri')->first()->total;
        $data['data_belum_lengkap_rujukan'] = DashboardCounter::where('nama', 'registrasi_data_belum_lengkap_rujukan')->first()->total;
        $data['pemeriksaan_selesai_mandiri'] = DashboardCounter::where('nama', 'registrasi_pemeriksaan_selesai_mandiri')->first()->total;
        $data['pemeriksaan_selesai_rujukan'] = DashboardCounter::where('nama', 'registrasi_pemeriksaan_selesai_rujukan')->first()->total;
        $data['belum_input_rujukan'] = DashboardCounter::where('nama', 'registrasi_belum_input_rujukan')->first()->total;
        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function adminSampel()
    {
        $data['jumlah_perhari_sampel'] = DashboardCounter::where('nama', 'admin_sampel_jumlah_perhari_sampel')->first()->total;
        $data['sampel_register_mandiri'] = DashboardCounter::where('nama', 'admin_sampel_sampel_register_mandiri')->first()->total;
        $data['total_sampel'] = DashboardCounter::where('nama', 'admin_sampel_total_sampel')->first()->total;
        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function ekstraksi()
    {
        $data['jumlah_perhari_ektraksi'] = DashboardCounter::where('nama', 'ekstraksi_jumlah_perhari_ektraksi')->first()->total;
        $data['sampel_baru'] = DashboardCounter::where('nama', 'ekstraksi_sampel_baru')->first()->total;
        $data['ekstraksi'] = DashboardCounter::where('nama', 'ekstraksi_ekstraksi')->first()->total;
        $data['kirim'] = DashboardCounter::where('nama', 'ekstraksi_kirim')->first()->total;
        $data['sampel_invalid'] = DashboardCounter::where('nama', 'ekstraksi_sampel_invalid')->first()->total;
        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function pcr()
    {
        $data['sampel_baru'] = DashboardCounter::where('nama', 'pcr_sampel_baru')->first()->total;
        $data['jumlah_perhari_pcr'] = DashboardCounter::where('nama', 'pcr_jumlah_perhari_pcr')->first()->total;
        $data['hasil_pemeriksaan'] = DashboardCounter::where('nama', 'pcr_hasil_pemeriksaan')->first()->total;
        $data['re_pcr'] = DashboardCounter::where('nama', 'pcr_re_pcr')->first()->total;
        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function verifikasi()
    {
        $data['belum_diverifikasi'] = DashboardCounter::where('nama', 'verifikasi_belum_diverifikasi')->first()->total;
        $data['terverifikasi'] = DashboardCounter::where('nama', 'verifikasi_terverifikasi')->first()->total;
        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }

    public function validasi()
    {
        $data['belum_divalidasi'] = DashboardCounter::where('nama', 'validasi_belum_divalidasi')->first()->total;
        $data['tervalidasi'] = DashboardCounter::where('nama', 'validasi_tervalidasi')->first()->total;
        return response()->json([
            'result' => $data,
            'status' => 200,
        ]);
    }
}
