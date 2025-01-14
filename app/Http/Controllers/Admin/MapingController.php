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
        $selectedTable = 'dealsCollection';
        $tables = getMappingTables(null, ['dealsCollection']);
        foreach ($tables as $table) {
            $data1[$table->title] = json_decode($table->fields, true);
        }

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
        // Cache::forget($key);
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
        $data = Cache::remember('dealsFields', 60 * 60, function () {
            $table = MappingTable::where('title', 'dealsCollection')->first();
            $data = [];
            if ($table) {
                $columns = json_decode($table->columns, true) ?? [];
                $data = processColumns($columns,'',['createdBy', 'lienholders', 'employeeSigner','coBorrower']);
            }
            return $data;
        });
        return $data;
    }

    public function ghlToNolocoFormSubmit(Request $request)
    {
        $data = $request->mapping;
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value);
        });

        save_settings('dealsMapping', $filteredData);

        return response()->json(['success' => true, 'route' => route('admin.mappings.ghl.form')]);
    }
}
