<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\DashboardChart;
use Illuminate\Http\Request;

class DashboardChartController extends Controller
{
    public function registrasi(Request $request)
    {
        $tipe = $request->input('tipe', 'Daily');
        $jenisRegistrasi = $request->input('jenis', 'mandiri');

        $models = DashboardChart::where('nama', 'registrasi')
                                ->where('tipe', $tipe)
                                ->where('where', $jenisRegistrasi)
                                ->first();

        return response()->json([
            'label' => json_decode($models->label),
            'value' => json_decode($models->data),
        ]);
    }

    public function sampel(Request $request)
    {
        $tipe = $request->input('tipe', 'Daily');

        $models = DashboardChart::where('nama', 'sampel')
                                ->where('tipe', $tipe)
                                ->first();

        return response()->json([
            'label' => json_decode($models->label),
            'value' => json_decode($models->data),
        ]);
    }

    public function ekstraksi(Request $request)
    {
        $tipe = $request->input('tipe', 'Daily');

        $models = DashboardChart::where('nama', 'ekstraksi')
                                ->where('tipe', $tipe)
                                ->first();

        return response()->json([
            'label' => json_decode($models->label),
            'value' => json_decode($models->data),
        ]);
    }

    public function pcr(Request $request)
    {
        $tipe = $request->input('tipe', 'Daily');

        $models = DashboardChart::where('nama', 'pcr')
                                ->where('tipe', $tipe)
                                ->first();

        return response()->json([
            'label' => json_decode($models->label),
            'value' => json_decode($models->data),
        ]);
    }

    public function positifNegatif(Request $request)
    {
        $tipe = $request->input('tipe', 'Daily');
        $hasilPemeriksaan = $request->input('jenis', 'positif');

        $models = DashboardChart::where('nama', 'positif_negatif')
                                ->where('tipe', $tipe)
                                ->where('where', $hasilPemeriksaan)
                                ->first();
        return response()->json([
            'label' => json_decode($models->label),
            'value' => json_decode($models->data),
        ]);
    }
}
