<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\amoCRM\Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * @throws Exception
     */
    public function weekend(Request $request)
    {
        $dateTime = Carbon::parse($request->toArray()['task']['add'][0]['complete_till']);
        $dayWeek  = Carbon::parse($request->toArray()['task']['add'][0]['complete_till'])->dayOfWeek;
        $taskId   = $request->toArray()['task']['add'][0]['id'];

        if ($dayWeek == 6 || $dayWeek == 7) {

            Log::info(__METHOD__, ['day' => $dayWeek, 'task' => $taskId]);

            $amoApi = (new Client(Account::query()->first()))->init();

            $task = $amoApi->service->tasks()->find($taskId);

            $completeTill = $dayWeek == 7 ? $dateTime->addDays(2) : $dateTime->addDay();

            $task->complete_till = $completeTill->timestamp;
            $task->save();
        }
    }
}
