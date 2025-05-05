<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Jobs\Export\UploadExportCsvJob;

class SetExportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ftp_account;
    public $location;
    public $fields;
    public $title;
    /**
     * Create a new job instance.
     */
    public function __construct($ftp_account,$location,$title,$fields)
    {
        $this->ftp_account = $ftp_account;
        $this->location = $location;
        $this->fields = $fields;
        $this->title = $title;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService,DealService $dealService): void
    {
        $loc = $this->location;
        $allEdges = $this->getList($loc,$inventoryService,$dealService);
        $filename = 'inventory_export_'. $this->title .'_' . now()->format('Ymd_His') . '.csv';
        $filePath = storage_path('app/public/export/' . $filename);

        // Create directory if not exists
        if (!file_exists(storage_path('app/public/export'))) {
            mkdir(storage_path('app/public/export'), 0755, true);
        }

        $file = fopen($filePath, 'w');

        fputcsv($file, array_values($this->fields));
        foreach ($allEdges as $item)
        {
            $node = $item['node'];
            $row = [];

            foreach ($this->fields as $key => $header) {
                $row[] = $node[$key] ?? '';
            }
            fputcsv($file, $row);
        }
        fclose($file);

        dispatch((new UploadExportCsvJob($filePath,$filename,$this->ftp_account)))->delay(5);
    }

    public function getList($locationId,$inventoryService,$dealService)
    {
        $allEdges = [];
        $after = null;
        $filters = [
            "filters" => [
                "column" => 'dealershipSubAccountId',
                "value" => $locationId,
                "order" => "equals",
            ],
            "after" => $after,
        ];
        $request = request();
        $request->merge(['filters' => $filters]);
        do {
            $query = $inventoryService->setQuery($request);
            $data = $inventoryService->submitRequest($query);
            $data = @$data['data'];

            if (!empty(@$data['inventoryCollection']['edges'])) {
                $allEdges = array_merge($allEdges, $data['inventoryCollection']['edges']);
            }

            $pageInfo = $data['inventoryCollection']['pageInfo'] ?? [];
            $after = $pageInfo['hasNextPage'] ? $pageInfo['endCursor'] : false;
            $request['after'] = $after;
        } while ($after);

        return $allEdges;
    }
}
