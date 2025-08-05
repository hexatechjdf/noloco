<?php

namespace App\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use Illuminate\Support\Facades\Log;
use App\Jobs\Import\AnalyseFeedJob;
use App\Jobs\Import\SetExtraInvJob;

class AnalyseFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public $locId;
    public $type;
    public $dPath;
    public $unique;
    public $mapping;
    /**
     * Create a new job instance.
     */
    public function __construct($locId, $dPath,$mapping,$unique,$type)
    {
        $this->locId = $locId;
        $this->dPath = $dPath;
        $this->mapping = $mapping;
        $this->unique = $unique;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService,DealService $dealService): void
    {
        $destinationPath = $this->dPath;
        $locationId = $this->locId;

        if (File::exists($destinationPath)) {
            // run jobb
            $rows = $this->parseCsvFile($destinationPath);
            $key = @$this->mapping[$this->unique];
            $existInventoryIds = $this->inventoryIds($locationId,$inventoryService,$dealService,strtolower($this->unique));
            if($existInventoryIds = 'no')
            {
                return;
            }
            $rowStocks = [];
            foreach($rows as $fields)
            {
                dispatch((new AnalyseFeedJob($fields,$this->mapping,$locationId,$this->unique,$existInventoryIds,$this->type)))->delay(5);
                $rowStocks[] = @$fields[$this->unique] ?? null;
            }

            if(count($existInventoryIds) > 0 && $this->type != 'manual')
            {
                dispatch((new SetExtraInvJob($existInventoryIds,$rowStocks)))->delay(5);
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
        //  $filePath = asset(trim($filePath));
        //  if (!file_exists($filePath)) {
        //      throw new \Exception("File not found: " . $filePath);
        //  }

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
             return 'no';
             Log::info($e);
         }
         return $stockids;
     }
}
