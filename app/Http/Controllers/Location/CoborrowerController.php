<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use App\Models\User;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;

class CoborrowerController extends Controller
{
    protected $inventoryService;
    protected $dealService;

    // Constructor to inject the services
    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }
    public function index(Request $request)
    {
        $deal_id = $request->dealId;
        $location_id = $request->locationId;

        return view('locations.coborrowers.index', get_defined_vars());
    }


    public function createCustomer($conId, $dealer_id, $request)
    {
        $filteredData = json_decode(supersetting('customerMapping'), true) ?? [];
        $contact = $this->dealService->getContact($request->locationId,$request->id);
        // $data = (array) $contact;
        // $data['phone'] = '+923244531747';
        // $contact = (object) $data;

        $replacedData = array_reduce(array_keys($filteredData), function ($result, $keyf) use ($filteredData, $contact) {
            $value = $filteredData[$keyf];

            $updatedData = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($contact, $keyf, &$result) {
                $key = $matches[1];

                if ($key == 'phone' && isset($contact->{$key})) {
                    $phone = $contact->{$key};
                    list($number, $country) = $this->getCountryForPhoneNumber($phone);
                    $updatedString = $this->replaceLastWordAfterDot($keyf, 'country');
                    $result[$updatedString] = $country;
                    return $number;
                }
                return isset($contact->{$key}) ? $contact->{$key} : '';
            }, $value);

            $result[$keyf] = $updatedData;

            return $result;
        }, []);

        $replacedData["dealershipId"] = "%c";
        $payload = [];
        foreach ($replacedData as $key => $value) {
            if (strpos($key, '.') !== false) {
                $keys = explode('.', $key);
                $object = $keys[0];
                $variable = $keys[1];

                if (!isset($payload[$object])) {
                    $payload[$object] = [];
                }
                $payload[$object][$variable] = $value;
            } else {
                $payload[$key] = $value;
            }
        }

        $graphqlPayload = $this->arrayToGraphQL($payload);

        $query = $this->dealService->setCustomerCreateQuery($graphqlPayload, $dealer_id);
        $data = $this->inventoryService->submitRequest($query, 1);
        $customer_id = @$data['data']['createCustomers']['id'];

        return $customer_id;
    }

    private function arrayToGraphQL($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Recursively handle nested objects
                $nested = $this->arrayToGraphQL($value);
                $result[] = "$key: { $nested }";
            } else {
                // Escape string values and format
                $result[] = "$key: \"$value\"";
            }
        }

        return implode(', ', $result);
    }

    public function getCustomer(Request $request)
    {
        $customers = [];
        $conId = $request->id;
        $locId = $request->locationId;
        $customer_id = null;

        try {
            $data = $this->dealService->getCustomerInfo($request);
            $res = $data['data']['customersCollection'];
            if (isset($res['edges']) && count($res['edges']) > 0) {
                $customer_id = $res['edges'][0]['node']['id'];
            } else {
                // $customer_id = 10;
                list($dealer_id,$dealership) =  $this->dealService->getDealership($request,$conId);
                if ($dealer_id) {
                    $customer_id = $this->createCustomer($conId, $dealer_id, $request);
                }
            }
        } catch (\Exception $e) {
            // dd($e);
        }
        return response()->json(['customer_id' => $customer_id]);
    }

    public function setDeal(Request $request)
    {
        try {
            $query = $this->dealService->updateDealQuery($request->customer_id, $request->dealId);
            $data = $this->inventoryService->submitRequest($query, 1);
        } catch (\Exception $e) {
            return response()->json(['error' => 'There is something wrong with deal id']);
        }
        return response()->json(['success' => 'Successfully Updated']);
    }

    public function getCountryForPhoneNumber($phoneNumber, $defaultRegion = 'PK')
    {
        // Create an instance of PhoneNumberUtil
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse the phone number
            $numberProto = $phoneUtil->parse($phoneNumber);

            // Get the region (country code)
            $regionCode = $phoneUtil->getRegionCodeForNumber($numberProto);

            return [$numberProto->getNationalNumber(), $regionCode]; //] Returns country code like 'PK' for Pakistan
        } catch (\libphonenumber\NumberParseException $e) {
            return [null, null];
        }
    }

    public function formatPhoneNumberWithCountryCode($phoneData)
    {
        // Check if phone data is in the correct format
        if (is_object($phoneData)) {
            $phoneData = (array) $phoneData;
        }
        if (!isset($phoneData['number']) || !isset($phoneData['country'])) {
            return 'Invalid phone data.';
        }

        $phoneNumber = $phoneData['number'];
        $countryCode = $phoneData['country'];

        // Create an instance of PhoneNumberUtil
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse the phone number
            $numberProto = $phoneUtil->parse($phoneNumber, $countryCode);

            // Format the number in international format
            $formattedNumber = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);

            return $formattedNumber; // Example: +17867867867
        } catch (\libphonenumber\NumberParseException $e) {
            return 'Invalid Number: ' . $e->getMessage();
        }
    }

    public function replaceLastWordAfterDot($string, $replacement)
    {
        return preg_replace('/\.(\w+)$/', '.' . $replacement, $string);
    }
    public function contactsSearch(Request $request)
    {
        //this code is only useable if need to store locations in database or connect with already saved locations in database using agency token
        $user = User::where('id', 1)->first();
        $token = $user->crmauth ?? null;
        $locationId = $request->locationId;
        $status = false;
        $message = 'Connect to Agency First';
        $type = '';
        $detail = '';
        $load_more = false;
        $query = '';
        $limit = 100;
        if ($request->term) {
            $query = '&query=' . $request->term;
        }

        $query = 'contacts/?locationId=' . $locationId . $query . '&limit=' . $limit;
        $detail = CRM::crmV2Loc(1, $locationId, $query, 'get');

        // $detail = CRM::crmV2($user->id, $query, 'get', '', [], false, $token->location_id);
        $contacts = [];
        if ($detail && property_exists($detail, 'contacts')) {
            $detail = $detail->contacts;
            foreach ($detail as $det) {
                $contacts[] = ['name' => $det->contactName, 'id' => $det->id];
            }
        }

        return response()->json($contacts);

        return response()->json(['status' => $status, 'message' => $message]);

    }
}
