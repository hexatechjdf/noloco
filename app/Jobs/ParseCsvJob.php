<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\MapInvJob;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use Illuminate\Support\Facades\Log;
use App\Jobs\UpdateMapInvJob;

class ParseCsvJob implements ShouldQueue
{
    // 2
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService,DealService $dealService): void
    {
        $files = $this->data['files'];
        $unique = $this->data['unique'];
        $mapping = $this->data['mapping'];
        $locationId = $this->data['locationId'];



        foreach ($files as $csvFile) {
            $rows = $this->parseCsvFile($csvFile);
            $key = @$mapping[$unique];
            $existInventoryIds = $this->inventoryIds($locationId,$inventoryService,$dealService,strtolower($unique));
            $arr = $existInventoryIds;
            $rowStocks = [];

            foreach ($rows as $fields) {

                dispatch((new MapInvJob($fields,$mapping,$locationId,$unique,$existInventoryIds)))->delay(5);

                $rowStocks[] = @$fields[$unique] ?? null;
            }


Log::info($arr, $rowStocks);
            if(count($existInventoryIds) > 0)
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
    }

         /**
     * Parse the CSV file and map data to headers.
     *
     * @param string $filePath
     * @return array
     */

    private function parseCsvFile($filePath)
    {
        $filePath = public_path(trim($filePath));
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $data = []; // Initialize data array

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if ($headers && count($headers) == count($row)) {
                    // Remove rows where all values are empty
                    if (!empty(array_filter($row))) {
                        $data[] = array_combine($headers, $row);
                    }
                }
            }

            fclose($handle);
        }

        return $data;
    }



    public function inventoryIds($locationId,$inventoryService,$dealService,$key)
    {
        $request = request();
        $stockids = [];
        $id = null;
        try {
            $query = $inventoryService->setQueryInventoryIds($request,$locationId);
            $data = $inventoryService->submitRequest($query);
            Log::info($data );
            $stocks = @$data['data']['inventoryCollection']['edges'];
            if($stocks)
            {
                foreach($stocks as $stock)
                {
                    $s = $stock['node'];
                    if(@$s[$key] && @$s['id'])
                    {
                        $stockids[@$s['id']] = @$s[$key];
                    }
                }
            }

        } catch (\Exception $e) {
            Log::info($e);
        }
        return $stockids;
    }


}
