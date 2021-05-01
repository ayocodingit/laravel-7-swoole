<?php

function parseDate($date)
{
    if ($date) {
        return date('Y-m-d', strtotime($date));
    }
    return null;
}
function parseTime($time)
{
    $time = str_replace(':', '', $time);
    if (strlen($time) == 4) {
        return substr($time, 0, 2) . ':' . substr($time, 2, 2);
    } elseif (strlen($time) == 3) {
        return '0' . substr($time, 0, 1) . ':' . substr($time, 1, 2);
    } else {
        return '00:00';
    }
}
function parseDecimal($val)
{
    $val = str_replace(',', '.', $val);
    return floatval($val);
}
function cctor($obj)
{
    return clone $obj;
}

function usiaPasien($tanggal_lahir, $usia_tahun)
{
    if ($usia_tahun) {
        return $usia_tahun;
    }

    if ($tanggal_lahir) {
        $bday = new DateTime($tanggal_lahir);
        $today = new Datetime();
        $diff = $today->diff($bday);
        return $diff->y;
    }

    return '-';
}

function getCodeDagri($code)
{
    if (!$code) {
        return $code;
    }
    $code = (string)$code;
    $code = str_split($code);
    $codeDagri = '';
    foreach ($code as $key => $value) {
        $codeDagri .= $value;
        if ($key % 2 == 1 && $key < 6 && $key < count($code) - 1) {
            $codeDagri .= '.';
        }
    }
    return $codeDagri;
}

function getConvertCodeDagri($wilayah)
{
    if (!$wilayah) {
        return $wilayah;
    }
    return (int)str_replace('.', '', $wilayah);
}
