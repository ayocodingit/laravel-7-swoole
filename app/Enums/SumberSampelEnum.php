<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class SumberSampelEnum extends Enum
{
    public static function mandiri(): SumberSampelEnum
    {
        return new class () extends SumberSampelEnum
        {
            public function getValue(): string
            {
                return 'Mandiri';
            }
        };
    }

    public static function rujukanDinkes(): SumberSampelEnum
    {
        return new class () extends SumberSampelEnum
        {
            public function getValue(): string
            {
                return 'Rujukan Dinkes';
            }
        };
    }

    public static function rujukanRs(): SumberSampelEnum
    {
        return new class () extends SumberSampelEnum
        {
            public function getValue(): string
            {
                return 'Rujukan RS';
            }
        };
    }
}
