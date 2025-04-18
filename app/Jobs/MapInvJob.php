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
use App\Jobs\UpdateMapInvJob;

class MapInvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 300;
    public $fields;
    public $mapping;
    public $locationId;
    public $unique;
    public $existInventoryIdss;
    /**
     * Create a new job instance.
     */
    public function __construct($fields,$mapping,$locationId,$unique,$existInventoryIdss)
    {
        $this->fields = $fields;
        $this->mapping = $mapping;
        $this->locationId = $locationId;
        $this->unique = $unique;
        $this->existInventoryIdss = $existInventoryIdss;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService,DealService $dealService): void
    {
        $val = @$this->fields[$this->unique];
        $key = @$this->mapping[$this->unique];
        if($key && $val)
        {
            $invType = 'createInventory';
            $filters = $this->setFilter($val,$key['column']);
            try{
                list($dealer_id,$dealership) =  $dealService->getDealership(request(),$this->locationId);
            }catch(\Exception $e){
            }

            list($id, $existInventoryIds) = $this->isExist($key,$val,$filters,$inventoryService,$dealService,$dealer_id,$this->existInventoryIdss,$this->unique);

            $data = [];
            foreach($this->mapping as $k => $map)
            {
                list($type,$value) = convertStringToArray('__', $map['column']);
                if (isset($this->fields[$k]) && $this->fields[$k] != '') {
                    $data[$value] = $this->fields[$k].'__'.$type;
                }elseif(!isset($this->fields[$k])){
                    $data[$value] = $k.'__'.$type;
                }
            }

            if($id)
            {
                $data['id'] = $id.'__Int';
                $invType = 'updateInventory';
                try{
                    $dId = supersetting('deal_dealership_col') ?? '';
                    $data[$dId] = $dealer_id.'__Int';
                }catch(\Exception $e){
                    Log::error('error file:'.$this->locationId.'=>' .$e);
                }
                $variables = ['graphqlPayload' => [arrayToGraphQL1($data,'inventoryCollection')]];

                dispatch((new UpdateMapInvJob($variables,$invType, $id)));
            }
        }


    }


    public function isExist($key,$value,$filters,$inventoryService,$dealService,$dealer_id,$existInventoryIds = [],$unique)
    {
        $id = null;
        if(in_array($value, $existInventoryIds))
        {
            $id = array_search($value, $existInventoryIds);
            unset($existInventoryIds[$id]);
        }
        else{
            $ar = [];
            $ar[strtolower($unique)] = $value.'__String';
            try{
                $dId = supersetting('deal_dealership_col') ?? '';
                $ar[$dId] = $dealer_id.'__Int';
            }catch(\Exception $e){
            }
            $graph = arrayToGraphQL($ar);
            try {
                $query = $inventoryService->setInventoryDataByCsv($graph,'createInventory');

                $inv = $inventoryService->submitRequest($query);
                $id = @$inv['data']['createInventory']['id'];
            } catch (\Exception $e) {
                $id = null;
            }
        }

        return [$id, $existInventoryIds];
    }
    public function setFilter($value,$key)
    {
        list($type,$col) = convertStringToArray('__', $key);
        $filters = [
            "filters" => [
                "column" => $col,
                "value" => $value,
                "order" => "equals",
            ],
        ];

        return $filters;
    }
}
