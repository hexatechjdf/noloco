<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Helper\CRM;

class SetDealsOBjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;
    public $dealId;
    public $contact;
    /**
     * Create a new job instance.
     */
    public function __construct($contact,$dealId, $type = 'customerMapping')
    {
        $this->contact = $contact;
        $this->dealId = $dealId;
        $this->type = $type;
    }
    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService, DealService $dealService)
    {
        $data = setDealQueryData($this->contact,[],$this->type);
        try {
            $query = $dealService->updateDealQuery($this->dealId, $data);
            $data = $inventoryService->submitRequest($query, 1);

            $uurl = 'contacts/'.$this->contact->id.'/tags';
            $data = [
                'tags' => ['noloco']
            ];
            $res= CRM::crmV2Loc(1, $this->contact->locationId, $uurl, 'post', $data);
        } catch (\Exception $e) {
        }
        return 1;
    }
}
