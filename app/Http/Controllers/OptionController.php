<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Models\LabPCR;
use App\Models\Validator;
use App\Models\JenisSampel;
use App\Models\JenisVTM;
use App\Models\LabSatelit;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class OptionController extends Controller
{
    public function getRoles()
    {
        $models = Role::select('id', 'roles_name as text')->get();
        return response()->json($models);
    }
    public function getLabPCR()
    {
        $models = LabPCR::select('id', 'nama as text')->get();
        return response()->json($models);
    }
    public function getValidator()
    {
        $models = Validator::selectRaw('id, concat(nama, \' | NIP \', nip) as text')->get();
        return response()->json($models);
    }
    public function getJenisSampel()
    {
        $models = JenisSampel::select('id', 'nama as text')->get();
        return response()->json($models);
    }
    public function getJenisVTM()
    {
        $models = JenisVTM::select('id', 'nama as text')->get();
        return response()->json($models);
    }
    public function getNegara()
    {
        $models = Negara::select('id', 'nama as text')->orderBy('nama')->get();
        return response()->json($models);
    }

    public function listProvinsi()
    {
        return response()->json(Provinsi::select('id', 'nama')->orderBy('nama')->get());
    }

    public function listKecamatan(Request $request)
    {
        $kecamatan = Kecamatan::orderby('nama', 'asc');
        if ($request->has('kota_id')) {
            $kecamatan->where('kota_id', $request->input('kota_id'));
        }
        return response()->json($kecamatan->get());
    }

    public function listKelurahan(Request $request)
    {
        $kelurahan = Kelurahan::orderby('nama', 'asc');
        if ($request->has('kecamatan_id')) {
            $kelurahan->where('kecamatan_id', $request->input('kecamatan_id'));
        }
        return response()->json($kelurahan->get());
    }
}
