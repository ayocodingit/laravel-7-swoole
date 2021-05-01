<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasyankes extends Model
{
    protected $table = 'fasyankes';

    protected $with = ['kota'];

    protected $fillable = [
        'nama',
        'tipe',
        'kota_id'
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function register()
    {
        return $this->hasOne(Register::class);
    }

    public function scopeOrder($query, $order, $order_direction)
    {
        $query->when(in_array($order, ['nama', 'tipe']), function ($query) use ($order, $order_direction) {
            $query->orderBy("fasyankes.$order", $order_direction);
        });
        $query->when($order == 'kota', function ($query) use ($order_direction) {
            $query->orderBy("kota.nama", $order_direction);
        });
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        $query->where(function ($query) use ($search) {
            $query->where('fasyankes.nama', 'ilike', '%' . $search . '%')
                    ->orWhere('fasyankes.tipe', 'ilike', '%' . $search . '%')
                    ->orWhere('kota.nama', 'ilike', '%' . $search . '%');
        });
        return $query;
    }

    public function scopeJoinKota($query)
    {
        return $query->leftJoin('kota', 'kota.id', 'fasyankes.kota_id');
    }
}
