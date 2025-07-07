<?php

namespace App\Jobs\CreditApp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\CreditApp\UpdateContactJob;
use App\Helper\CRM;

class SearchContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $locationId;
    public $payload;
    public $key;
    public $is_tag;
    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data,$locationId,$payload,$key = 'dealscustomerMapping',$is_tag = 'false')
    {
        $this->locationId = $locationId;
        $this->payload = $payload;
        $this->key = $key;
        $this->is_tag = $is_tag;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $url = 'contacts/search';
            $detail = CRM::crmV2Loc(1, $this->locationId, $url, 'post',$this->payload);
            $conId = null;
            if ($detail && property_exists($detail, 'contacts')) {
                $conId = @$detail->contacts[0]->id ?? null;
            }

            UpdateContactJob::dispatch($this->data,$this->key,$this->locationId,$conId,$this->is_tag);
        }catch(\Exception $e){
            \Log::error('Search Contact Job: ');
            \Log::error($e);
        }

    }
}
