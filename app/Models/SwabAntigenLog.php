<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwabAntigenLog extends Model
{
    protected $appends = ['updated_by'];

    protected $fillable = [
        'info',
        'user_id'
    ];

    public function swabAntigen()
    {
        return $this->belongsTo('App\Models\SwabAntigen', 'swab_antigen_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function getUpdatedByAttribute()
    {
        return $this->user->name;
    }
}
