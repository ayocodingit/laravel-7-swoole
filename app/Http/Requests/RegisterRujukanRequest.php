<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumValueRule;
use App\Enums\JenisKelaminEnum;
use App\Enums\SumberPasienEnum;
use App\Enums\KewarganegaraanEnum;
use App\Enums\StatusPasienEnum;
use Spatie\Enum\Laravel\Rules\EnumIndexRule;

class RegisterRujukanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reg_fasyankes_id' => 'required|exists:fasyankes,id',
            'reg_kewarganegaraan' => ['required', new EnumValueRule(KewarganegaraanEnum::class)],
            'reg_sumberpasien' => 'required',
            'reg_sumberpasien_isian' => 'required_unless:reg_sumberpasien,' . SumberPasienEnum::Umum(),
            'reg_nama_pasien' => 'required',
            'reg_nik'  => 'nullable|digits:16',
            'reg_nohp' => 'nullable',
            'reg_kota_id' => 'required|numeric|exists:kota,id',
            'reg_provinsi_id' => 'nullable|numeric|exists:provinsi,id',
            'reg_kecamatan_id' => 'nullable|numeric|exists:kecamatan,id',
            'reg_kelurahan_id' => 'nullable|numeric|exists:kelurahan,id',
            'reg_alamat' => 'required',
            'reg_jk' => ['nullable', new EnumValueRule(JenisKelaminEnum::class)],
            'reg_fasyankes_pengirim' => 'required',
            'reg_nama_rs' => 'required',
            'samples' => 'array',
            'samples.*.id' => 'required|exists:sampel,id',
            'reg_tgllahir' => 'nullable|date',
            'reg_usia_bulan' => 'nullable|numeric',
            'reg_usia_tahun' => 'nullable|numeric',
            'reg_tempatlahir' => 'nullable',
            'reg_tanggalkunjungan' => 'nullable|date',
            'status' => [
                'nullable',
                new EnumIndexRule(StatusPasienEnum::class),
            ],
        ];
    }
}
