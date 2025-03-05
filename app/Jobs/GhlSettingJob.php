<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GhlSettingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $type;
    public $deal;

    /**
     * Create a new job instance.
     */
    public function __construct($deal,$type)
    {
        $this->deal = $deal;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $m = $this->type == 'customer' ? 'customerMapping' : 'coborrowerMapping';
        $mapping = json_decode(supersetting($m), true) ?? [];
        $newData = [];
        foreach ($mapping as $key => $value) {
            if (isset($value['column'])) {
                $newKey = trim($value['column'], '{}');
                $newData[$newKey] = $this->getValueFromObject($this->deal, $key);
            }
        }
        $newData = array_filter($newData, function ($value) {
            return !is_null($value); // Remove null values
        });

        unset($newData['id']);

        $conId = @$this->deal['highlevelClientId'] ?? null;
        $locationId = @$this->deal['dealershipSubAccountId'] ?? null;
        $payload = $this->setPayload($newData);
        $query = 'contacts/'.$conId;

        $detail = CRM::crmV2Loc(1, $locationId, $query, 'put',$payload);
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
}
