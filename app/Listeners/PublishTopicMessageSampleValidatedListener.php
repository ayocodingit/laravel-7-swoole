<?php

namespace App\Listeners;

use App\Events\SampleValidatedEvent;
use App\Http\Services\GCloudPubSubService;
use App\Traits\ConvertEnumTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;

class PublishTopicMessageSampleValidatedListener
{
    use ConvertEnumTrait;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SampleValidatedEvent $event)
    {
        $sampel = $event->sampel;
        $topic = config('services.gcould_pubsub_topic_name.sample_validated');
        $pubSubClient = App::make(GCloudPubSubService::class);
        $pubSubClient->publish($topic, $this->getDataMapping($sampel));
    }

    /**
     * getDataMaaping
     * function maaping data
     * @param  mixed $sampel
     * @return void
     */
    private function getDataMapping($sampel)
    {
        $pasien = optional($sampel->register)
                    ->pasiens()
                    ->with(['kota', 'provinsi'])
                    ->first();
        $pemeriksaanSampel = $sampel->pemeriksaanSampel;
        $fasyankes = $sampel->register->fasyankes;
        $mappingPasien = $this->getPasien($pasien);
        $mappingSampel = $this->getSampel($sampel, $pemeriksaanSampel, $fasyankes);
        return [
            'data' => collect($mappingPasien + $mappingSampel)
        ];
    }

    private function getPasien($pasien): array
    {
        return [
            'nik' => $pasien->nik,
            'phone_number' => $pasien->no_hp,
            'name' => $pasien->nama_lengkap,
            'age' => $pasien->usia_tahun,
            'month' => $pasien->usia_bulan,
            'yearsOld' => $pasien->usia_tahun,
            'monthsOld' => $pasien->usia_bulan,
            'place_of_birth' => $pasien->tempat_lahir,
            'birth_date' => $pasien->tanggal_lahir,
            'gender' => $pasien->jenis_kelamin,
            'address_street' => $pasien->alamat_lengkap,
            'rt' => $pasien->no_rt,
            'rw' => $pasien->no_rw,
            'nationality' => $pasien->kewarganegaraan,
            'status' => $pasien->status_name,
            'address_province_name' => optional($pasien->provinsi)->nama,
            'address_province_code' => $pasien->provinsi_id,
            'address_district_name' => optional($pasien->kota)->nama,
            'address_district_code' => $pasien->kota_id,
            'address_subdistrict_name' => $pasien->kecamatan,
            'address_subdistrict_code' => $pasien->kecamatan_id,
            'address_village_name' => $pasien->kelurahan,
            'address_village_code' => $pasien->kelurahan_id,
        ];
    }

    private function getSampel($sampel, $pemeriksaanSampel, $fasyankes): array
    {
        return [
            'last_date_status_patient' => $sampel->waktu_sample_valid,
            'specimens_type' => $sampel->jenis_sampel_nama,
            'inspection_date' => $sampel->waktu_sample_taken,
            'get_specimens_to' => optional($sampel->register)->kunjungan_ke,
            'inspection_result' => $pemeriksaanSampel->kesimpulan_pemeriksaan,
            'id_fasyankes_pelaporan' => optional($fasyankes)->id_fasyankes_pelaporan,
            'nama_fasyankes_pelaporan' => optional($fasyankes)->nama_fasyankes_pelaporan,
        ];
    }
}
