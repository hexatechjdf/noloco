<?php

namespace App\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use Illuminate\Support\Facades\Log;
use App\Jobs\UpdateMapInvJob;

class AnalyseFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 3
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 300;
    public $fields;
    public $mapping;
    public $locationId;
    public $unique;
    public $existInventoryIdss;
    public $type;
    /**
     * Create a new job instance.
     */
    public function __construct($fields,$mapping,$locationId,$unique,$existInventoryIdss,$type)
    {
        $this->fields = $fields;
        $this->mapping = $mapping;
        $this->locationId = $locationId;
        $this->unique = $unique;
        $this->existInventoryIdss = $existInventoryIdss;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService,DealService $dealService): void
    {
        $val = @$this->fields[$this->unique];
        $key = @$this->mapping[$this->unique];

        Log::info('key');
        Log::info($key);
        Log::info('Value');
        Log::info($val);
        Log::info('mapping');
        Log::info($this->mapping);
        Log::info('unique');
        Log::info($this->unique);
        Log::info('fields');
        Log::info($this->fields);
        if($key && $val)
        {
            $invType = 'createInventory';
            $filters = $this->setFilter($val,$key['column']);
            try{
                list($dealer_id,$dealership) =  $dealService->getDealership(request(),$this->locationId);
            }catch(\Exception $e){
            }

            list($id, $existInventoryIds,$is_new) = $this->isExist($key,$val,$filters,$inventoryService,$dealService,$dealer_id,$this->existInventoryIdss,$this->unique);
            $t = true;
            if($this->type == 'manual' && !$is_new)
            {
                $t = false;
            }
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

            \Log::info("id");
            \Log::info($id);
            \Log::info("type");
            \Log::info($this->type);

            if($id && $t)
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
        $is_new = true;
        if(in_array($value, $existInventoryIds))
        {
            $is_new = false;
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

        return [$id, $existInventoryIds,$is_new];
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
