<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMapingUrl;
use App\Services\Api\InventoryService;
use Illuminate\Http\Request;
use App\Models\MappingTable;
use Illuminate\Support\Facades\Cache;
use App\Helper\CRM;
use App\Http\Requests\Admin\MappingUrlRequest;

class MapingController extends Controller
{
    public function customMaping()
    {
        $items = AdminMapingUrl::paginate(10);

        return view('admin.mapings.custom.index', get_defined_vars());
    }

    public function customMapingForm(InventoryService $inventoryService, $id)
    {
        $url = AdminMapingUrl::where('uuid', $id)->firstOrFail();
        $attrr = $url->listed_attributes ?? $url->attributes;
        $attributes = json_decode($attrr, true) ?? [];
        $related_urls = json_decode($url->related_urls, true) ?? [];
        $searchable = json_decode($url->searchable_fields ?? '', true) ?? [];
        $displayable = json_decode($url->displayable_fields ?? '', true) ?? [];
        $table_name = $url->table ?? '';

        $data1 = [];
        // $tables = getMappingTables('array');
        $tables = getMappingTables();
        foreach ($tables as $table) {
            $data1[$table->title] = json_decode($table->fields, true);
        }
        // $data = gCache::get('mappings', function () use ($tables, $inventoryService) {
        //     try {
        //         $query = $inventoryService->setTableQuery($tables);
        //         $data = $inventoryService->submitRequest($query);
        //         return $data;
        //     } catch (\Exception $e) {
        //         return [];
        //     }
        // });

        return view('admin.mapings.custom.map', get_defined_vars());
    }

    public function customMapingFormSubmit(MappingUrlRequest $request)
    {
        AdminMapingUrl::where('id', $request->id)->update([
            'table' => $request->table,
            'mapping' => json_encode($request->maps),
            'searchable_fields' => json_encode($request->searchable_fields),
            'displayable_fields' => json_encode($request->displayable_fields),
            'listed_attributes' => json_encode($request->attr),
            'related_urls' => json_encode($request->related_urls),
        ]);

        return response()->json(['success' => true, 'route' => route('admin.mappings.custom.index')]);
    }

    public function ghlToNolocoForm()
    {
        $mapping = json_decode(supersetting('dealsMapping'), true) ?? [];
        $columns = $this->getDealsFields();
        $locationId = supersetting('crm_location_id');
        $contact_fileds = CRM::getContactFields($locationId, true);

        $vehicle = $this->getFieldsByTable('inventoryCollection', 'invetoryFields');
        $dealership = $this->getFieldsByTable('dealershipCollection', 'dealershipFields');
        $customer = $this->getFieldsByTable('customersCollection', 'customerFields');

        return view('admin.mapings.fromGhl.form', get_defined_vars());
    }

    public function getFieldsByTable($table_name, $key)
    {
        Cache::forget($key);
        $data = Cache::remember($key, 60 * 60, function () use ($table_name) {
            $table = MappingTable::where('title', $table_name)->first();
            $data = [];
            if ($table) {
                $data = json_decode($table->fields, true);
            }
            return $data;
        });
        return $data;
    }

    public function getDealsFields()
    {
        $data = Cache::remember('dealsssFields', 60 * 60, function () {
            $table = MappingTable::where('title', 'dealsCollection')->first();
            $data = [];
            if ($table) {
                $columns = json_decode($table->columns, true) ?? [];
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


    public function processString($input)
    {
        // Step 1: Split the input string by dots
        $parts = explode('.', $input);

        // Step 2: Extract the object name (first word)
        $objectName = array_shift($parts); // Removes and returns the first element

        // Step 3: Convert the remaining parts into the required format
        $formattedString = collect($parts)->map(fn($part) => "['$part']")->join('');

        // Return both object name and formatted string
        return [
            'objectName' => $objectName,
            'formatted' => $formattedString,
        ];
    }

    public function getObjectData($string)
    {
        $availableObjects = [];
        // Step 1: Extract the object name (first word before dot)
        $parts = explode('.', $string);
        $objectName = array_shift($parts); // This will give you "contact" from "contact.name.id.uuid"

        // Step 2: Check if the object exists in the provided array
        if (!isset($availableObjects[$objectName])) {
            return null; // Return null if the object doesn't exist
        }

        // Step 3: Get the object
        $currentObject = $availableObjects[$objectName];

        // Step 4: Build the dynamic access path
        foreach ($parts as $key) {
            // Check if $currentObject is an array and the key exists
            if (is_array($currentObject) && array_key_exists($key, $currentObject)) {
                $currentObject = $currentObject[$key];
            }
            // Check if $currentObject is an object and the property exists
            elseif (is_object($currentObject) && property_exists($currentObject, $key)) {
                $currentObject = $currentObject->$key;
            } else {
                return null; // Key does not exist
            }
        }
        return $currentObject;
    }
    public function ghlToNolocoFormSubmit(Request $request)
    {
        $data = $request->mapping;
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value);
        });

        // $replacedData = array_reduce(array_keys($filteredData), function ($result, $keyf) use ($filteredData) {
        //     $value = $filteredData[$keyf];

        //     $updatedData = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($keyf, &$result) {
        //         $key = $matches[1];

        //         return $key;
        //     }, $value);

        //     $result[$keyf] = $this->getObjectData($updatedData);

        //     // $result[$keyf] = $updatedData;

        //     return $result;
        // }, []);
        // dd($replacedData);

        save_settings('dealsMapping', $filteredData);

        return response()->json(['success' => true, 'route' => route('admin.mappings.ghl.form')]);
    }

    public function customMapingFields(Request $request, InventoryService $inventoryService)
    {
        $tables = json_decode(supersetting('table_options') ?? '');
        $data = [];
        try {
            $query = $inventoryService->setTableQuery($tables);
            $data = $inventoryService->submitRequest($query, 1);
        } catch (\Exception $e) {

        }

        dd($data);
    }
}
