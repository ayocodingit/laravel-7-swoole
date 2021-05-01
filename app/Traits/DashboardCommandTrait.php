<?php

namespace App\Traits;

use App\Models\DashboardChart;
use App\Models\PasienRegister;
use App\Models\Sampel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait DashboardCommandTrait
{

    private $status_sampel = [
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

    private function getPeriod()
    {
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i');
        return CarbonPeriod::create($weekStartDate, $weekEndDate);
    }

    private function dashboardChart($item, $chart)
    {
        DB::beginTransaction();
        try {
            $data = array_merge($item, [
                'label' => json_encode($chart['label']),
                'data' => json_encode($chart['data']),
            ]);
            DashboardChart::updateOrCreate($item, $data);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
        }
    }

    public function queryPositifNegatif($tipe, $date, $jenisPemeriksaan)
    {
        $models = Sampel::sampel(['sample_verified', 'sample_valid'])
                            ->where('pemeriksaansampel.kesimpulan_pemeriksaan', 'ilike', "%$jenisPemeriksaan%");
        switch ($tipe) {
            case 'Daily':
                $models->whereDate('sampel.waktu_sample_verified', $date);
                break;
            case 'Monthly':
                $models->whereMonth('sampel.waktu_sample_verified', $date);
                break;
        }
        return $models->count('sampel.id');
    }

    public function queryRegistrasi($tipe, $date, $jenisRegistrasi)
    {
        $models = PasienRegister::joinRegisterFromPasienRegister()
                                    ->joinPasien()
                                    ->joinSampel()
                                    ->where('register.jenis_registrasi', $jenisRegistrasi);
        switch ($tipe) {
            case 'Daily':
                $models->whereDate('register.created_at', $date);
                break;
            case 'Monthly':
                $models->whereMonth('register.created_at', $date);
                break;
        }
        return $models->count('pasien.id');
    }

    public function querySampel($tipe, $date)
    {
        $models = Sampel::sampelIsFromMigration()
            ->whereIn('sampel_status', $this->status_sampel);
        switch ($tipe) {
            case 'Daily':
                $models->whereDate('sampel.waktu_sample_taken', $date);
                break;
            case 'Monthly':
                $models->whereMonth('sampel.waktu_sample_taken', $date);
                break;
        }
        return $models->count('id');
    }

    public function queryEkstraksi($tipe, $date)
    {
        $models = Sampel::queryEkstraksiPcr()
                            ->whereNotNull('waktu_extraction_sample_extracted')
                            ->whereNotNull('waktu_extraction_sample_sent');
        switch ($tipe) {
            case 'Daily':
                $models->whereDate('sampel.waktu_extraction_sample_extracted', $date);
                break;
            case 'Monthly':
                $models->whereMonth('sampel.waktu_extraction_sample_extracted', $date);
                break;
        }
        return $models->count('sampel.id');
    }

    public function queryPcr($tipe, $date)
    {
        $models = Sampel::joinEkstraksi()
            ->joinPemeriksaanSampel()
            ->sampelIsFromMigration()
            ->whereIn('sampel.sampel_status', ['pcr_sample_analyzed', 'sample_verified', 'sample_valid', 'inkonklusif'])
            ->whereNotIn('pemeriksaansampel.kesimpulan_pemeriksaan', ['swab_ulang']);
        switch ($tipe) {
            case 'Daily':
                $models->whereDate('sampel.waktu_pcr_sample_analyzed', $date);
                break;
            case 'Monthly':
                $models->whereMonth('sampel.waktu_pcr_sample_analyzed', $date);
                break;
        }
        return $models->count('sampel.id');
    }
}
