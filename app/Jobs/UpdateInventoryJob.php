<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api\InventoryService;

class UpdateInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $allImages;
    public $id;
    public $featuredFile;
    /**
     * Create a new job instance.
     */
    public function __construct($featuredFile, $id, $allImages)
    {
        $this->featuredFile = $featuredFile;
        $this->id = $id;
        $this->allImages = $allImages;
    }

    /**
     * Execute the job.
     */
    public function handle(InventoryService $inventoryService): void
    {
        $featured_id = 1232423423234;
        if ($this->featuredFile && !empty($this->featuredFile)) {
            try {
                // $file = !empty($this->featuredFile) ? $this->featuredFile : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ718nztPNJfCbDJjZG8fOkejBnBAeQw5eAUA&s";
                $query = $inventoryService->createFileQuery($this->featuredFile);
                $result = $inventoryService->submitRequest($query);
                $featured_id = $result['data']['createFile']['id'] ?? $featured_id;
            } catch (\Exception $e) {
            }
        }

        try {
            $query = $inventoryService->updateQuery($this->id, $this->allImages, $featured_id);
            $result = $inventoryService->submitRequest($query);
        } catch (\Exception $e) {

        }

    }
}
