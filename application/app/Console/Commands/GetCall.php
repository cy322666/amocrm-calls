<?php

namespace App\Console\Commands;

use App\Models\Call;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use Services\amoCRM\Client;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GetCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calls:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        //this app
        $latestId1 = DB::connection('pgsql')
            ->table('calls')
            ->latest('call_id')
            ->limit(1)
            ->get('call_id')
                ->first()
                ->call_id ?? 0;

        //origin app
        $latestId2 = DB::connection('pgsql2')
            ->table('calls')
            ->latest('id')
            ->limit(1)
            ->get('id')
                ->first()
                ->id;

        if ($latestId1 !== $latestId2) {

            $calls = DB::connection('pgsql2')
                ->table('calls')
                ->where('id', '>', $latestId1)
                ->get();

            foreach ($calls as $call) {

                try {
                    Call::query()->create([
                        'call_id'  => $call->id,
                        'type'     => $call->tp,
                        'datetime' => $call->date_call,
                        'direction'=> $call->direction,
                        'link'     => $call->basename,
                        'duration' => $call->duration,
                        'phone'    => $call->where_call,
                        'status'   => 0,
                    ]);
                } catch (\Throwable $e) {

                    Log::error(__METHOD__, [$e->getMessage()]);

                    continue;
                }
            }
        }

        return CommandAlias::SUCCESS;
    }
}
