<?php

namespace App\Services\amoCRM\Models;

use App\Services\amoCRM\Client;

class Calls
{
    public static function send(Client $amoApi, $call, int $responsibleId): bool
    {
        return $amoApi->service
            ->ajax()
            ->postJson('/api/v4/calls', [[
                "duration" => (int)$call['duration'],
                "source"   => $call['type'],
                "phone"    => $call['phone'],
                "link"     => env('APP_URL').'/'.str_replace(' ', '%20', $call['link']),
                "direction"=> 'outbound',
                "call_responsible" => $responsibleId,
                "call_result" => "Разговор",
                "call_status" => 4,
            ]]);
    }
}
