<?php

namespace App\Jobs\Dealers;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Inventory;

class StoreInventoriesJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $inventories;
    public $filters;
    public $locationId;
    public $statusCounts;
    public $dealer_id;
    /**
     * Create a new job instance.
     */
    public function __construct($inventories,$filters,$locationId,$statusCounts,$dealer_id)
    {
        $this->inventories = $inventories;
        $this->filters = $filters;
        $this->locationId = $locationId;
        $this->statusCounts = $statusCounts;
        $this->dealer_id = $dealer_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Inventory::updateOrCreate([
            'location_id' => $this->locationId
        ],[
            'content' => json_encode($this->inventories),
            'filters' => json_encode($this->filters),
            'active_items' => @$this->statusCounts['active'],
            'inactive_items' => @$this->statusCounts['inactive'],
            'dealer_id' => $this->dealer_id,
        ]);
    }
}
