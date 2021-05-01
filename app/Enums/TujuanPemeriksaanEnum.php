<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class TujuanPemeriksaanEnum extends Enum
{
    public static function default(): TujuanPemeriksaanEnum
    {
        return new class () extends TujuanPemeriksaanEnum
        {
            public function getIndex(): int
            {
                return 0;
            }

            public function getValue(): string
            {
                return 'default';
            }
        };
    }

    public static function suspek(): TujuanPemeriksaanEnum
    {
        return new class () extends TujuanPemeriksaanEnum
        {
            public function getIndex(): int
            {
                return 1;
            }

            public function getValue(): string
            {
                return 'suspek';
            }
        };
    }

    public static function kontak_erat(): TujuanPemeriksaanEnum
    {
        return new class () extends TujuanPemeriksaanEnum
        {
            public function getIndex(): int
            {
                return 2;
            }

            public function getValue(): string
            {
                return 'kontak erat';
            }
        };
    }

    public static function screening(): TujuanPemeriksaanEnum
    {
        return new class () extends TujuanPemeriksaanEnum
        {
            public function getIndex(): int
            {
                return 3;
            }

            public function getValue(): string
            {
                return 'screening';
            }
        };
    }

    public static function pelaku_perjalanan(): TujuanPemeriksaanEnum
    {
        return new class () extends TujuanPemeriksaanEnum
        {
            public function getIndex(): int
            {
                return 4;
            }

            public function getValue(): string
            {
                return 'pelaku perjalanan';
            }
        };
    }

    public static function lainnya(): TujuanPemeriksaanEnum
    {
        return new class () extends TujuanPemeriksaanEnum
        {
            public function getIndex(): int
            {
                return 5;
            }

            public function getValue(): string
            {
                return 'alasan lain';
            }
        };
    }
}
