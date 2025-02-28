<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use Illuminate\Support\Facades\Log;
use App\Models\ErrorLog;

class UpdateDealJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $availableObjects;
    public $deal_id;
    public $log_table_id;
    public $payload;
    /**
     * Create a new job instance.
     */
    public function __construct($availableObjects,$deal_id,$payload = null,$retry = 1,$log_table_id = null)
    {
        $this->availableObjects = $availableObjects;
        $this->deal_id = $deal_id;
        $this->log_table_id = $log_table_id;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService, DealService $dealService): void
    {
        try {
            $variables =  $this->payload ?? updateDealQueryData($this->availableObjects,null, null);

            $variables['graphqlPayload'][0]['id'] = $this->deal_id;
            $query = $this->dealService->updateDealQuery($variables);
            $data = $this->inventoryService->submitRequest($query, 1,$variables);

            if($this->log_table_id)
            {
                // ErrorLog::where('table_id',$this->log_table_id)->delete();
            }
            if(isset($data['errors']) && $this->retry <=2)
            {
                $this->retry++;
                $res = createErrorLogs($data['errors'],$variables,'update', $this->deal_id,'Deals','deals');
                dispatch((new UpdateDealJob($this->availableObjects, $this->deal_id,$res,$this->retry)));
            }
        }
        catch(\Exception $e){
        }
    }
}
