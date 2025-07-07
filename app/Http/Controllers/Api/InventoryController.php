<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminMapingUrl;
use Illuminate\Http\Request;
use App\Services\Api\InventoryService;
use App\Helper\CRM;
use App\Models\Script;
use App\Helper\gCache;
use App\Models\CrmAuths;
use App\Models\Inventory;


class InventoryController extends Controller
{
    public function index(Request $request, InventoryService $inventoryService)
    {
        $inv = Inventory::where('location_id', $request->locationId)->first();
        if(!$inv)
        {
            return response()->json(['error' => 'Failed to fetch inventory data'], 500);
        }

        $data = [
            'filters' => json_decode($inv->filters ?? '',true) ?? [],
            'inventories' => json_decode($inv->content ?? '',true) ?? [],
        ];

        return response()->json(['data' => $data], 200);
    }

    public function getSettings(Request $request)
    {
        $settings = supersetting('inv_settings', '', 'inv_%');

        $scripts = gCache::get('scripts', function () {
            return Script::get();
        });

        return response()->json(['settings' => $settings, 'scripts' => $scripts], 200);
    }

    public function getSpecificInv(Request $request, $id, InventoryService $inventoryService)
    {
        if (!$id) {
            return response()->json(['error' => 'Invalid Request'], 404);
        }
        if (!$request->locationId) {
            return response()->json(['error' => 'location id is invalid']);
        }
        try {
            $query = $inventoryService->setQuery(request(), $id);
            $data = $inventoryService->submitRequest($query);

            return $data;
        } catch (\Exception $e) {

        }
        return response()->json(['error' => 'Failed to fetch inventory data'], 500);
    }

    public function adminExtention(Request $request)
    {
        $url = $request->url;
        $attributes = $request->attributes;

        $url = AdminMapingUrl::updateOrCreate(['url' => $url], ['attributes' => $attributes]);

        return response()->json(['success' => ''], 200);
    }
}
