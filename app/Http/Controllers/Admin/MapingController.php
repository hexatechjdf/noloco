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
use App\Services\Api\DealService;

class MapingController extends Controller
{

    protected $inventoryService;
    protected $dealService;

    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }

    public function customMaping()
    {
        $items = AdminMapingUrl::paginate(10);

        return view('admin.mapings.custom.index', get_defined_vars());
    }

    public function customMapingForm($id)
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
        AdminMapingUrl::where('uuid', $request->id)->update([
            'table' => $request->table ?? '',
            'mapping' => json_encode($request->maps) ?? '',
            'searchable_fields' => json_encode($request->searchable_fields) ?? '',
            'displayable_fields' => json_encode($request->displayable_fields) ?? '',
            'listed_attributes' => json_encode($request->attr) ?? '',
            'related_urls' => json_encode($request->related_urls) ?? '',
        ]);

        return response()->json(['success' => true, 'route' => route('admin.mappings.custom.index')]);
    }

    public function customerForm($prefix = 'deals')
    {
        $keyy = 'customer';
        list($mapping, $columns,$locationId,$contact_fileds) = $this->getFields($keyy,$prefix.$keyy);
        return view('admin.mapings.coborrower.form', get_defined_vars());
    }

    public function coborrowerForm($prefix = 'deals')
    {
        $keyy = 'coborrower';
        list($mapping, $columns,$locationId,$contact_fileds) = $this->getFields($keyy,$prefix.$keyy);
        return view('admin.mapings.coborrower.form', get_defined_vars());
    }

    public function dealsForm()
    {
        $keyy = 'deals';
        $vehicle = $this->getFieldsByTable('inventoryCollection', 'invetoryFields');
        $dealership = $this->getFieldsByTable('dealershipCollection', 'dealershipFields');
        $customer = $this->getFieldsByTable('customersCollection', 'customerFields');
        list($mapping, $columns,$locationId,$contact_fileds) = $this->getFields($keyy);
        return view('admin.mapings.deals.form', get_defined_vars());
    }

    public function getFields($key,$setKey = null,$exclude = [], $contain = null)
    {
        $setKey  = $setKey ?? $key;
        $mapping = json_decode(supersetting($key.'Mapping'), true) ?? [];
        $columns = json_decode(supersetting($setKey.'MappingSetting'), true) ?? [];

        $locationId = supersetting('crm_location_id');
        $contact_fileds = CRM::getContactFields($locationId, true);


        return [$mapping, $columns,$locationId,$contact_fileds];
    }

    public function getFieldsByTable($table_name, $key)
    {
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

    public function formSubmit(Request $request)
    {
        $prefix = $request->prefix;
        $keyy = $prefix ? $prefix.$request->key . 'Mapping' : $request->key . 'Mapping';
        $mapping = $request->mapping;
        $type = $request->type;
        $filteredData = [];
        foreach ($mapping as $key => $value) {
            if(!is_null($value))
            {
                if (array_key_exists($key, $type)) {
                    $filteredData[$key] = [
                        'column' => $value,
                        'type' => $type[$key]
                    ];
                }
            }

        }

        save_settings($keyy, $filteredData);

        $route = $request->key == 'customer' ? route('admin.mappings.customer.form',$prefix) : ( $request->key == 'deals' ? route('admin.mappings.deals.form') : route('admin.mappings.coborrower.form',$prefix));

        return response()->json(['success' => true, 'route' => $route]);
    }

}
