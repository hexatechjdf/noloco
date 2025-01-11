<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\UploadGhlMediaJob;
use App\Models\CrmAuths;
use App\Helper\CRM;
class CollectAllImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $images;
    public $location_id;
    /**
     * Create a new job instance.
     */
    public function __construct($images, $location_id)
    {
        $this->images = $images;
        $this->location_id = $location_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = CRM::getCrmToken(['location_id' => $this->location_id]);
        foreach ($this->images as $image) {
            dispatch((new UploadGhlMediaJob($image, $token)))->delay(5);
        }
    }
}
