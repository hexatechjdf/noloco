<?php

namespace App\Jobs\Deals;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateContactByDealStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $con_id;
    /**
     * Create a new job instance.
     */
    public function __construct($con_id)
    {
        $this->con_id = $con_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload['customFields'][] = [
                'key' => 'Deals',
                'value' => ''
            ];
        try{
            $query = 'contacts/'.$conId;
            $detail = CRM::crmV2Loc(1, $this->con_id, $query, 'put',$payload);
        }catch(\Exception $e){

        }
    }
}
