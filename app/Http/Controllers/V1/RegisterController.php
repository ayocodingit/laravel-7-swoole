<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterMandiriRequest;
use App\Models\Pasien;
use App\Models\PasienRegister;
use App\Models\Register;
use App\Models\RegisterLog;
use App\Models\Sampel;
use App\Traits\RegisterTrait;
use App\Traits\SampelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegisterTrait;
    use SampelTrait;

    public function requestNomor(Request $request)
    {
        return response()->json([
            'result' => $this->generateNomorRegister(null, $request->input('tipe')),
        ]);
    }

    public function storeMandiri(RegisterMandiriRequest $request)
    {
        DB::beginTransaction();
        try {
            $register = $this->getRequestRegister($request);
            $register['nomor_register'] = $this->generateNomorRegister();
            $register['register_uuid'] = Str::uuid();
            $register['creator_user_id'] = $request->user()->id;
            $register = Register::create($register);
            $pasien = Pasien::create($this->getRequestPasien($request));
            PasienRegister::create(['pasien_id' => $pasien->id, 'register_id' => $register->id]);
            $sampel = Sampel::create([
                'nomor_sampel' => $request->input('reg_sampel'),
                'nomor_register' => $register->nomor_register,
                'register_id' => $register->id,
                'sampel_status' => 'waiting_sample',
            ]);
            $description = 'Data Pasien Mandiri Teregistrasi';
            $this->updateState($sampel, $sampel->sampel_status, $sampel, $description);
            DB::commit();
            return response()->json(['message' => 'Proses Registrasi Mandiri Berhasil Ditambahkan'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function storeUpdate(RegisterMandiriRequest $request, Register $register, Pasien $pasien)
    {
        DB::beginTransaction();
        try {
            $sampel = Sampel::find($request->input('sampel_id'));
            $registerOrigin = clone $register;
            $pasienOrigin = clone $pasien;
            $sampelOrigin = clone $sampel;
            $register->update($this->getRequestRegister($request));
            $pasien->update($this->getRequestPasien($request));
            $sampel->update(['nomor_sampel' => $request->input('reg_sampel')]);
            $registerChanges = $register->getChanges();
            $pasienChanges = $pasien->getChanges();
            $sampelChanges = $sampel->getChanges();
            $info = [
                'register' => $this->registerLogs($registerOrigin, $registerChanges),
                'sampel' => $this->sampelLogs($sampelOrigin, $sampelChanges),
                'pasien' => $this->pasienLogs($pasienOrigin, $pasienChanges)
            ];
            if ($registerChanges || $pasienChanges || $sampelChanges) {
                $register->logs()->create(['user_id' => $request->user()->id, 'info' => json_encode($info)]);
            }
            DB::commit();
            return response()->json(['message' => 'Data Register Berhasil Diubah']);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function getById($register_id, $pasien_id)
    {
        $model = PasienRegister::where('pasien_register.register_id', $register_id)
            ->where('pasien_register.pasien_id', $pasien_id)
            ->joinRegisterFromPasienRegister()
            ->joinPasien()
            ->joinWilayah()
            ->joinFasyankes()
            ->joinSampel()
            ->selectDetail()
            ->first();

        return response()->json([
            'result' => $model,
        ]);
    }

    public function logs($register_id)
    {
        $model = RegisterLog::where('register_id', $register_id)
            ->join('users', 'users.id', 'register_logs.user_id')
            ->whereRaw("(
                    info->>'pasien' <> '[]'::text or
                    info->>'register' <> '[]'::text or
                    info->>'sampel' <> '[]'::text
                    )")
            ->select('info', 'register_logs.created_at', 'users.name as updated_by')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'result' => $model,
        ]);
    }

    public function delete(Register $register)
    {
        $register->delete();
        return response()->json([
            'message' => "Berhasil menghapus data register",
        ]);
    }
}
