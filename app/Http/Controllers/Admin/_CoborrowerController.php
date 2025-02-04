<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MappingTable;
use App\Models\CoborrowMaping;
use Illuminate\Support\Str;
use App\Helper\CRM;
use Illuminate\Support\Facades\Cache;

class CoborrowerController extends Controller
{
    public function index()
    {
        $items = CoborrowMaping::paginate(10);

        return view('admin.mapings.coborrower.index', get_defined_vars());
    }

    public function form()
    {
        $mapping = json_decode(supersetting('coborrowerMapping'), true) ?? [];
        $columns = $this->getCoborrowerFields();
        $locationId = supersetting('crm_location_id');
        $contact_fileds = CRM::getContactFields($locationId, true);

        return view('admin.mapings.coborrower.form', get_defined_vars());
    }
    public function formSubmit(Request $request)
    {
        save_settings('coborrowerMapping', $request->mapping);

        return response()->json(['success' => true, 'route' => route('admin.mappings.coborrower.form')]);
    }

    public function getCoborrowerFields()
    {
        Cache::forget('coborrowerFields');
        $data = [];
        $data = Cache::remember('coborrowerFields', 60 * 60, function () {
            $table = MappingTable::where('title', 'dealsCollection')->first();
            if ($table) {
                $columns = json_decode($table->columns, true) ?? [];
                foreach ($columns as $key => $column) {
                    if (is_array($column) && Str::contains($key, 'coBorrower')) {
                        foreach ($column as $c) {
                            $data[] = $key . '.' . $c;

                        }
                        continue;
                    }
                    if (!is_array($column) && Str::contains($column, 'coBorrower')) {
                        $data[] = $column;
                    }
                }
            }
            return $data;
        });

        return $data;
    }


}
