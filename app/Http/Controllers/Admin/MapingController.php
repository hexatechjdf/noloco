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
        $mapping = json_decode(supersetting('ghlMapping'), true) ?? [];
        $columns = $this->getDealsFields();
        $locationId = supersetting('crm_location_id');
        $contact_fileds = CRM::getContactFields($locationId, true);

        $customer_data = $this->getMappingFieldsByTable('customersCollection', 'customersFields');
        $dealership_data = $this->getMappingFieldsByTable('dealershipCollection', 'dealershipFields');
        $vehicle_data = $this->getMappingFieldsByTable('inventoryCollection', 'inventoryFields');

        return view('admin.mapings.fromGhl.form', get_defined_vars());
    }

    public function getMappingFieldsByTable($table, $key)
    {
        $data = Cache::remember($key, 60 * 60, function () use ($table) {
            $table = MappingTable::where('title', $table)->first();
            $data = [];
            if ($table) {
                $columns = json_decode($table->columns, true) ?? [];
                $data = $this->processColumns($columns);
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
    public function ghlToNolocoFormSubmit(Request $request)
    {
        save_settings('ghlMapping', $request->mapping);

        return response()->json(['success' => true, 'route' => route('admin.mappings.ghl.form')]);
    }

    public function customMapingFields(Request $request, InventoryService $inventoryService)
    {
        $tables = json_decode(supersetting('table_options') ?? '');
        $data = [];
        try {
            $query = $inventoryService->setTableQuery($tables);
            $data = $inventoryService->submitRequest($query);
        } catch (\Exception $e) {

        }

        dd($data);
    }
}
