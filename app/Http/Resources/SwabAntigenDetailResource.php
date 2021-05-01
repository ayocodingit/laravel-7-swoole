<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SwabAntigenDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $logs = $this->swabAntigenLogs()->orderBy('created_at', 'desc')->take(5)->get();
        return parent::toArray($request) + ['logs' => $logs];
    }
}
