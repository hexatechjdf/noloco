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


    public function formSubmit(Request $request)
    {
        $data = $request->mapping;
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value);
        });

        save_settings('customerMapping', $filteredData);

        return response()->json(['success' => true, 'route' => route('admin.mappings.customer.form')]);
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
