<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $table = 'kota';

    public $timestamps = false;

    protected $with = ['provinsi'];

    protected $fillable = [
        'id',
        'provinsi_id',
        'nama'
    ];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
    }

    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = strtoupper($value);
    }

    public function scopeOrder($query, $order, $order_direction)
    {
        $query->when($order == 'provinsi', function ($query) use ($order_direction) {
            $query->orderBy('provinsi.nama', $order_direction);
        });
        $query->when($order == 'nama', function ($query) use ($order, $order_direction) {
            $query->orderBy('kota.nama', $order_direction);
        });
        return $query;
    }

    public function scopeJoinProvinsi($query)
    {
        return $query->leftJoin('provinsi', 'provinsi.id', 'kota.provinsi_id');
    }

    public function scopeSearch($query, $search)
    {
        $query->where(function ($query) use ($search) {
            $query->where('kota.nama', 'ilike', '%' . $search . '%')
                    ->orWhere('provinsi.nama', 'ilike', '%' . $search . '%');
        });
        return $query;
    }
}
