<?php

use App\Models\Setting;
use App\Helper\gCache;
use Carbon\Carbon;
use App\Models\User;
use App\Models\MappingTable;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberToCarrierMapper;
use App\Models\ErrorLog;


function supersetting($key, $default = '', $keys_contain = null)
{
    Cache::forget($key);
    try {
        $setting = gCache::get($key, function () use ($default, $key, $keys_contain) {
            $setting = Setting::when($keys_contain, function ($q) use ($key, $keys_contain) {
                return $q->where('key', 'LIKE', $keys_contain)->pluck('value', 'key');
            }, function ($q) use ($key) {
                return $q->where(['key' => $key])->first();
            });

            $value = $keys_contain ? $setting : ($setting->value ?? $default);
            gCache::put($key, $value);
            return $value;
        });
        return $setting;
    } catch (\Exception $e) {
        return null;
    }

}

function getNestedValue(array $array, string $path)
{
    $keys = explode('.', $path); // Split the string into keys
    foreach ($keys as $key) {
        if (!isset($array[$key])) {
            return null; // Return null if the key doesn't exist
        }
        if($key == 'number')
        {
           $array =  str_replace(' ', '', @$array['_root']);
        }
        else{
        $array = $array[$key]; // Move deeper into the array

        }
    }
    return $array; // Return the final value
}

function braceParser($value)
{
    return str_replace(['[', ']'], ['{', '}'], $value);
}

function loginUser($user = null)
{
    if (auth()->check()) {
        $user = auth()->user();

    } else {
        if (!$user) {
            $user = User::find($user);
        }

    }
    return $user;
}

function save_settings($key, $value = '')
{
    $value = is_array($value) ? json_encode($value) : $value;
    $setting = Setting::updateOrCreate(
        ['key' => $key],
        [
            'value' => $value,
            'key' => $key,
        ]
    );
    gCache::del($key);
    gCache::put($key, $value);
    return $setting;
}

function isLocal()
{
    return strpos($_SERVER['DOCUMENT_ROOT'], 'htdocs') !== false || strpos($_SERVER['SERVER_NAME'], 'localhost') !== false;
}

function humanNumber($value)
{
    $format = $value;
    try {
        $format = number_format($value);
    } catch (\Throwable $th) {
        //throw $th;
    }
    return $format;
}



function setKeyValueJson($key, $value, $jsonData)
{
    $jsonData->$key = $value;
    return $jsonData;
}


function customDate($date, $format, $type = null)
{
    try {
        if($type=='time')
        {
         return Carbon::parse($date)->toIso8601String();
        }
        return Carbon::parse($date)->format($format);
    } catch (\Throwable $th) {
        //throw $th;
    }
    return $date;
}



function funnelTypes()
{
    return [
        'all',
        'funnel',
        'website',
    ];
}
function getAuthUrl($web)
{
    return route('auth.check') . '?web=' . $web . '&location_id=' . braceParser('[[location.id]]') . '&sessionkey=' . braceParser('[[user.sessionKey]]');
}


function getActions()
{
    return [
        'custom-menu-link' => 'Custom Menu Link',
        'page' => 'Goto Page',
    ];
}
function getCompletedByOptions()
{
    return [
        'user' => 'User',
        'custom_values' => 'Custom Values',
    ];
}

function scriptPaths()
{
    return [
        'inventory' => 'Inventory',
        'inventory_detail' => 'Inventory Detail',
    ];
}

if (!function_exists('lang')) {
    function lang($key, $replace = [], $locale = 'en')
    {
        return trans("messages.$key", $replace, $locale);
    }
}

function getNonObjectFields($input, $table = null)
{
    $fields = [];
    foreach ($input as $key => $value) {
        if (!is_array($value) && $value != 'featuredPhoto' && $value!='vendorAddress') {
            $fields[] = $value;
        }
    }

    if ($table == 'inventoryCollection') {
        $additionalData = ['featuredPhoto' => ['url']];
        $fields[] = ['featuredPhoto' => $additionalData['featuredPhoto']];
    }

    return $fields;
}

function transformGraphQLData($data,$parent=false)
{
    if (is_array($data)) {
        $transformed = [];
        foreach ($data as $key => $value) {
            if($parent && in_array($value, ['inventory','vendorAddress','photos', 'phoneNumber','streetAddress','vendors','logo','customers','dealership']))
            {
                continue;
            }
            if(!in_array($value,['createdBy', 'deals', 'vendorAddress']) && !in_array($key,['createdBy', 'deals', 'vendorAddress']))
            {

                if (is_numeric($key)) {
                    $transformed[] = $value;
                }
                else{
                    $transformed[$key] = transformGraphQLData($value,$key);
                }
            }


            // Recursively transform nested arrays
        }
        return $transformed;
    }
    if($data != 'createdBy' && $data != 'deals')
    {
        return $data;
    }
}



function buildGraphQLFields($fields) {
    $fieldsString = '';

    foreach ($fields as $key => $field) {
        if (is_array($field)) {
            $nestedFields = buildGraphQLFields($field);
            $fieldsString .= sprintf("%s { %s } ", $key, $nestedFields);
        } else {
            $fieldsString .= $field . " ";
        }
    }

    return trim($fieldsString);
}


function fields($tableName = null,$isall = false)
{
    try {
        if ($tableName) {
            $table = MappingTable::where('title', $tableName)->first();
            $columns = json_decode($table->columns, true);
            $data = $isall ?  transformGraphQLData($columns) :getNonObjectFields($columns, $table->title);
            $data = array_filter($data, function ($str) {
                if (!is_array($str)) {
                    return strpos($str, '_') !== 0; // Only include strings NOT starting with '_'
                } else {
                    return $str;
                }
            });
            return $data;
        }
    } catch (Exception $e) {
    }

    return [
        'id',
        'photosUrls',
        ['featuredPhoto' => ['url']],
        'uuid',
        'name',
        'miles',
        'stock',
        'bodyStyle',
        'make',
        'listedPrice',
        'drivetrain',
        'fuelType',
        'description',
        'subHeader',
        'dealerName',
        'exteriorColor',
        'interiorColor',
        'interiorMaterial',
        'fuelType',
        'transmission',
        'engineSize',
        'engineCylinders',
        'status',
        'subHeader',
    ];
}


function getMappingTables($type = null,$names= null)
{
    Cache::forget('table_options');
    $data = gCache::get('table_options', function () use($names) {
        try {
            return MappingTable::when($names,function($q) use($names){
               $q->whereIn('title',$names);
            })->get();
        } catch (\Exception $e) {
            return [];
        }
    });
    if ($type == 'array') {
        $data = $data->pluck('title')->toArray();
    }
    return $data;

}

function defaultContactFields()
{
    return [
        "id" => 'Contact Id',
        "contactName" => '',
        "locationId" => '',
        "firstName" =>'',
        "lastName" => '',
        // 'firstNameLowerCase' => 'First Name',
        // 'lastNameLowerCase' => 'Last Name',
        "email" => '',
        "timezone" => '',
        "companyName" => '',
        "phone" => '',
        // "phoneLabel" => '',
        "dnd" => '',
        "dndSettings" => '',
        "type" => '',
        "source" => '',
        "assignedTo" => '',
        "address1" => '',
        "city" => '',
        "state" => '',
        "country" => '',
        "postalCode" => '',
        "dateOfBirth" => '',
    ];
}

function processColumns($columns, $parentKey = '',$exclude= [],$containKey = null)
{
    // ['createdBy', 'previousResidence', 'dealership']
    $data = [];
    foreach ($columns as $key => $column) {
        if (is_array($column)) {
            if (!in_array($key, $exclude)) {
                if($containKey && !Str::contains($key, $containKey))
                {
                    continue;
                }
                $currentKey = $parentKey ? $parentKey . '.' . $key : $key;
                $data = array_merge($data, processColumns($column, $currentKey,$exclude));
            }
        } else {
            if($containKey && !Str::contains($column, $containKey))
            {
                continue;
            }
            $currentKey = $parentKey ? $parentKey . '.' . $column : $column;
            $data[] = $currentKey;
        }
    }

    return $data;
}

function columnsTypes()
{
return [
   'Int' => 'Integer',
   'Phone' => 'Phone',
   'DateTime' => 'DateTime',
   'Date' => 'Date',
   'Float' => 'Float',
   'Boolean' => 'Boolean',
   'ENUM' => 'ENUM',
   'String' => 'String',
];
}

function getColumnsByTable($key,$exclude = [], $contain = null,$table_name = 'dealsCollection')
{
    // Cache::forget($key);
    $data = [];
    $data = Cache::remember($key, 60 * 60, function () use ($data,$table_name,$exclude,$contain) {
        $table = MappingTable::where('title', $table_name)->first();
        if ($table) {
            $columns = json_decode($table->columns, true) ?? [];
            $data = processColumns($columns,'',$exclude,$contain);
        }
        return $data;
    });
    return $data;
}


function arrayToGraphQL($data)
{
    $result = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $nested = arrayToGraphQL($value);
            $result[] = "$key: { $nested }";
        } else {
            list($type,$value) = convertStringToArray('__', $value);
            $result[] = checkValueByType($type,$key,$value);
        }
    }
    $res =  implode(', ', $result);
    return $res;
    // dd($data);
}

function convertStringToArray($del, $value)
{
    $parts = explode($del, $value);

    return [@$parts[1] ?? 'string', @$parts[0] ?? null];
}

function checkValueByType($type,$key,$value,$is_seperate = null)
{
    if ($type === 'Int' || $type === 'int') {
        $value = ltrim($value, '0');
        $ret =   "$key: $value";
    } elseif ($type === 'Float' || $type === 'float' ) {
        $value = (float)$value;
        $ret =  "$key: $value";
    } elseif ($type === 'ENUM' || $type === 'enum') {
        // add value checker
        $value = transformStateString(strtoupper($value));
        $ret =  "$key: $value";

    }elseif ($type === 'DateTime' || $type == 'Date') {
        $value  = customDate($value, 'm/d/Y','time');
        $ret =  "$key: \"$value\"";

    }elseif ($type === 'Boolean' || $type === 'bool') {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($value)) {
            $ret = "$key: null"; // Handle invalid values as null
        } else {
            $ret = "$key: " . ($value ? 'true' : 'false'); // Ensure GraphQL expects true/false
        }
    } else {
        $value = (string)$value;
        $ret =  "$key: \"$value\"";
    }

    return $ret;
}

function setDataWithType($array,$result,$dealershipId=null,$vehicleId = null)
{
    if($dealershipId && $vehicleId)
    {
        $vId = supersetting('deal_vehicle_col') ?? '';
        $array[$vId] = ['column' => $vehicleId, 'type' => 'int'];
        $dId  = supersetting('deal_dealership_col') ?? '';
        $array[$dId] = ['column' => $dealershipId, 'type' => 'int'];
        $array['dealStatus'] = ['column' => 'open', 'type' => 'ENUM'];
    }
    foreach ($array as $key => $data) {
        if (!is_array($data) || !isset($data['column'], $data['type'])) {
            continue;
        }
        $value = $data['column'];
        if(!empty($value))
        {
            $type = $data['type'];

            $value = $value.'__'.$type;
            $keys = explode('.', $key);
            $temp = &$result;
            foreach ($keys as $k) {
                if (!isset($temp[$k])) {
                    $temp[$k] = [];
                }
                $temp = &$temp[$k];
            }
            $temp = $value;
        }

    }
    return $result;
}

function setDealQueryData($contact,$result,$map_type = 'customerMapping')
{
    $filteredData = json_decode(supersetting($map_type), true) ?? [];

    // Log::info($contact);
    $result = [];
    $replacedData = array_reduce(array_keys($filteredData), function ($result, $keyf) use ($filteredData, $contact) {
        $value = $filteredData[$keyf];
        $updatedData = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($value,$contact, $keyf, &$result) {
            $key = $matches[1];
            if (@$value['type'] == 'phone' && isset($contact->{$key})) {
                $phone = $contact->{$key};
                list($number, $country) = getCountryForPhoneNumber($phone);
                $updatedString = replaceLastWordAfterDot($keyf, 'country');
                $result[$updatedString] = ['column' => $country, 'type' => 'string'];
                return $number;
            }
            if($key == 'today')
            {
                return Carbon::now();
            }
            return isset($contact->{$key}) ? $contact->{$key} : '';
        }, $value);

        $result[$keyf] = $updatedData;

        return $result;
    }, []);

    $result = setDataWithType($replacedData,[]);

    $result['id'] = "%s";

    return  ['graphqlPayload' => [arrayToGraphQL1($result)]];

    return  arrayToGraphQL($result);
}

function arrayToGraphQL1($data)
{
    $result = [];

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // Recursively process nested arrays
            $result[$key] = arrayToGraphQL1($value);
        } else {
            list($type, $value) = convertStringToArray('__', $value);
            $result[$key] = formatValueByType1($type, $value);
        }
    }

    return $result;
}

function formatValueByType1($type, $value)
{
    if ($type === 'Int' || $type === 'int') {
        return (int) ltrim($value, '0'); // Remove leading zeros for integers
    } elseif ($type === 'Float' || $type === 'float') {
        return (float) $value;
    } elseif ($type === 'Boolean' || $type === 'bool') {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    } elseif ($type === 'ENUM' || $type === 'enum') {
        return transformStateString(strtoupper($value));
    } elseif ($type === 'DateTime' || $type === 'Date') {
        return customDate($value, 'm/d/Y', 'time');
    }

    return (string) $value;
}

function getCountryForPhoneNumber($phoneNumber, $defaultRegion = 'PK')
{
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $numberProto = $phoneUtil->parse($phoneNumber);
        $regionCode = $phoneUtil->getRegionCodeForNumber($numberProto);

        return [$numberProto->getNationalNumber(), $regionCode]; //] Returns country code like 'PK' for Pakistan
    } catch (\libphonenumber\NumberParseException $e) {
        return [null, null];
    }
}

function formatPhoneNumberWithCountryCode($phoneData)
{
    if (is_object($phoneData)) {
        $phoneData = (array) $phoneData;
    }
    if (!isset($phoneData['number']) || !isset($phoneData['country'])) {
        return 'Invalid phone data.';
    }

    $phoneNumber = $phoneData['number'];
    $countryCode = $phoneData['country'];
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $numberProto = $phoneUtil->parse($phoneNumber, $countryCode);
        $formattedNumber = $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
        return $formattedNumber;
    } catch (\libphonenumber\NumberParseException $e) {
        return 'Invalid Number: ' . $e->getMessage();
    }
}

function replaceLastWordAfterDot($string, $replacement)
{
    return preg_replace('/\.(\w+)$/', '.' . $replacement, $string);
}


function transformStateString($string)
{
    // Use regex to match the pattern and replace spaces or hyphens with an underscore
    return preg_replace('/\s*[-\s]\s*/', '_', $string);
}

function extractGraphQLErrors(array $errors,$type = 'update')
{
    $extractedErrors = [];
    foreach ($errors as $error) {
        if (isset($error['message'])) {
            preg_match('/"graphqlPayload\[0\]\.(.*?)"/', $error['message'], $matches);

            if (!empty($matches[1])) {
                $columnName = $matches[1];
                $errorMessage = explode(';', $error['message'])[1] ?? $error['message'];

                $extractedErrors[] = [
                    'column' => $columnName,
                    'error' => trim($errorMessage),
                ];
            }
        }
    }

    return $extractedErrors;
}


function removeInvalidGraphQLFields(array &$variables, array $errors)
{
    foreach ($errors as $error) {
        if (isset($error['column'])) {
            $keys = explode('.', $error['column']); // Convert "coBorrowerPhone.number" to ['coBorrowerPhone', 'number']
            unsetNestedKey($variables['graphqlPayload'][0], $keys);
        }
    }

    // Recursively remove empty arrays
    $variables['graphqlPayload'][0] = removeEmptyArrays($variables['graphqlPayload'][0]);

    return $variables;
}

function unsetNestedKey(array &$array, array $keys)
{
    $key = array_shift($keys); // Get the first key

    if (count($keys) === 0) {
        unset($array[$key]); // If it's the last key, remove it
    } elseif (isset($array[$key]) && is_array($array[$key])) {
        unsetNestedKey($array[$key], $keys); // Recursively move deeper
    }
}


function removeEmptyArrays(array $array)
{
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = removeEmptyArrays($value);
            if (empty($value)) {
                unset($array[$key]); // Remove empty arrays
            }
        }
    }
    return $array;
}

function createErrorLogs($errors,$variables,$type = 'create', $dealId =null ,$table = 'Deals',$for = 'deals')
{
    $errors = extractGraphQLErrors($errors,$type);
    $res = null;
    if(is_array($errors) && count($errors) > 0)
    {
        $res = removeInvalidGraphQLFields($variables, $errors);
        foreach($errors as $ee)
        {
            ErrorLog::create([
                'type' => $type,
                'table' => $table,
                'for' => $for,
                'table_id' => $dealId,
                'column' => $ee['column'],
                'error' => $ee['error'],
            ]);
        }
    }


    return $res;
}

function updateDealQueryData($availableObjects=null, $dealershipId=null, $vehicleId = null)
{
    $replacedData = [];
    if($availableObjects)
    {
        $filteredData = json_decode(supersetting('dealsMapping'), true) ?? [];
        $replacedData = array_map(function ($value) use ($availableObjects) {
            if (preg_match('/\{\{(.*?)\}\}/', $value['column'], $matches)) {
                $val = getDealsObjectData($matches[1], $availableObjects) ?? null;
            } else {
                $val = $value['column'];
            }
            return ['column' => $val, 'type' => $value['type']];
        }, $filteredData);
    }

    $result = setDataWithType($replacedData, [], $dealershipId, $vehicleId);
    if($availableObjects)
    {
        $result['id'] = "%s";
        return ['graphqlPayload' => [arrayToGraphQL1($result)]];
    }
    return  arrayToGraphQL($result);
}

function getDealsObjectData($string,$availableObjects)
{

    $parts = explode('.', $string);
    $objectName = array_shift($parts);

    if (!isset($availableObjects[$objectName])) {
        return null;
    }
    $currentObject = $availableObjects[$objectName];
    foreach ($parts as $key) {
        if (is_array($currentObject) && array_key_exists($key, $currentObject)) {
            $currentObject = $currentObject[$key];
        }
        elseif (is_object($currentObject) && property_exists($currentObject, $key)) {
            $currentObject = $currentObject->$key;
        } else {
            if($key == 'today')
            {
                return Carbon::now();
            }
            return null;
        }
    }
    return $currentObject;
}


function ghlRedurect($locationId, $contactId, $type = 'contact')
{
    $base = "https://app.gohighlevel.com/v2/";
    if($type =='contact')
    {
        $url = $base.'location/'.$locationId.'/contacts/detail/'.$contactId;
    }

    return $url;
}


function contactForm()
{
    return [
        "first_name"             => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "middle_name"            => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "last_name"              => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "email"                  => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "phone"                  => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "address1"               => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "city"                   => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "state"                  => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "postal_code"            => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "source"                 => ['is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "date_of_birth"          => ['is_required' => true,'input_type' => 'text', 'field_type' => 'extra'],
        "social_security_number" => ['is_required' => true,'input_type' => 'text', 'field_type' => 'extra'],
        "id_type"                => ['is_required' => true,'input_type' => 'text', 'field_type' => 'extra'],
        "id_number"              => ['is_required' => true,'input_type' => 'text', 'field_type' => 'extra'],
        "state_id"               => ['is_required' => true,'input_type' => 'text', 'field_type' => 'extra'],
        "id_expiration"          => ['is_required' => true,'input_type' => 'text', 'field_type' => 'extra']
    ];

}

function vehicleForm()
{
    return [
        "stock_"             => ['sub_key' => 'stock','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "vin_"               => ['sub_key' => 'vin','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "year"               => ['sub_key' => 'year','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "make"               => ['sub_key' => 'make','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "model"              => ['sub_key' => 'model','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "trim"               => ['sub_key' => 'trim','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "mileage"            => ['sub_key' => 'miles','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "listed_price"       => ['sub_key' => 'listedPrice','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
        "vehicle_cost"       => ['sub_key' => 'vehicleCost','is_required' => true,'input_type' => 'text', 'field_type' => 'simple'],
    ];

}

function creditAppForm()
{
    return [
        "residence_type"            => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "years_at_residence"        => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "months_at_residence"       => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "residence_payment"         => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_address"          => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_city"             => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_state"            => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_postal_code"      => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_residence_years"  => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_residence_months" => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_residence_type"   => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_residence_payment"=> ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_country"          => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "employment_status"         => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "employer_name"             => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "job_position"              => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "job_years"                 => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "job_months"                => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "employer_phone_number"     => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "income_frequency"          => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "gross_income"              => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_employment_status"=> ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_employer"         => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_job_position"     => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_employer_phone"   => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_job_years"        => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "previous_job_months"       => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "other_income_source"       => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "other_income_amount"       => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "down_payment"              => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
    ];
}

function tradeForm()
{
    return [
        "trade_year"   => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "trade_make"   => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "trade_model"  => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "trade_trim"   => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "trade_vin"    => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "trade_miles"  => ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
        "trade_pay_off"=> ['is_required' => true, 'input_type' => 'text', 'field_type' => 'simple'],
    ];
}


function convertKeysToCamelCase(array $array): array {
    $converted = [];
    foreach ($array as $key => $value) {
        $converted[Str::camel($key)] = $value;
    }
    return $converted;
}
