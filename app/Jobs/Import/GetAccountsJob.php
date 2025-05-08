<?php

namespace App\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Import\GetLocationsJob;
use App\Models\FtpAccount;
use Illuminate\Support\Facades\File;

class GetAccountsJob implements ShouldQueue
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
        $accounts = FtpAccount::with('mapping')
        ->where('location_id','!=',null)
        ->select('username', 'mapping_id', 'id','location_id')
        ->get();

        $folders = $this->getFolders();
        foreach($accounts as $acc)
        {
            dispatch((new GetLocationsJob($acc,$folders)))->delay(5);
        }
    }

    public function getFolders()
    {
        $basePath = base_path('../csvfiles');
        $directories = File::directories($basePath);
        $result = [];

        foreach ($directories as $dirPath) {
            $dirName = basename($dirPath); // Get folder name only
            $csvFiles = [];

            // Get all .csv files in this directory
            foreach (File::files($dirPath) as $file) {
                if ($file->getExtension() === 'csv') {
                    $csvFiles[] = $file->getFilename();
                }
            }

            if (!empty($csvFiles)) {
                $result[$dirName] = $csvFiles;
            }
        }
        return $result;
    }
}
