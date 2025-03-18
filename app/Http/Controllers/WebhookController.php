<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helper\CRM;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Jobs\GetDealsJob;

class WebhookController extends Controller
{

    protected $inventoryService;
    protected $dealService;

    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }

    public function ghlContactToNoloco(Request $request)
    {
        $contact = $request->all();
        $contact =  (object)$contact;
        $locationId = @$contact->location['id'] ?? null;
        $contactId = @$contact->contact_id ?? null;
        $tags = explode(',', (@$contact->tags ?? '')) ?? [];
        if(in_array('deals',$tags))
        {
            if($locationId && $contactId)
            {
                $contact =  $this->dealService->getContact($locationId,$contactId);
                $contact =  (object)$contact;
                // Log::info($contact);
                dispatch((new GetDealsJob($contact,$contactId,$locationId,'customerMapping')))->delay(5);
                dispatch((new GetDealsJob($contact,$contactId,$locationId,'coborrowerMapping')))->delay(5);
            }

            return response()->json(['success',true],200);
        }
    }

    public function nolocoToGhl(Request $request,$type)
    {
        $deal = $request->all();
        $customerMapping = json_decode(supersetting('customerMapping'), true) ?? [];
        $cob = json_decode(supersetting('coborrowerMapping'), true) ?? [];
        $newArray = [];
        $newData = [];
        foreach ($customerMapping as $key => $value) {
            if (isset($value['column'])) {
                $newKey = trim($value['column'], '{}');
                $newData[$newKey] = $this->getValueFromObject($deal, $key);
            }
        }
        $newData = array_filter($newData, function ($value) {
            return !is_null($value); // Remove null values
        });

        unset($newData['id']);

        $conId = @$deal['highlevelClientId'] ?? null;
        $locationId = @$deal['dealershipSubAccountId'] ?? null;
        $payload = $this->setPayload($newData);
        $query = 'contacts/'.$conId;

        $detail = CRM::crmV2Loc(1, $locationId, $query, 'put',$payload);
        return $detail;

        dd($newArray);
        dd($customerMapping);
        foreach($customerMapping as $key => $map)
        {
           dd($key,$map);
        }
        dd($cus,$cob);
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
