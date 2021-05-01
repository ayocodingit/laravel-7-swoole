<?php

namespace App\Console\Commands;

use App\Models\DashboardCounter;
use App\Models\PasienRegister;
use App\Models\Sampel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DashboardCounterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dashboard_counter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Dashboard total untuk counter dashboard total';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    const STATUS_SAMPEL = [
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->items() as $item) {
            DashboardCounter::updateOrCreate(
                \Illuminate\Support\Arr::only($item, ['nama']),
                $item
            );
        }
    }

    public function items(): array
    {
        $data = [
            [
                "nama" => "tracking_progress_registration",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "tracking_progress_sampel",
                "total" => Sampel::sampelIsFromMigration()
                                    ->whereIn('sampel_status', array_merge(self::STATUS_SAMPEL, ['waiting_sample']))
                                    ->count('id'),
            ],
            [
                "nama" => "tracking_progress_ekstraksi",
                "total" => Sampel::queryEkstraksiPcr()
                                    ->whereNotNull('waktu_extraction_sample_extracted')
                                    ->whereNotNull('waktu_extraction_sample_sent')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "tracking_progress_rtpcr",
                "total" => Sampel::joinEkstraksi()
                                    ->joinPemeriksaanSampel()
                                    ->sampelIsFromMigration()
                                    ->whereIn('sampel.sampel_status', ['pcr_sample_analyzed', 'sample_verified', 'sample_valid', 'inkonklusif'])
                                    ->whereNotIn('pemeriksaansampel.kesimpulan_pemeriksaan', ['swab_ulang'])
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "tracking_progress_verifikasi",
                "total" => Sampel::sampel(['sample_verified', 'sample_valid'])->count('sampel.id'),
            ],
            [
                "nama" => "tracking_progress_validasi",
                "total" => Sampel::sampel('sample_valid')->count('sampel.id'),
            ],
            [
                "nama" => "pasien_negatif",
                "total" => Sampel::sampel(['sample_verified', 'sample_valid'])
                                    ->where('pemeriksaansampel.kesimpulan_pemeriksaan', 'ilike', '%negatif%')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "pasien_positif",
                "total" => Sampel::sampel(['sample_verified', 'sample_valid'])
                                    ->where('pemeriksaansampel.kesimpulan_pemeriksaan', 'ilike', '%positif%')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "registrasi_mandiri",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->where('register.jenis_registrasi', 'mandiri')
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_rujukan",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->where('register.jenis_registrasi', 'rujukan')
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_total",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_jumlah_perhari_mandiri",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->whereDate('register.created_at', date('Y-m-d'))
                                            ->where('register.jenis_registrasi', 'mandiri')
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_jumlah_perhari_rujukan",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->whereDate('register.created_at', date('Y-m-d'))
                                            ->where('register.jenis_registrasi', 'rujukan')
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_data_belum_lengkap_mandiri",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->where('register.jenis_registrasi', 'mandiri')
                                            ->where(function ($query) {
                                                $query->whereNull('pasien.nik')
                                                        ->orWhereNull('pasien.nama_lengkap');
                                            })
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_data_belum_lengkap_rujukan",
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinFasyankes()
                                            ->joinSampel()
                                            ->where('register.jenis_registrasi', 'rujukan')
                                            ->where(function ($query) {
                                                $query->whereNull('pasien.nik')
                                                        ->orWhereNull('pasien.nama_lengkap');
                                            })
                                            ->count('pasien.id'),
            ],
            [
                "nama" => "registrasi_pemeriksaan_selesai_mandiri",
                "total" => Sampel::sampel(['sample_verified', 'sample_valid'])
                                    ->where('register.jenis_registrasi', 'mandiri')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "registrasi_pemeriksaan_selesai_rujukan",
                "total" => Sampel::sampel(['sample_verified', 'sample_valid'])
                                    ->where('register.jenis_registrasi', 'rujukan')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "registrasi_belum_input_rujukan",
                "total" => Sampel::sampelIsFromMigration()
                                    ->whereNull('sampel.nomor_register')
                                    ->joinRegisterSampel()
                                    ->where('register.jenis_registrasi', 'rujukan')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "admin_sampel_jumlah_perhari_sampel",
                "total" => Sampel::sampelIsFromMigration()
                                ->where(function ($query) {
                                    $query->whereDate('sampel.waktu_sample_taken', date('Y-m-d'))
                                            ->orWhereDate('sampel.waktu_waiting_sample', date('Y-m-d'));
                                })
                                ->whereIn('sampel_status', ['waiting_sample', 'sample_taken'])
                                ->count('id'),
            ],
            [
                "nama" => "admin_sampel_sampel_register_mandiri",
                "total" => Sampel::sampelIsFromMigration()
                                    ->where('sampel_status', 'waiting_sample')
                                    ->count('id'),
            ],
            [
                "nama" => "admin_sampel_total_sampel",
                "total" => Sampel::sampelIsFromMigration()
                                    ->whereIn('sampel_status', self::STATUS_SAMPEL)
                                    ->count('id'),
            ],
            [
                "nama" => "ekstraksi_jumlah_perhari_ektraksi",
                "total" => Sampel::sampelIsFromMigration()
                                    ->whereDate('waktu_extraction_sample_extracted', date('Y-m-d'))
                                    ->count('id'),
            ],
            [
                "nama" => "ekstraksi_sampel_baru",
                "total" => Sampel::queryEkstraksiPcr()
                                    ->where('sampel.sampel_status', 'sample_taken')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "ekstraksi_ekstraksi",
                "total" => Sampel::queryEkstraksiPcr()
                                    ->where('sampel.sampel_status', 'extraction_sample_extracted')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "ekstraksi_kirim",
                "total" => Sampel::queryEkstraksiPcr()
                                    ->whereNotNull('waktu_extraction_sample_extracted')
                                    ->whereNotNull('waktu_extraction_sample_sent')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "ekstraksi_sampel_invalid",
                "total" => Sampel::queryEkstraksiPcr()
                                    ->whereNotNull('sampel.waktu_extraction_sample_reextract')
                                    ->where('sampel.sampel_status', 'extraction_sample_reextract')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "pcr_sampel_baru",
                "total" => Sampel::joinEkstraksi()
                                    ->joinPemeriksaanSampel()
                                    ->joinStatusSampel()
                                    ->sampelIsFromMigration()
                                    ->where('sampel.sampel_status', 'extraction_sample_sent')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "pcr_jumlah_perhari_pcr",
                "total" => Sampel::sampelIsFromMigration()
                                    ->whereDate('waktu_pcr_sample_received', date('Y-m-d'))
                                    ->count('id'),
            ],
            [
                "nama" => "pcr_hasil_pemeriksaan",
                "total" => Sampel::joinEkstraksi()
                                    ->joinPemeriksaanSampel()
                                    ->sampelIsFromMigration()
                                    ->whereIn('sampel.sampel_status', ['pcr_sample_analyzed', 'sample_verified', 'sample_valid', 'inkonklusif'])
                                    ->whereNotIn('pemeriksaansampel.kesimpulan_pemeriksaan', ['swab_ulang'])
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "pcr_re_pcr",
                "total" => Sampel::joinEkstraksi()
                                    ->joinPemeriksaanSampel()
                                    ->sampelIsFromMigration()
                                    ->whereIn('sampel.sampel_status', ['pcr_sample_analyzed', 'sample_verified', 'sample_invalid'])
                                    ->where('pemeriksaansampel.kesimpulan_pemeriksaan', 'invalid')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "verifikasi_belum_diverifikasi",
                "total" => Sampel::sampel(['pcr_sample_analyzed', 'inkonklusif', 'swab_ulang'])
                                    ->where('pemeriksaansampel.kesimpulan_pemeriksaan', '!=', 'invalid')
                                    ->count('sampel.id'),
            ],
            [
                "nama" => "verifikasi_terverifikasi",
                "total" => Sampel::sampel(['sample_verified', 'sample_valid'])->count('sampel.id'),
            ],
            [
                "nama" => "validasi_belum_divalidasi",
                "total" => Sampel::sampel('sample_verified')->count('sampel.id'),
            ],
            [
                "nama" => "validasi_tervalidasi",
                "total" => Sampel::sampel('sample_valid')->count('sampel.id'),
            ],
        ];
        foreach (STATUSES as $key => $value) {
            $data[] = [
                "nama" => "pasien_diperiksa_" . $key,
                "total" => PasienRegister::joinRegisterFromPasienRegister()
                                            ->joinPasien()
                                            ->joinSampel()
                                            ->whereIn('register.jenis_registrasi', ['rujukan', 'mandiri'])
                                            ->where('pasien.status', $key)
                                            ->count('pasien.id'),
            ];
        }
        return $data;
    }
}
