<?php

namespace App\Http\Services;

use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Support\Facades\Log;

class GCloudPubSubService
{
    private $pubSubClient;

    public function __construct()
    {
        $keyFile = config('services.gcould');
        $keyFile['private_key'] = base64_decode($keyFile['private_key']);
        $this->pubSubClient = new PubSubClient([
            'keyFile' => $keyFile
        ]);
    }

    public function publish($topic, $payload)
    {
        Log::alert(json_encode($payload));
        $this->pubSubClient->topic($topic)->publish($payload);
    }
}
