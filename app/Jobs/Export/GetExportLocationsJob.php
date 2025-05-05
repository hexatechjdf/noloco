<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Export\SetExportCsvJob;

class GetExportLocationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ftp_account;
    public $mapping;
    /**
     * Create a new job instance.
     */
    public function __construct($ftp_account,$mapping)
    {
        $this->ftp_account = $ftp_account;
        $this->mapping = $mapping;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ac = $this->ftp_account;
        $map = $this->mapping;
        $locations = json_decode($ac->location_id, true) ?? [];
        $fields = json_decode($map->content, true) ?? [];

        foreach($locations as $loc as $title)
        {
            dispatch((new SetExportCsvJob($ac,$loc,$title,$fields)))->delay(5);
        }
    }
}
