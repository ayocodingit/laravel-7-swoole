<?php

namespace App\Enums;

use Spatie\Enum\Enum;

class JenisSampelEnum extends Enum
{
    public static function luarJenis(): JenisSampelEnum
    {
        return new class () extends JenisSampelEnum
        {
            public function getIndex(): int
            {
                return 999999;
            }
        };
    }
}
