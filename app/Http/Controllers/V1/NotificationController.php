<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Sampel;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $data = [];
        $count = 0;
        if (in_array($user->role_id, [ROLE_EKSTRAKSI, ROLE_ADMIN])) {
            $count = Sampel::where('sampel_status', 'extraction_sample_reextract')->count('id');
        }
        if ($count > 0) {
            $data[] = [
                'link' => '/ekstraksi/dikembalikan',
                'message' => "Ada $count sampel yang dikembalikan",
                'icon' => 'uil-flask',
            ];
        }
        return response()->json(['data' => $data, 'count' => $count]);
    }
}
