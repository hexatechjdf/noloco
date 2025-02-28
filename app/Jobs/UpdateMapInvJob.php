<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Models\ErrorLog;
use App\Jobs\UpdateMapInvJob;

class UpdateMapInvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $variables;
    public $invType;
    public $inv_id;
    public $retry;
    public $log_table_id;
    /**
     * Create a new job instance.
     */
    public function __construct($variables,$invType,$inv_id,$retry = 1,$log_table_id = null)
    {
        $this->variables = $variables;
        $this->invType = $invType;
        $this->inv_id = $inv_id;
        $this->retry = $retry;
        $this->log_table_id = $log_table_id;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService,DealService $dealService): void
    {
        try {
            $query = $inventoryService->setInventoryDataByCsv($this->variables,$this->invType);
            $data = $inventoryService->submitRequest($query,1,$this->variables);

            if($this->log_table_id)
            {
                // ErrorLog::where('table_id',$this->log_table_id)->delete();
            }

            if(isset($data['errors']) && $this->retry <=2)
            {
                $this->retry++;
                $res = createErrorLogs($data['errors'],$variables,'update', $this->inv_id,'Inventory','csv');
                dispatch((new UpdateMapInvJob($res,$this->invType, $this->inv_id,$this->retry)));
            }
        } catch (\Exception $e) {
            Log::error('error file:'.$this->locationId.'=>' .$e);
        }
    }
}
