<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helper\CRM;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Jobs\GetDealsJob;
use App\Models\Dealer;
use App\Jobs\Deals\UpdateContactByDealStatusJob;
use App\Jobs\CreditApp\ProcessApplicationJob;

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
        $is_deal = @$contact->Deal ?? "";
        // $tags = explode(',', (@$contact->tags ?? '')) ?? [];
        if($is_deal && !empty($is_deal) && $is_deal != "")
        {
            if($locationId && $contactId)
            {
                $contact =  $this->dealService->getContact($locationId,$contactId);
                $contact =  (object)$contact;
                // Log::info($contact);
                dispatch((new GetDealsJob($contact,$contactId,$locationId,'dealscustomerMapping')))->delay(5);
                dispatch((new GetDealsJob($contact,$contactId,$locationId,'dealscoborrowerMapping')))->delay(5);
            }

            return response()->json(['success',true],200);
        }
    }

    public function updateContactByDeal(Request $request)
    {
        $deal = $request->all();
        $s = @$deal['dealStatus'] ?? null;

        if($s && !empty($s) && $s != 'OPEN')
        {
            $conId = @$deal['highlevelClientId'] ?? null;
            $locationId = @$deal['dealershipSubAccountId'] ?? null;

            if(@$conId && @$locationId)
            {
                dispatch((new UpdateContactByDealStatusJob($conId, $locationId )))->delay(5);
            }
        }

        return true;
    }

    public function nolocoToGhl(Request $request)
    {
        $deal = $request->all();

        dispatch((new UpdateContactJob($deal,'dealscustomerMapping')))->delay(5);
        dispatch((new UpdateContactJob($deal,'dealscoborrowerMapping')))->delay(5);

        return true;

        $customerMapping =  json_decode(supersetting('customerMapping'), true) ?? [];
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

    public function storeDealer(Request $request)
    {
        $data = $request->all();
        $dealer = Dealer::updateOrCreate(
            ['location_id' => $data['subAccountId']], // ya 'uuid' if you use uuid as unique key
            [
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phoneApi'] ?? data_get($data, 'phoneNumber._root'),
                'address' => $data['addressApi'] ?? data_get($data, 'streetAddress._root'),
                'postal_address' => data_get($data, 'streetAddress.postalCode'),
                'country' => data_get($data, 'streetAddress.country'),
                'city' => data_get($data, 'streetAddress.city'),
                'latitude' => data_get($data, 'streetAddress.latitude'),
                'longitude' => data_get($data, 'streetAddress.longitude'),
                'role' => 'dealer', // default role or as needed
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Dealer saved successfully',
            'dealer' => $dealer,
        ]);
    }

    public function creditAppHandle(Request $request)
    {
        $data = $request->all();

        // $data = [
        //     "uuid" => "recc29uas8m7jw4wkw",
        //     "createdAt" => "2025-02-25T02:52:00.183Z",
        //     "id" => 3,
        //     "updatedAt" => "2025-07-07T10:23:18.657Z",
        //     "subscribedNotificationUserIds" => [],
        //     "address" => [
        //         "street" => "4454 Davidson Road",
        //         "suiteAptBldg" => "",
        //         "city" => "Hilliard",
        //         "stateRegion" => "Ohio",
        //         "postalCode" => "43026",
        //         "country" => "United States",
        //         "latitude" => 40.0515517,
        //         "longitude" => -83.1380817,
        //         "_root" => "4454 Davidson Road, Hilliard, Ohio, 43026, United States"
        //     ],
        //     "applicationType" => "JOINT_APPLICATION_CO_BORROWER",
        //     "creditAcknowledgment" => true,
        //     "dateOfBirth" => "1986-03-18T00:00:00.000Z",
        //     "dealerGhlId" => "GOsZwMqjkYVyCJGEufIQ",
        //     "dealershipId" => 10,
        //     "downPayment" => 1000,
        //     "emailAddress" => "kdoskdd@dkosdks.com",
        //     "employerName" => "dsds",
        //     "employerPhoneNumber" => [
        //         "country" => "US",
        //         "number" => "(434) 343-4433",
        //         "_root" => "+1 (434) 343-4433"
        //     ],
        //     "employmentStatus" => "EMPLOYED",
        //     "fullName" => [
        //         "first" => "Sale",
        //         "middle" => "Motor",
        //         "last" => "Tocak",
        //         "_root" => "Sale Motor Tocak"
        //     ],
        //     "grossIncome" => 2500,
        //     "idState" => "AZ_ARIZONA",
        //     "idType" => "DRIVERS_LICENSE",
        //     "inventoryId" => 26,
        //     "jobMonths" => 2,
        //     "jobPosition" => "dsads",
        //     "jobYears" => 3,
        //     "monthsAtResidence" => 4,
        //     "phoneNumber" => [
        //         "country" => "US",
        //         "number" => "(614) 525-3411",
        //         "_root" => "+1 (614) 525-5255"
        //         // "_root" => "(937) 205-9310"
        //     ],
        //     "residencePayment" => 1000,
        //     "residenceType" => "RENT",
        //     "socialSecurityNumber" => "123524568",
        //     "trade" => "NO",
        //     "yearsAtResidence" => 3,
        //     "hiddenSsn" => "***-**-4568",
        //     "name" => 3,
        //     "reviewed" => true,
        //     "coBorrowerEmail" => "fghfghfg@gmail.com",
        //     "coBorrowerPhone" => [
        //         "country" => "PK",
        //         "number" => "324453174",
        //         "_root" => "+92 324453174"
        //     ],
        //     "coBorrowerFullName" => [
        //         "first" => "sdf",
        //         "middle" => "sdf",
        //         "last" => "sdfsdf",
        //         "_root" => "sdf sdf sdfsdf"
        //     ],
        //     "_dataType" => "creditApps",
        //     "_meta" => [
        //         "user" => [
        //             "uuid" => "rec19g617m490yeid",
        //             "createdAt" => "2024-12-03T22:22:20.148Z",
        //             "id" => 6,
        //             "updatedAt" => "2025-07-07T10:21:30.541Z",
        //             "subscribedNotificationUserIds" => [],
        //             "email" => "saadjdfunnel@gmail.com",
        //             "firstName" => "Saad",
        //             "invitationToken" => "21b6a370-5a73-4ba1-b123-ec21c73a1e0b",
        //             "lastName" => "Mukhtar",
        //             "roleId" => 1,
        //             "dealership" => [10],
        //             "dealershipSubAccountId" => ["GOsZwMqjkYVyCJGEufIQ"],
        //             "dealershipName" => ["Jc Auto"],
        //             "lastActiveAt" => "2025-07-07T10:21:30.541Z",
        //             "testDealership" => ["Jc Auto"],
        //             "dealershipIdVal" => "GOsZwMqjkYVyCJGEufIQ",
        //             "dealershipCount" => 1,
        //             "dealershipCountFilter" => false,
        //             "role" => [
        //                 "id" => 1,
        //                 "uuid" => "rec19g616m48zezz5",
        //                 "createdAt" => "2024-12-03T21:39:15.233Z",
        //                 "updatedAt" => "2025-02-18T01:07:27.414Z",
        //                 "subscribedNotificationUserIds" => [],
        //                 "name" => "Agency Admin",
        //                 "referenceId" => "team-admin",
        //                 "internal" => true,
        //                 "builder" => true,
        //                 "dataAdmin" => true
        //             ],
        //             "_dataType" => "user"
        //         ]
        //     ]
        // ];

         ProcessApplicationJob::dispatch($data,'creditAppscustomerMapping');
        if (@$data['applicationType'] && $data['applicationType'] !== 'SINGLE_APPLICANT') {
            ProcessApplicationJob::dispatch($data,'creditAppscoborrowerMapping');
        }
        return 1;
        dd(123);

        $type = 'creditAppscustomerMapping';
        $phone = $type=='creditAppscustomerMapping' ? @$data['phoneNumber'] : @$data['coBorrowerPhone'];
        $phone = formatPhoneNumberWithCountryCode($phone);
        $email = ($type=='creditAppscustomerMapping' ? @$data['emailAddress'] : @$data['coBorrowerEmail']);

        $locationId = 'geAOl3NEW1iIKIWheJcj' ?? $data['dealerGhlId'];
        $payload = [
            "locationId" => $locationId,
            "page" => 1,
            "pageLimit" => 1,
            "filters" => [
                [

                    "group" => "OR",
                    "filters" => [
                        [
                            "field" => "phone",
                            "operator" => "eq",
                            "value" => $phone,
                        ],
                        [
                            "field" => "email",
                            "operator" => "eq",
                            "value" => $email,
                        ],
                    ],
                ],
            ],
        ];
        $url = 'contacts/search';
        $detail = CRM::crmV2Loc(1, $locationId, $url, 'post',$payload);
        $conId = null;
        if ($detail && property_exists($detail, 'contacts')) {
            $conId = @$detail->contacts[0]->id ?? null;
        }

        if($conId)
        {
            $url = 'contacts/'.$conId;
            $method = 'put';
        }else{
           $url = 'contacts';
            $method = 'post';
        }

        $customerMapping =  json_decode(supersetting('creditAppscustomerMapping'), true) ?? [];


        $newArray = [];
        $newData = [];
        foreach ($customerMapping as $key => $value) {
            if (isset($value['column'])) {
                $newKey = trim($value['column'], '{}');
                $newData[$newKey] = $this->getValueFromObject($data, $key);
            }
        }
        $newData = array_filter($newData, function ($value) {
            return !is_null($value); // Remove null values
        });
        $body = $this->setPayload($newData);
        // dd($method,$url);
        // XqOyrqDOsAB8V1mdtJeP
        // $conId = null;
        $detail = CRM::crmV2Loc(1, $locationId, $url, $method,$body);
        if ($detail && property_exists($detail, 'contact')) {
            $conId = @$detail->contact->id ?? null;
        }
        if($conId)
        {
            $payload = [
                'tags' => [
                    'New Credit App'
                ],
            ];
            $url = 'contacts/'.$conId.'/tags';
            $detail = CRM::crmV2Loc(1, $locationId, $url, 'post',$payload);

            dd($detail);
        }

        dd($detail);
        return 123;

        ProcessApplicationJob::dispatch($deal,'creditAppscustomerMapping');
        if (@$data['applicationType'] && $data['applicationType'] !== 'SINGLE_APPLICANT') {
            ProcessApplicationJob::dispatch($deal,'creditAppscoborrowerMapping');
        }

        return response()->json(['status' => 'received']);

        $customerMapping =  json_decode(supersetting('creditAppscustomerMapping'), true) ?? [];


        $newArray = [];
        $newData = [];
        foreach ($customerMapping as $key => $value) {
            if (isset($value['column'])) {
                $newKey = trim($value['column'], '{}');
                $newData[$newKey] = $this->getValueFromObject($data, $key);
            }
        }
        $newData = array_filter($newData, function ($value) {
            return !is_null($value); // Remove null values
        });

        dd($newData);


        ProcessApplicationJob::dispatch($data,'borrower');

        if (@$data['applicationType'] && $data['applicationType'] !== 'SINGLE_APPLICANT') {
            ProcessApplicationJob::dispatch($data);
        }

        return response()->json(['status' => 'received']);

    }


}
