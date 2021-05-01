<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Register;
use App\Models\PasienRegister;
use App\Models\Sampel;
use Illuminate\Support\Facades\DB;
use App\Enums\JenisRegistrasiEnum;
use App\Http\Requests\RegisterRujukanRequest;
use App\Traits\RegisterRujukanTrait;
use Illuminate\Http\Response;
use App\Http\Resources\ShowRegisterRujukanResource;
use App\Traits\RegisterTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RegistrasiRujukanController extends Controller
{
    use RegisterRujukanTrait;
    use RegisterTrait;

    public function cekData(Request $request)
    {
        $nomor = $request->get('nomor_sampel');
        $sampel = Sampel::where('nomor_sampel', 'ilike', $nomor)->firstOrFail();
        if ($sampel->register_id != null) {
            return response()->json(['message' => 'Sampel sudah memiliki data pasien'], Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            return response()->json(['result' => $sampel]);
        }
    }

    public function store(RegisterRujukanRequest $request)
    {
        DB::beginTransaction();
        try {
            $nomorRegister = $this->generateNomorRegister(null, JenisRegistrasiEnum::rujukan()->getValue());
            $storeRegistrasi = ['nomor_register' => $nomorRegister, 'register_uuid' => Str::uuid()];
            $register = new Register();
            $register->fill($this->getRegisterRujukanRequest($request) + $storeRegistrasi)->save();
            $pasien = new Pasien();
            $pasien->fill($this->getPasienRujukanRequest($request))->save();
            $pasienRegister = new PasienRegister();
            $pasienRegister->fill(['pasien_id' => $pasien->id, 'register_id' => $register->id]);
            $pasienRegister->save();
            $sampel = Arr::first($request->input('samples'));
            $sampel = Sampel::find($sampel['id']);
            $sampel->fill(['register_id' => $register->id, 'nomor_register' => $register->nomor_register, 'waktu_sample_taken' => now()]);
            $sampel->save();
            DB::commit();
            return response()->json(['message' => 'Proses Registrasi Rujukan Berhasil Ditambahkan'], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(Register $register)
    {
        $register->delete();
        return response()->json(['message' => "Berhasil menghapus data register"]);
    }

    public function show(Register $register)
    {
        return new ShowRegisterRujukanResource($register);
    }

    public function update(RegisterRujukanRequest $request, Register $register, Pasien $pasien)
    {
        DB::beginTransaction();
        try {
            $registerOrigin = clone $register;
            $pasienOrigin = clone $pasien;
            $register->fill($this->getRegisterRujukanRequest($request))->save();
            $pasien->fill($this->getPasienRujukanRequest($request))->save();
            $registerChanges = $register->getChanges();
            $pasienChanges = $pasien->getChanges();
            $register->logs()->create([
                "user_id" => auth()->user()->id,
                "info" => json_encode(array(
                    "register" => $this->registerLogs($registerOrigin, $registerChanges),
                    "pasien" => $this->pasienLogs($pasienOrigin, $pasienChanges)
                ))
            ]);
            DB::commit();
            return response()->json(['message' => 'Proses Registrasi Rujukan Berhasil Diubah']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
