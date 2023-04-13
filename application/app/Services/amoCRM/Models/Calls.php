<?php

namespace App\Services\amoCRM\Models;

class Calls
{
    public static function send($amoApi, $call)
    {
        return $amoApi->service
            ->ajax()
            ->postJson('/api/v4/calls', [[
                "duration" => $call['duration'],
                "source"   => $call[''],
                "phone"    => $call['where_call'],
                "link"     => $call['basename'],
                "direction"=> $call['direction'],
                "call_result" => "Успешный разговор",
                "call_status" => 4,
                "call_responsible" => 0,
            ]]);
    }
}
