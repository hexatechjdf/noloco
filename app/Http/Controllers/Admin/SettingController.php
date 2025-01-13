<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\InventoryService;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\MappingTable;
use App\Helper\CRM;
use App\Helper\gCache;
class SettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $scopes = CRM::$scopes;
        $company_name = null;
        $connecturl = CRM::directConnect();
        $company_id = null;
        $authuser = loginUser();
        $crmauth = $authuser->crmauth;
        $inv_data = $this->getSpecificFields();
        $noloco_tables = getMappingTables('array');
        try {
            if (@$crmauth->company_id) {
                list($company_name, $company_id) = CRM::getCompany($authuser);
            }
        } catch (\Exception $e) {

        }

        return view('admin.setting.index', get_defined_vars());
    }

    public function getSpecificFields($table = 'inventoryCollection')
    {
        $data = [];
        try {
            $inv = MappingTable::where('title', $table)->first();
            if ($inv) {
                $fields = json_decode($inv->fields, true);
                $data[$table] = $fields;
            }
        } catch (\Exception $e) {

        }

        return $data;
    }

    public function save(Request $request)
    {
        $user = Auth::user();
        foreach ($request->setting ?? [] as $key => $value) {

            save_settings($key, $value);
        }
        return response()->json(['success' => true, 'message' => 'Successfully Submitted']);
    }

    public function nolocoTables(Request $request, InventoryService $inventoryService)
    {
        $query = $inventoryService->getTableQuery($request);
        $data = $inventoryService->submitRequest($query);
        $tables = [];
        if (isset($data['data']['__type']['fields'])) {
            $fields = $data['data']['__type']['fields'];
            foreach ($fields as $field) {
                $tables[] = $field['name'];
                // if ($field['name'] && str_contains($field['name'], 'Collection')) {
                //     $tables[] = $field['name'];
                // }
            }
        }

        $custom_tables = getMappingTables('array');

        $view = view('admin.setting.components.tables', get_defined_vars())->render();
        return response()->json(['success' => true, 'view' => $view], 200);
    }
    public function nolocoTablesInfo(Request $request, InventoryService $inventoryService)
    {
        $this->updateMappingTables($request);
        $data = [];
        try {
            $query = $inventoryService->setTableQuery($request->table_options);
            $data = $inventoryService->submitRequest($query,1);
        } catch (\Exception $e) {
            $data = [];
        }

        return response()->json(['data' => $data]);

    }

    public function updateMappingTables($request)
    {
        MappingTable::whereNotIn('title', $request->table_options)->delete();

        foreach ($request->table_options as $table) {
            MappingTable::updateOrCreate(['title' => $table]);
        }

    }
    public function nolocoTablesSubmit(Request $request, InventoryService $inventoryService)
    {

        $data = json_decode($request->data, true);
        $tables = getMappingTables();

        foreach ($tables as $table) {
            $collection = $data[$table->title];

            $table->fields = json_encode($collection);
            $table->columns = json_encode($this->extractKeysOnly($collection));
            $table->save();
        }
        return response()->json(['success' => true], 200);
    }
    function extractKeysOnly(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // If the value is an array, go deeper and collect inner keys
                $result[$key] = $this->extractKeysOnly($value);
            } else {
                // Otherwise, just include the key
                $result[] = $key;
            }
        }
        return $result;
    }
}
