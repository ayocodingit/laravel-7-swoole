<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

trait ImportExcelTrait
{
    private $limit = 200;

    public $result = [
        'message' => 'Sukses membaca file import excel',
        'data' => [],
        'errors' => [],
        'errors_count' => 0
    ];

    private $sampel = [];

    public function validated(array $rows, $key)
    {
        App::setLocale('id');
        $validator = Validator::make($rows, $this->rules());
        $this->initError($key);
        if ($validator->fails()) {
            $this->setError($key, $validator->errors()->all());
        }
        $errors = $validator->errors();
        $isNomorSampel = isset($rows['nomor_sampel']) &&  !$errors->get('nomor_sampel');
        $isNomorSampelLabkes = isset($rows['nomor_sampel_labkes']) &&  !$errors->get('nomor_sampel_labkes');
        if ($isNomorSampel || $isNomorSampelLabkes) {
            $this->checkDuplicateSampel($key, $rows);
        }
    }

    public function setData(array $data)
    {
        array_push($this->result['data'], $data);
    }

    public function getStatusCodeResponse()
    {
        return $this->result['errors_count'] > 0 ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;
    }

    public function checkValidLimit($rows)
    {
        abort_if(
            count($rows) >= $this->limit,
            Response::HTTP_BAD_REQUEST,
            __('validation.excel_data_limit', ['limit' => $this->limit])
        );
    }

    public function setError($key, $message)
    {
        if (is_array($message)) {
            $this->result['errors'][$key] = $message;
        } else {
            $this->result['errors'][$key][] = $message;
        }
        ++$this->result['errors_count'];
    }

    public function initError($key)
    {
        $this->result['errors'][$key] = null;
    }

    public function checkDuplicateSampel($key, $rows)
    {
        $nomorSampel = $this->getSampel($rows);

        if (!$nomorSampel) {
            return;
        }

        if (in_array($nomorSampel, $this->sampel)) {
            $this->setError($key, __('validation.unique', ['attribute' => 'nomor sampel']));
        } else {
            $this->sampel[] = $nomorSampel;
        }
    }

    public function getSampel($rows)
    {
        return $rows['nomor_sampel_labkes'] ?? $rows['nomor_sampel'] ?? null;
    }
}
