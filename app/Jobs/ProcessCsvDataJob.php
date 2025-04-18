<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;

class ProcessCsvDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 300;
    public $fields;
    public $mapping;
    public $unique;
    /**
     * Create a new job instance.
     */
    public function __construct($fields,$mapping,$unique)
    {
        $this->fields = $fields;
        $this->mapping = $mapping;
        $this->unique = $unique;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService): void
    {
        $val = $this->fields[$this->unique];
        $key = $this->mapping[$this->unique];
        $invType = 'createInventory';
        $filters = $this->setFilter($val,$key);
        $id = $this->isExist($key,$val,$filters,$inventoryService);
        $data = [];
        foreach ($this->fields as $key => $field) {
        // Map CSV headers to fields based on the mapping
            if (isset($this->mapping[$key])) {
                list($type,$value) = convertStringToArray('__', $this->mapping[$key]);
                $data[$value] = $field.'__'.$type;
            }
        }
        if($id)
        {
            $data['id'] = $id.'__Int';
            $invType = 'updateInventory';
        }
        $data = arrayToGraphQL($data);
        try {
            $query = $this->inventoryService->setInventoryDataByCsv($data,$invType);
            $data = $this->inventoryService->submitRequest($query);
        } catch (\Exception $e) {

        }
    }


    public function setFilter($value,$key)
    {
        list($type,$col) = convertStringToArray('__', $key);

        // $col = 'id';
        // $value = '10';
        // $res = checkValueByType($type,$key,$value);
        // list($r1,$r2) = convertStringToArray(': ', $res);
        $filters = [
            "filters" => [
                "column" => $col,
                "value" => $value,
                "order" => "equals",
            ],
        ];

        return $filters;
    }

    public function isExist($key,$value,$filters,$inventoryService)
    {
        $request = request();
        $request->merge(['filters' => $filters]);
        $id = null;
        try {
            $query = $inventoryService->setQuery($request);
            $data = $inventoryService->submitRequest($query);
            $id = @$data['data']['inventoryCollection']['edges'][0]['node']['id'];

        } catch (\Exception $e) {

        }
        return $id;
    }
}
