<?php

namespace App\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Jobs\UpdateMapInvJob;

class SetExtraInvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $fields;
    public $mapping;
    public $locationId;
    public $unique;
    public $existInventoryIds;
    public $rowStocks;
    /**
     * Create a new job instance.
     */
    public function __construct($existInventoryIds,$rowStocks)
    {
        $this->existInventoryIds = $existInventoryIds;
        $this->rowStocks = $rowStocks;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $result = array_filter($existInventoryIds, function($value) use ($rowStocks) {
            return !in_array($value, $rowStocks);
        });

        Log::info($result);

        foreach($result as $ke => $exitt)
        {
            $pl = [
                'id' => $ke.'__Int',
                'status' => 'SOLD__ENUM',
            ];
            $variables = ['graphqlPayload' => [arrayToGraphQL1($pl,'inventoryCollection')]];
            dispatch((new UpdateMapInvJob($variables,'updateInventory', $ke)));
        }
    }
}
