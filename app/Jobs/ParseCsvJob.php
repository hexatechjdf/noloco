<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\MapInvJob;

class ParseCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $files = $this->data['files'];
        $unique = $this->data['unique'];
        $mapping = $this->data['mapping'];
        $locationId = $this->data['locationId'];

        foreach ($files as $csvFile) {
            $rows = $this->parseCsvFile($csvFile);
            foreach ($rows as $fields) {
                dispatch((new MapInvJob($fields,$mapping,$locationId,$unique)))->delay(5);
            }
        }
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

        $data = []; // Initialize data array

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if ($headers && count($headers) == count($row)) {
                    // Remove rows where all values are empty
                    if (!empty(array_filter($row))) {
                        $data[] = array_combine($headers, $row);
                    }
                }
            }

            fclose($handle);
        }

        return $data;
    }
}
