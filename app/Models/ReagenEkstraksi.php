<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReagenEkstraksi extends Model
{
    protected $table = 'reagen_ekstraksi';

    protected $fillable = ['nama', 'metode_ekstraksi'];

    protected $hidden = ['created_at', 'updated_at'];
}
