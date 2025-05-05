<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FtpAccount;
use Illuminate\Support\Facades\File;
use App\Jobs\ParseCsvJob;

class GetFoldersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    // 1
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $folders = $this->getFolders();
        foreach($folders as $folder => $data)
        {
            dispatch((new ParseCsvJob($data)))->delay(5);
        }
    }

    public function getFolders()
    {
        $basePath = base_path('../csvfiles'); // Parent folder containing multiple folders
        $folders = File::directories($basePath); // Get all subdirectories
        $csvFolders = [];

        foreach ($folders as $folder) {
            $folderName = basename($folder); // Get folder name

            $acc = FtpAccount::with('mapping')
            ->where('location_id','!=',null)
            ->where('username',$folderName)
            ->select('username', 'mapping_id', 'id','location_id')
            ->first();

            if($acc)
            {
                $mapping = json_decode(@$acc->mapping->content, true) ?? [];
                $locationId =  $acc->location_id;
                $unique = $acc->mapping->unique_field;

                $files = File::files($folder); // Get all files in the folder
                $csvFiles = [];
                $path = 'app/csvfiles/'.$folderName.'/';
                $storagePath = public_path($path.$folderName);
                if (!File::exists($storagePath)) {
                    File::makeDirectory($storagePath, 0755, true);
                }

                foreach ($files as $file) {
                    $file_name = $file->getFilename();
                    if ($file->getExtension() === 'csv' && !str_contains($file_name, 'export')) {
                        $newFileName = $locationId . '_' . now()->format('YmdHis') . '_' . $file_name;
                        $newFilePath = $storagePath . '/' . $newFileName;
                        File::copy($file->getPathname(), $newFilePath);
                        $csvFiles[] = $path. $folderName.'/' . $newFileName;
                    }
                }

                if (!empty($csvFiles)) {
                    $csvFolders[$folderName] = ['unique' => $unique,'mapping' => $mapping, 'files' => $csvFiles,'locationId' => $locationId];
                }
            }
        }
        return  $csvFolders;
    }
}
