<?php

namespace App\Jobs\Dealers;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Dealer;
use App\Jobs\Dealers\ProcessDealersDataJobs;

class DispatchDealersJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Dealer::chunk(50, function ($deals) {
            foreach ($deals as $deal) {
                ProcessDealersDataJobs::dispatch($deal);
            }
        });
    }
}
