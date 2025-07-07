<?php

namespace App\Jobs\CreditApp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\CreditApp\AddTagsJob;
use App\Helper\CRM;

class UpdateContactJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deal;
    public $type;
    public $locationId;
    public $conId;
    public $is_tag;
    /**
     * Create a new job instance.
     */
    public function __construct($deal, $type='dealscustomerMapping',$locationId=null,$conId = null,$is_tag = 'false')
    {
        $this->deal = $deal;
        $this->type = $type;
        $this->conId = $conId;
        $this->locationId = $locationId;
        $this->is_tag = $is_tag;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if($this->conId)
        {
            $url = 'contacts/'.$this->conId;
            $method = 'put';
        }else{
            $url = 'contacts';
            $method = 'post';
        }
        $mapping =  json_decode(supersetting($this->type), true) ?? [];

        $newArray = [];
        $newData = [];
        try{
            foreach ($mapping as $key => $value) {
                if (isset($value['column'])) {
                    $newKey = trim($value['column'], '{}');
                    $newData[$newKey] = $this->getValueFromObject($this->deal, $key);
                }
            }
            $newData = array_filter($newData, function ($value) {
                return !is_null($value); // Remove null values
            });

            $payload = $this->setPayload($newData);

            unset($newData['id']);
            $detail = CRM::crmV2Loc(1, $this->locationId, $url, $method,$payload);
            if($this->is_tag == 'true')
            {
                if ($detail && property_exists($detail, 'contact')) {
                    $contactId = @$detail->contact->id ?? null;
                    if($contactId)
                    {
                        AddTagsJob::dispatch($contactId,'New Credit App');
                    }
                }

            }

        }catch(\Exception $e){

        }
    }

    public function setPayload($data) {
        $payload = [];
        $arr = array_keys(defaultContactFields());
        foreach ($data as $key => $d) {
            if (in_array($key, $arr)) {
                $payload[$key] = $d;
            } else {
                $payload['customFields'][] = [
                    'key' => $key,  // Using $key as 'id', change if needed
                    'value' => $d
                ];
            }
        }


        $payload['firstName'] = 'tester';
        // $payload['customFields'][] = ['financedAmount' => 8845.25] ;

        return $payload;
    }

    public function getValueFromObject($object, $path) {
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (is_object($object)) {
                $object = (array) $object;
            }
            if (isset($object[$key])) {
                $object = $object[$key];
            } else {
                return null;
            }
        }
        return $object;
    }
}
