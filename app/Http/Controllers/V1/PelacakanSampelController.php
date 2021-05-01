<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SampelDetailResource;
use App\Models\Sampel;

class PelacakanSampelController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sampel $sampel
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Sampel $sampel)
    {
        return new SampelDetailResource($sampel);
    }
}
