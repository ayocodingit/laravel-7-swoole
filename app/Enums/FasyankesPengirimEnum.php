<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class FasyankesPengirimEnum extends Enum
{
    public static function puskesmas(): FasyankesPengirimEnum
    {
        return new class () extends FasyankesPengirimEnum
        {
            public function getValue(): string
            {
                return 'puskesmas';
            }
        };
    }

    public static function rumah_sakit(): FasyankesPengirimEnum
    {
        return new class () extends FasyankesPengirimEnum
        {
            public function getValue(): string
            {
                return 'rumah sakit';
            }
        };
    }

    public static function dinkes(): FasyankesPengirimEnum
    {
        return new class () extends FasyankesPengirimEnum
        {
            public function getValue(): string
            {
                return 'dinkes';
            }
        };
    }

    public static function dinkes_kota(): FasyankesPengirimEnum
    {
        return new class () extends FasyankesPengirimEnum
        {
            public function getValue(): string
            {
                return 'dinkes kota';
            }
        };
    }

    public static function klinik(): FasyankesPengirimEnum
    {
        return new class () extends FasyankesPengirimEnum
        {
            public function getValue(): string
            {
                return 'klinik';
            }
        };
    }

    public static function lab(): FasyankesPengirimEnum
    {
        return new class () extends FasyankesPengirimEnum
        {
            public function getValue(): string
            {
                return 'laboratorium';
            }
        };
    }
}
