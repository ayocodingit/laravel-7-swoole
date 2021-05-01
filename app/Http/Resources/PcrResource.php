<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PcrResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
            're_pcr' => $this->logs->where('sampel_status', 'pcr_sample_received')->count() >= 2 ? 're-PCR' : null
        ];
    }
}
