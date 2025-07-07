<?php

namespace App\Jobs\CreditApp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper\CRM;

class AddTagsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $conId;
    public $key;
    /**
     * Create a new job instance.
     */
    public function __construct($conId,$key)
    {
        $this->conId = $conId;
        $this->key = $key;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $payload = [
                        'tags' => [
                            $this->key
                        ],
                    ];
            $url = 'contacts/'.$conId.'/tags';
            $detail = CRM::crmV2Loc(1, $locationId, $url, 'post',$payload);
        }catch(\Exception $e){

        }
    }
}
