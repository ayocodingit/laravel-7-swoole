<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class StatusPasienEnum extends Enum
{
    public static function kontak_erat(): StatusPasienEnum
    {
        return new class () extends StatusPasienEnum
        {
            public function getValue(): string
            {
                return 'kontak erat';
            }

            public function getIndex(): int
            {
                return 1;
            }
        };
    }

    public static function suspek(): StatusPasienEnum
    {
        return new class () extends StatusPasienEnum
        {
            public function getValue(): string
            {
                return 'suspek';
            }

            public function getIndex(): int
            {
                return 2;
            }
        };
    }

    public static function probable(): StatusPasienEnum
    {
        return new class () extends StatusPasienEnum
        {
            public function getValue(): string
            {
                return 'probable';
            }

            public function getIndex(): int
            {
                return 3;
            }
        };
    }

    public static function konfirmasi(): StatusPasienEnum
    {
        return new class () extends StatusPasienEnum
        {
            public function getValue(): string
            {
                return 'konfirmasi';
            }

            public function getIndex(): int
            {
                return 4;
            }
        };
    }

    public static function tanpa_kriteria(): StatusPasienEnum
    {
        return new class () extends StatusPasienEnum
        {
            public function getValue(): string
            {
                return 'tanpa kriteria';
            }

            public function getIndex(): int
            {
                return 5;
            }
        };
    }
}
