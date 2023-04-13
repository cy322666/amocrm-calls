<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Call;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\amoCRM\Client;
use App\Services\amoCRM\Models\Contacts;
use App\Services\amoCRM\Models\Notes;
use Throwable;

class SendCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calls:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        $calls = Call::query()
            ->where('status', 0)
            ->limit(15)
            ->get();

        $amoApi = (new Client(Account::query()->first()))->init();

        foreach ($calls as $call) {

            try {
                $amoApi->service->queries->setDelay(0.5);

                if (strlen($call->phone) < 15) {

                    $contact = Contacts::search(['Телефоны' => [$call->phone]], $amoApi);

                    $leads = $contact
                        ->leads
                        ->filter(function($lead) {

                            return $lead->status_id !== 142 && $lead->status_id !== 143;
                        });

                    $lead = $leads->count() > 0 ? $leads->first() : false;

                    if ($lead) {

                        Notes::addOne($lead, Notes::formatCall($call->toArray()));

                        $call->lead_id = $lead->id;
                        $call->contact_id = $lead->contact->id;
                        $call->status = 1;
                    } else
                        $call->status = 5;
                } else
                    $call->status = 4;

                $call->save();

            } catch (Throwable $e) {

                $call->status = 2;
                $call->save();

                Log::error(__METHOD__.' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine(), [
                    $call->id,
                ]);

                continue;
            }
        }
    }
}
