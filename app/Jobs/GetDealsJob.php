<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SetDealsOBjectJob;
use App\Services\Api\DealService;

class GetDealsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;
    public $contactId;
    public $locationId;
    public $contact;
    /**
     * Create a new job instance.
     */
    public function __construct($contact,$contactId,$locationId, $type = 'dealscustomerMapping')
    {
        $this->contact = $contact;
        $this->contactId = $contactId;
        $this->locationId = $locationId;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(DealService $dealService): void
    {
           $customer_deals =  $dealService->getContactDeals($this->locationId,$this->contactId,$this->type,true);
           foreach($customer_deals as $deal_id)
           {
             dispatch((new SetDealsOBjectJob($this->contact, $deal_id,$this->type)));
           }
    }
}
