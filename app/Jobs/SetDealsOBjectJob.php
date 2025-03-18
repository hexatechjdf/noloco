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
use App\Helper\CRM;

class SetDealsOBjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;
    public $dealId;
    public $contact;
    public $payload;
    /**
     * Create a new job instance.
     */
    public function __construct($contact,$dealId, $type = 'customerMapping',$payload = null)
    {
        $this->contact = $contact;
        $this->dealId = $dealId;
        $this->type = $type;
        $this->payload = $payload;
    }
    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService, DealService $dealService)
    {
        $variables = $this->payload ?? setDealQueryData($this->contact,[],$this->type);
         Log::info($variables);
        $variables['graphqlPayload'][0]['id'] = $this->dealId;

        // $variables = $this->payload ?? [
        //     'graphqlPayload' => [
        //         [
        //             "coBorrowerFullName" => ["first" => 2324,'last' => 'honolulu'],
        //             "coBorrowerAddress" => ["country" => "US"],
        //             "coBorrowerPhone" => ["number" => 235435],
        //             "coBorrowerEmail" => "dfgfg",
        //             "coBorrowerHighlevelClientId" => "Y13wCs9djV5O0pb4mR14",
        //             "coBorrower" => true,
        //             "id" => "75"
        //         ]
        //     ]
        // ];
        try {
            $query = $dealService->updateDealQuery($variables);

            $data = $inventoryService->submitRequest($query, 1,$variables);
            if(isset($data['errors']))
            {
                $t = $this->type == 'customerMapping' ? 'customer' : 'coborrower';

                $res = createErrorLogs($data['errors'],$variables,'update', $this->dealId,'Deals',$t);
                dispatch((new SetDealsOBjectJob($this->contact, $this->dealId,'coborrowerMapping',$res)));
            }
            Log::info($data);
            Log::info($query);
            $uurl = 'contacts/'.$this->contact->id.'/tags';
            $data = [
                'tags' => ['deals']
            ];
            $res= CRM::crmV2Loc(1, $this->contact->locationId, $uurl, 'post', $data);
        } catch (\Exception $e) {
            Log::error($e);
        }
        return 1;
    }


}
