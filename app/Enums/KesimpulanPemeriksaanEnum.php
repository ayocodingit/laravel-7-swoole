<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class KesimpulanPemeriksaanEnum extends Enum
{
    public static function positif(): KesimpulanPemeriksaanEnum
    {
        return new class () extends KesimpulanPemeriksaanEnum
        {
            public function getValue(): string
            {
                return 'positif';
            }
        };
    }

    public static function negatif(): KesimpulanPemeriksaanEnum
    {
        return new class () extends KesimpulanPemeriksaanEnum
        {
            public function getValue(): string
            {
                return 'negatif';
            }
        };
    }

    public static function invalid(): KesimpulanPemeriksaanEnum
    {
        return new class () extends KesimpulanPemeriksaanEnum
        {
            public function getValue(): string
            {
                return 'invalid';
            }
        };
    }

    public static function inkonklusif(): KesimpulanPemeriksaanEnum
    {
        return new class () extends KesimpulanPemeriksaanEnum
        {
            public function getValue(): string
            {
                return 'inkonklusif';
            }
        };
    }

    public static function swab_ulang(): KesimpulanPemeriksaanEnum
    {
        return new class () extends KesimpulanPemeriksaanEnum
        {
            public function getValue(): string
            {
                return 'swab ulang';
            }
        };
    }
}
