<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FtpAccount;
use App\Jobs\ProcessCsvDataJob;
use Illuminate\Support\Facades\File;

class ProcessCsvFoldersJob implements ShouldQueue
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
        $folders = $this->getFolders();
        foreach($folders as $folder => $data)
        {
               $files = $data['files'];
               $unique = $data['unique'];
               $mapping = $data['mapping'];
                foreach ($files as $csvFile) {
                    $rows = $this->parseCsvFile($csvFile);
                    foreach ($rows as $fields) {
                        dispatch((new ProcessCsvDataJob($fields,$mapping,$unique)))->delay(5);
                    }
            }
        }
    }

    public function getFolders()
    {
        $basePath = base_path('../csvfiles'); // Parent folder containing multiple folders
        $folders = File::directories($basePath); // Get all subdirectories
        $csvFolders = [];

        foreach ($folders as $folder) {
            $folderName = basename($folder); // Get folder name

            $acc = FtpAccount::with('mapping', 'location')
            ->where('username',$folderName)
            ->select('username', 'mapping_id', 'id')
            ->first();

            if($acc)
            {
                $mapping = json_decode(@$acc->mapping->content, true) ?? [];
                $locationId = @$acc->location->location_id ?? '';
                $unique = $acc->mapping->unique_field;

                $files = File::files($folder); // Get all files in the folder
                $csvFiles = [];
                $path = 'app/csvfiles/';
                $storagePath = public_path($path.$folderName);
                if (!File::exists($storagePath)) {
                    File::makeDirectory($storagePath, 0755, true);
                }

                foreach ($files as $file) {
                    if ($file->getExtension() === 'csv') {
                        $newFileName = $locationId . '_' . now()->format('YmdHis') . '_' . $file->getFilename();
                        $newFilePath = $storagePath . '/' . $newFileName;
                        File::move($file->getPathname(), $newFilePath);
                        $csvFiles[] = $path. $folderName.'/' . $newFileName;
                    }
                }

                if (!empty($csvFiles)) {
                    $csvFolders[$folderName] = ['unique' => $unique,'mapping' => $mapping, 'files' => $csvFiles];
                }
            }
        }
        return  $csvFolders;
    }

    /**
     * Parse the CSV file and map data to headers.
     *
     * @param string $filePath
     * @return array
     */
    private function parseCsvFile($filePath)
    {
        $filePath = public_path(trim($filePath));
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if ($headers && count($headers) == count($row)) {
                    $data[] = array_combine($headers, $row);
                }
            }

            fclose($handle);
        }

        return $data;
    }
}
