<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\InventoryService;
use Illuminate\Http\Request;
use App\Models\CrudSetting;
use App\Models\Setting;
use App\Models\MappingTable;
use App\Helper\CRM;
use App\Helper\gCache;
use Illuminate\Support\Str;
class SettingController extends Controller
{

    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $scopes = CRM::$scopes;
        $company_name = null;
        $connecturl = CRM::directConnect();
        $company_id = null;
        $authuser = loginUser();
        $crmauth = $authuser->crmauth;
        try {
            if (@$crmauth->company_id) {
                list($company_name, $company_id) = CRM::getCompany($authuser);
            }
        } catch (\Exception $e) {

        }

        return view('admin.setting.index', get_defined_vars());
    }

    public function noloco(Request $request)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $inv_data = $this->getSpecificFields();
        $noloco_tables = getMappingTables('array');

        return view('admin.setting.noloco', get_defined_vars());
    }

    public function mapping(Request $request,$type = 'deals')
    {
        $keyy = $type.'MappingSetting';
        $mapping = json_decode(supersetting($keyy), true) ?? [];
        // dd($mapping);
        $columns = json_decode(supersetting('dealsCustomTypeColumns'), true) ?? $this->nolocoCustomColumnsWithType();

        // $columns = getColumnsByTable('dealsColumns',[],  null,'dealsCollection');

        return view('admin.setting.mapping', get_defined_vars());
    }

    public function fetchDealFields(Request $request)
    {
        $this->nolocoCustomColumnsWithType();

        return response()->json(['success' => 'Successfully Updated']);
    }



    public function nolocoCustomColumnsWithType($tableName = 'dealsCollection')
    {
        $final = [];
        try {
            $query = $this->inventoryService->setTableQuery([$tableName]);
            $data = $this->inventoryService->submitRequest($query,1);
            $fields = $data['data']['dealsCollection']['fields'];
            // dd($fields);
            $final = $this->fetchNonObjectColumns($fields) ?? [];

            save_settings('dealsCustomTypeColumns', $final);
            return $final;
        } catch (\Exception $e) {
            return $final;
        }
    }

    public function fetchNonObjectColumns($fields, $parent_key = null)
    {
        $final = [];

        foreach ($fields as $f) {
            $k = @$f['type']['kind'];
            $m = @$f['type']['name'];
            $sf = @$f['type']['fields'];
            $name = $parent_key ? $parent_key . '.' . $f['name'] : $f['name'];

            if (!in_array($name, ['vehicles', 'lienholders', 'dealership', 'employeeSigner','worksheets'])) {
                $t = $k == 'SCALAR' ? $m : $k;

                // If the field is an object, process its fields recursively
                if ($k == 'OBJECT' && $sf) {
                    $nestedFields = $this->fetchNonObjectColumns($sf, $name);
                    $final = array_merge($final, $nestedFields); // Merge the nested results
                } else {
                    $final[$name] = Str::contains($name, '.number') ? 'Phone' : $t; // Add scalar or non-object fields
                }
            }
        }

        return $final;
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

    public function nolocoTables(Request $request)
    {
        $query = $this->inventoryService->getTableQuery($request);
        $data = $this->inventoryService->submitRequest($query);
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
    public function nolocoTablesInfo(Request $request)
    {
        $this->updateMappingTables($request);
        $data = [];
        try {
            $query = $this->inventoryService->setTableQuery($request->table_options);
            $data = $this->inventoryService->submitRequest($query,1);
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
    public function nolocoTablesSubmit(Request $request)
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

    public function crudSetting($key)
    {
        $setting = CrudSetting::where('key',$key)->first();
        $data = $setting ? json_decode($setting->content ?? '',true) : [];

        return view('admin.setting.crud',get_defined_vars());
    }

    public function crudSettingSave(Request $request,$key)
    {
        $data = $request->data;
        $data = json_encode($data);
        $setting = CrudSetting::updateOrCreate(['key' => $key], ['content' => $data]);

        return response()->json(['success' => 'Successfully  Submitted']);
    }
}
