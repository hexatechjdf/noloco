<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CsvMapping;
use App\Jobs\Export\GetExportLocationsJob;

class GetExportMappingJob implements ShouldQueue
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
        $maps = CsvMapping::whereHas('outboundAccount')->where('type','export')->get();
        foreach($maps as $map)
        {
            $ac = $map->outboundAccount;
            dispatch((new GetExportLocationsJob($ac,$map)))->delay(5);
        }
    }
}
