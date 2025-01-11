<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CrmAuths;
use App\Helper\CRM;

class UploadGhlMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $image;
    public $token;

    public $retryCount;
    /**
     * Create a new job instance.
     */
    public function __construct($image, $token, $retryCount = 3)
    {
        $this->image = $image;
        $this->token = $token;
        $this->retryCount = $retryCount;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if ($this->token && $this->retryCount > 0) {
            try {
                $data = [
                    'hosted' => true,
                    'fileUrl' => $this->image,
                ];
                $res = CRM::crmV2Loc($this->token->user_id, $this->token->location_id, 'medias/upload-file', 'POST', $data, $this->token);
                try {


                } catch (\Exception $e) {
                    static::dispatch($this->image, $this->token, $this->retryCount - 1)->delay(5);
                }
            } catch (\Exception $e) {

            }
        }
    }
}
