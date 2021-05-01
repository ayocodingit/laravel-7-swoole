<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EkstraksiDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sampel = parent::toArray($request);
        return [
            'data' => $sampel + [
                'ekstraksi' => $this->ekstraksi,
                'status' => $this->status,
                'log_ekstraksi' => $this->logs()
                ->whereIn('sampel_status', ['extraction_sample_extracted', 'extraction_sample_sent', 'extraction_sample_reextract'])
                ->orderByDesc('created_at')
                ->get(),

            ],
        ];
    }
}
