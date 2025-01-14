<?php

use App\Models\Setting;
use App\Helper\gCache;
use Carbon\Carbon;
use App\Models\User;
use App\Models\MappingTable;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

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
        if ($type == 'payroll_date') {
            $dateParts = explode('/', $date);
            $date = $dateParts[0] . '/01/' . $dateParts[1];
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
function fields($tableName = null)
{

    try {
        if ($tableName) {
            $table = MappingTable::where('title', $tableName)->first();
            $columns = json_decode($table->columns, true);

            $data = getNonObjectFields($columns, $table->title);
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
        "firstName" => '',
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
        "assignedTo" => 'Assigned User',
        "address" => '',
        "city" => '',
        "state" => '',
        "country" => '',
        "postalCode" => '',
        "website" => '',
        "tags" => '',
        "dateOfBirth" => '',
        "dateAdded" => '',
        "dateUpdated" => '',
        // "businessId" => '',
        "businessName" => '',
        "lastActivity" => '',
        "opportunities" => '',
        "notes" => '',
    ];
}

function processColumns($columns, $parentKey = '',$exclude= [])
{
    // ['createdBy', 'previousResidence', 'dealership']
    $data = [];
    foreach ($columns as $key => $column) {
        if (is_array($column)) {
            if (!in_array($key, $exclude)) {
                $currentKey = $parentKey ? $parentKey . '.' . $key : $key;
                $data = array_merge($data, processColumns($column, $currentKey,$exclude));
            }
        } else {
            $currentKey = $parentKey ? $parentKey . '.' . $column : $column;
            $data[] = $currentKey;
        }
    }

    return $data;
}
