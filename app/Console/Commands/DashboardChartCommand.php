<?php

namespace App\Console\Commands;

use App\Traits\DashboardCommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DashboardChartCommand extends Command
{
    use DashboardCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dashboard_chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Dashboard Chart untuk counter dashboard chart';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    const REGISTER = [
        [
            'nama' => 'registrasi',
            'where' => 'mandiri',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'registrasi',
            'where' => 'rujukan',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'registrasi',
            'where' => 'mandiri',
            'tipe' => 'Monthly',
        ],
        [
            'nama' => 'registrasi',
            'where' => 'rujukan',
            'tipe' => 'Monthly',
        ],
    ];
    const SAMPEL = [
        [
            'nama' => 'sampel',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'sampel',
            'tipe' => 'Monthly',
        ],
    ];
    const EKSTRAKSI = [
        [
            'nama' => 'ekstraksi',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'ekstraksi',
            'tipe' => 'Monthly',
        ],
    ];
    const PCR = [
        [
            'nama' => 'pcr',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'pcr',
            'tipe' => 'Monthly',
        ],
    ];
    const POSITIF_NEGATIF = [
        [
            'nama' => 'positif_negatif',
            'where' => 'positif',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'positif_negatif',
            'where' => 'positif',
            'tipe' => 'Monthly',
        ],
        [
            'nama' => 'positif_negatif',
            'where' => 'negatif',
            'tipe' => 'Daily',
        ],
        [
            'nama' => 'positif_negatif',
            'where' => 'negatif',
            'tipe' => 'Monthly',
        ],
    ];

    const SELECT_REGISTER = "SELECT DISTINCT(TO_CHAR(created_at,'YYYY MON')) as label,TO_CHAR(created_at,'MM') as month FROM register ORDER BY month";
    const SELECT_SAMPEL = "SELECT DISTINCT(TO_CHAR(created_at,'YYYY MON')) as label,TO_CHAR(created_at,'MM') as month FROM sampel ORDER BY month";
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (self::REGISTER as $item) {
            $chart = $this->chartRegistrasi($item['tipe'], $item['where']);
            $this->dashboardChart($item, $chart);
        }
        foreach (self::SAMPEL as $item) {
            $chart = $this->chartSampel($item['tipe']);
            $this->dashboardChart($item, $chart);
        }
        foreach (self::EKSTRAKSI as $item) {
            $chart = $this->chartEkstraksi($item['tipe']);
            $this->dashboardChart($item, $chart);
        }
        foreach (self::PCR as $item) {
            $chart = $this->chartPcr($item['tipe']);
            $this->dashboardChart($item, $chart);
        }
        foreach (self::POSITIF_NEGATIF as $item) {
            $chart = $this->chartPositifNegatif($item['tipe'], $item['where']);
            $this->dashboardChart($item, $chart);
        }
    }

    public function chartTemplate($tipe, $namaChart, $select, $where = null)
    {
        $label = [];
        $value = [];
        switch ($tipe) {
            case "Daily":
                foreach ($this->getPeriod() as $date) {
                    array_push($label, $date->format('D'));
                    array_push($value, $this->dailyQuery($namaChart, $date, $where));
                }
                break;
            case "Monthly":
                $periode = DB::select($select);
                foreach ($periode as $row) {
                    array_push($label, $row->label);
                    array_push($value, $this->monthlyQuery($namaChart, $row, $where));
                }
                break;
        }
        return ['label' => $label, 'data' => $value];
    }

    private function dailyQuery($namaChart, $date, $where = null)
    {
        switch ($namaChart) {
            case 'register':
                $value = $this->queryRegistrasi('Daily', $date->format('Y-m-d'), $where);
                break;
            case 'sampel':
                $value = $this->querySampel('Daily', $date->format('Y-m-d'));
                break;
            case 'ekstraksi':
                $value = $this->queryEkstraksi('Daily', $date->format('Y-m-d'));
                break;
            case 'pcr':
                $value = $this->queryPcr('Daily', $date->format('Y-m-d'));
                break;
            case 'positif_negatif':
                $value = $this->queryPositifNegatif('Daily', $date->format('Y-m-d'), $where);
                break;
        }
        return $value;
    }

    private function monthlyQuery($namaChart, $row, $where = null)
    {
        switch ($namaChart) {
            case 'register':
                $value = $this->queryRegistrasi('Monthly', $row->month, $where);
                break;
            case 'sampel':
                $value = $this->querySampel('Monthly', $row->month);
                break;
            case 'ekstraksi':
                $value = $this->queryEkstraksi('Monthly', $row->month);
                break;
            case 'pcr':
                $value = $this->queryPcr('Monthly', $row->month);
                break;
            case 'positif_negatif':
                $value = $this->queryPositifNegatif('Monthly', $row->month, $where);
                break;
        }
        return $value;
    }

    public function chartRegistrasi($tipe, $jenisRegistrasi)
    {
        return $this->chartTemplate($tipe, 'register', self::SELECT_REGISTER, $jenisRegistrasi);
    }

    public function chartSampel($tipe)
    {
        return $this->chartTemplate($tipe, 'sampel', self::SELECT_SAMPEL);
    }

    public function chartEkstraksi($tipe)
    {
        return $this->chartTemplate($tipe, 'ekstraksi', self::SELECT_SAMPEL);
    }

    public function chartPcr($tipe)
    {
        return $this->chartTemplate($tipe, 'pcr', self::SELECT_SAMPEL);
    }

    public function chartPositifNegatif($tipe, $hasilPemeriksaan)
    {
        return $this->chartTemplate($tipe, 'positif_negatif', self::SELECT_SAMPEL, $hasilPemeriksaan);
    }
}
