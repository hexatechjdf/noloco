<?php

namespace App\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Import\CheckFileJob;

class GetLocationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public $account;
    public $fols;

    /**
     * Create a new job instance.
     */
    public function __construct($account, $fols)
    {
        $this->account = $account;
        $this->fols = $fols;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $acc = $this->account;
        $folders = $this->fols;

        $mapping = json_decode(@$acc->mapping->content, true) ?? [];
        $locations = json_decode(@$acc->location_id, true) ?? [];
        $unique = $acc->mapping->unique_field;
        $username = $acc->username;
        $files = @$folders[$username] ?? [];
        \Log::info('username');
        \Log::info($username);
        \Log::info('folders');
        \Log::info($folders);
        \Log::info('files');
        \Log::info($files);
        \Log::info('locations');
        \Log::info($locations);

        foreach($locations as $loc)
        {
            dispatch((new CheckFileJob($loc['key'], $loc['value'] . '.csv', $loc['type'],$files,$username,$mapping,$unique)))->delay(5);
        }
    }
}