<?php

namespace App\Jobs\Import;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use App\Jobs\Import\AnalyseFileJob;

class CheckFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public $locId;
    public $title;
    public $fileType;
    public $files;
    public $username;
    public $unique;
    public $mapping;

    /**
     * Create a new job instance.
     */
    public function __construct($locId, $title, $fileType,$files,$username,$mapping,$unique)
    {
        $this->locId = $locId;
        $this->title = $title;
        $this->fileType = $fileType;
        $this->files = $files;
        $this->username = $username;
        $this->mapping = $mapping;
        $this->unique = $unique;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $locationId = $this->locId;
        $fName = $this->title;
        $type = $this->fileType;

        if(in_array($this->files,$fName))
        {
            $p = $this->username . '/' . $fname;
            $sourcePath = base_path('../csvfiles/' . $p);

            $relativePath = 'app/csvfiles/' . $this->username;
            $storagePath = public_path($relativePath);

            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0755, true);
            }

            $destinationPath = $storagePath . '/' . $fname;
            File::copy($sourcePath, $destinationPath);

            dispatch((new AnalyseFileJob($locationId,$destinationPath,$this->mapping, $this->unique,$this->fileType)))->delay(5);


        }
    }
}
