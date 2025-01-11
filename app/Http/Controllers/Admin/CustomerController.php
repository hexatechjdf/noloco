<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MappingTable;
use App\Models\CoborrowMaping;
use Illuminate\Support\Str;
use App\Helper\CRM;
use Illuminate\Support\Facades\Cache;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;

class CustomerController extends Controller
{

    protected $inventoryService;
    protected $dealService;

    // Constructor to inject the services
    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }
    public function index()
    {
        $items = CoborrowMaping::paginate(10);

        return view('admin.mapings.coborrower.index', get_defined_vars());
    }

    public function form()
    {
        $mapping = json_decode(supersetting('customerMapping'), true) ?? [];
        $columns = $this->getCoborrowerFields();
        $locationId = supersetting('crm_location_id');
        $contact_fileds = CRM::getContactFields($locationId, true);

        return view('admin.mapings.coborrower.form', get_defined_vars());
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
        // Check if there's a dot in the string and replace the last word after it
        return preg_replace('/\.(\w+)$/', '.' . $replacement, $string);
    }

    public function getContact()
    {
        $contact_id = 'Aiml0qxtPRr1fiK5mOf3';
        try {
            $response = CRM::crmV2Loc('2', 'HuVkfWx59Pv4mUMgGRTp', 'contacts/' . $contact_id, 'get');
            return $response->contact;
        } catch (\Exception $e) {
        }
    }
    public function formSubmit(Request $request)
    {
        $filteredData = json_decode(supersetting('customerMapping'), true) ?? [];
        dd($filteredData);
        $nol = new \stdClass();
        $nol->firstName = "John";
        $nol->lastName = "Doe";
        $nol->phone = "1234567890";
        $payload = [];
        $array = [];
        foreach ($filteredData as $key => $value) {
            // Remove the curly braces {{}} and split by the delimiter }}{{
            $value = str_replace(["{{", "}}"], "", $value);
            $variables = explode("}}{{", $value);

            // Initialize a variable to hold the corresponding value from $nol
            $mappedValue = '';

            // Handle the case if the value contains more than one variable (split by commas)
            if (count($variables) > 1) {
                // Concatenate the values if needed (e.g., for fullName as "firstName lastName")
                $mappedValue = '';
                foreach ($variables as $variable) {
                    $mappedValue .= $nol->{$variable}; // Concatenate variables
                }
            } else {
                // If there's only one variable, fetch the corresponding value from $nol
                $mappedValue = isset($nol->{$variables[0]}) ? $nol->{$variables[0]} : '';
            }

            // Now, we handle the key to match the object path
            // Check if there's a dot in the key (indicating object hierarchy)
            if (strpos($key, '.') !== false) {
                // Break the key into parts (e.g., "fullName.title")
                $keyParts = explode('.', $key);

                // Traverse through the object hierarchy
                $temp = $nol;
                foreach ($keyParts as $part) {
                    if (isset($temp->$part)) {
                        $temp = $temp->$part; // Traverse to the next level in the object
                    }
                }

                // Set the value of the final object part in the result
                $payload[$keyParts[count($keyParts) - 1]] = $temp; // The final value in the hierarchy
            } else {
                // If no dot, it's a simple key-value mapping
                $payload[$variables[0]] = $mappedValue; // Map directly to the final key
            }
        }
        dd($payload);


        // dd($filteredData);
        // $contact = $this->getContact();
        // $data = (array) $contact;
        // $data['phone'] = '+923244531747';
        // $contact = (object) $data;

        // $replacedData = array_reduce(array_keys($filteredData), function ($result, $keyf) use ($filteredData, $contact) {
        //     $value = $filteredData[$keyf];

        //     $updatedData = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($contact, $keyf, &$result) {
        //         $key = $matches[1];

        //         if ($key == 'phone' && isset($contact->{$key})) {
        //             $phone = $contact->{$key};
        //             list($number, $country) = $this->getCountryForPhoneNumber($phone);
        //             $updatedString = $this->replaceLastWordAfterDot($keyf, 'country');
        //             $result[$updatedString] = $country;
        //             return $number;
        //         }
        //         return isset($contact->{$key}) ? $contact->{$key} : '';
        //     }, $value);

        //     $result[$keyf] = $updatedData;

        //     return $result;
        // }, []);

        $replacedData = [
            // "name" => "testing1 testing1",
            "fullName.title" => "testing1",
            "fullName.first" => "testing1",
            "fullName.middle" => "testing1",
            "employerPhoneNumber.country" => "PK",
            "employerPhoneNumber.number" => "3244531747",
            "dealershipId" => 10,
        ];


        $payload = [];

        // Process each key-value pair
        foreach ($replacedData as $key => $value) {
            if (strpos($key, '.') !== false) {
                // Split the key into object and variable parts
                $keys = explode('.', $key);
                $object = $keys[0];
                $variable = $keys[1];

                // Assign the value to the corresponding nested object
                if (!isset($payload[$object])) {
                    $payload[$object] = []; // Initialize the object if not already set
                }
                $payload[$object][$variable] = $value;
            } else {
                // Directly assign simple keys
                $payload[$key] = $value;
            }
        }

        // Convert the array into GraphQL structure
        $graphqlPayload = $this->arrayToGraphQL($payload);

        $query = $this->dealService->setCustomerCreateQuery($graphqlPayload);
        $data = $this->inventoryService->submitRequest($query, 1);


        dd($data);

        // $phoneData = [
        //     "number" => "3244531747",
        //     "country" => "PK"
        // ];

        // $formattedNumber = $this->formatPhoneNumberWithCountryCode($phoneData);
        // dd($formattedNumber);


        // list($number, $country) = $this->getCountryForPhoneNumber("+17867867867");
        // dd($number, $country);


        // $data = $request->mapping;
        // $filteredData = array_filter($data, function ($value) {
        //     return !is_null($value);
        // });

        // save_settings('customerMapping', $filteredData);

        // return response()->json(['success' => true, 'route' => route('admin.mappings.customer.form')]);

        // dd($filteredData);

        $contact = (object) [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phone' => '1234567890'
        ];

        // $replacedData = array_map(function ($value) use ($contact) {
        //     return preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($contact) {
        //         $key = $matches[1];
        //         return isset($contact->{$key}) ? $contact->{$key} : '';
        //     }, $value);
        // }, $filteredData);

        // $replacedData = array_map(function ($value) {
        //     return preg_replace('/([a-z])([A-Z])/', '$1 $2', $value);
        // }, $replacedData);
        // $payload = [];

        // // Process each key-value pair
        // foreach ($replacedData as $key => $value) {
        //     if (strpos($key, '.') !== false) {
        //         // Split the key into object and variable parts
        //         $keys = explode('.', $key);
        //         $object = $keys[0];
        //         $variable = $keys[1];

        //         // Assign the value to the corresponding nested object
        //         if (!isset($payload[$object])) {
        //             $payload[$object] = []; // Initialize the object if not already set
        //         }
        //         $payload[$object][$variable] = $value;
        //     } else {
        //         // Directly assign simple keys
        //         $payload[$key] = $value;
        //     }
        // }

        // // Convert the array into GraphQL structure
        // $graphqlPayload = $this->arrayToGraphQL($payload);

        // dd($graphqlPayload);


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

    public function getCoborrowerFields()
    {
        Cache::forget('coborrowerFields');
        $data = [];
        $data = Cache::remember('coborrowerrFields', 60 * 60, function () use ($data) {
            $table = MappingTable::where('title', 'customersCollection')->first();
            if ($table) {
                $columns = json_decode($table->columns, true) ?? [];
                // dd($columns);
                $data = $this->processColumns($columns);
            }
            return $data;
        });

        return $data;
    }


    private function processColumns($columns, $parentKey = '')
    {
        $data = [];
        foreach ($columns as $key => $column) {
            if (is_array($column)) {
                if (!in_array($key, ['createdBy', 'previousResidence', 'dealership'])) {
                    $currentKey = $parentKey ? $parentKey . '.' . $key : $key;
                    $data = array_merge($data, $this->processColumns($column, $currentKey));
                }
            } else {
                $currentKey = $parentKey ? $parentKey . '.' . $column : $column;
                $data[] = $currentKey;
            }
        }

        return $data;
    }
}
