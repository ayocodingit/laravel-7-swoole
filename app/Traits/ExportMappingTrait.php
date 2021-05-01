<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ExportMappingTrait
{
    private function getNomorUrut(Request $request): int
    {
        return (int) ($request->get('page', 1) - 1) * $request->get('perpage', 20) + 1;
    }

    private function alamatLengkap($model)
    {
        $alamat_lengkap = $model->alamat_lengkap ? $model->alamat_lengkap : '';
        $alamat_lengkap .= $model->kelurahan ? ' Kel. ' . $model->kelurahan : null;
        $alamat_lengkap .= $model->kecamatan ? ' Kec. ' . $model->kecamatan : null;
        $alamat_lengkap .= $model->nama_kota ? ' ' . $model->nama_kota : null;
        $alamat_lengkap .= $model->provinsi ? ' Prov. ' . $model->provinsi : null;
        return $alamat_lengkap;
    }

    private function hasil_deteksi($hasil)
    {
        if (!$hasil) {
            return $hasil;
        }
        $result = '';
        foreach ($hasil as $row) {
            $result .= $row['target_gen'] . ':' . $row['ct_value'] . ';';
        }
        return $result;
    }

    private function getHasilDeteksiTerkecil($hasil)
    {
        if (!$hasil) {
            return '-';
        }
        $result = collect($hasil)
            ->whereNotNull('ct_value')
            ->whereNotIn('target_gen', ['IC','ic'])
            ->where('ct_value', '!=', '-')
            ->where('ct_value', '>', 0)
            ->sortBy('ct_value')
            ->first();
        return $result ? round($result['ct_value'], 2) : '-';
    }
}
