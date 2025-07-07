<?php

namespace App\Jobs\Dealers;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;
use App\Jobs\Dealers\StoreInventoriesJobs;

class ProcessDealersDataJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dealer;
    /**
     * Create a new job instance.
     */
    public function __construct($dealer)
    {
        $this->dealer = $dealer;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService): void
    {
        $locationId = $this->dealer->location_id;

        try {
            list($inventories,$filters,$statusCounts) = $this->getInvntoryListing($inventoryService,$locationId ?? 'geAOl3NEW1iIKIWheJcj');
            StoreInventoriesJobs::dispatch($inventories,$filters,$locationId,$statusCounts,$this->dealer->id);
        } catch (\Exception $e) {
        }

    }

    public function getInvntoryListing($inventoryService,$locationId)
    {
        $allEdges = [];
        $after = null;

        $request = request();
        $filterCounts = [
            'make' => [],
            'exteriorColor' => [],
            'bodyStyle' => [],
        ];
        $statusCounts = [
            'active' => 0,
            'inactive' => 0,
        ];

        $filters = setFilters($locationId);
        do {
            $query = $inventoryService->setQuery($request,null,$filters);
            $data = $inventoryService->submitRequest($query);
            $data = @$data['data'];
            $edges = @$data['inventoryCollection']['edges'];
            if (!empty(@$edges)) {
                foreach ($edges as $edge) {

                    $status = strtolower($node['status'] ?? '');
                    if (in_array($status, ['active', 'inactive'])) {
                        $statusCounts[$status]++;
                    }

                    $node = $edge['node'] ?? [];
                    // Count Make
                    if (!empty($node['make'])) {
                        $make = $node['make'];
                        $filterCounts['make'][$make] = ($filterCounts['make'][$make] ?? 0) + 1;
                    }

                    // Count Exterior Color
                    if (!empty($node['exteriorColor'])) {
                        $color = $node['exteriorColor'];
                        $filterCounts['exteriorColor'][$color] = ($filterCounts['exteriorColor'][$color] ?? 0) + 1;
                    }

                    // Count Body Style
                    if (!empty($node['bodyStyle'])) {
                        $style = $node['bodyStyle'];
                        $filterCounts['bodyStyle'][$style] = ($filterCounts['bodyStyle'][$style] ?? 0) + 1;
                    }
            }
                $allEdges = array_merge($allEdges, $data['inventoryCollection']['edges']);
            }

            $pageInfo = $data['inventoryCollection']['pageInfo'] ?? [];
            $after = $pageInfo['hasNextPage'] ? $pageInfo['endCursor'] : false;
            $request['after'] = $after;
        } while ($after);

        return [$allEdges, $filterCounts, $statusCounts];

    }
}
