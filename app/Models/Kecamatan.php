<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = "kecamatan";

    protected $fillable = [
        'nama',
        'kota_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class);
    }
}
