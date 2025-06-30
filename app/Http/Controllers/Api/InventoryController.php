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

class InventoryController extends Controller
{
    public function index(Request $request, InventoryService $inventoryService)
    {
        try {
            list($inventories,$filters) = $this->getInvntoryListing($inventoryService,$request->locationId ?? 'geAOl3NEW1iIKIWheJcj');

            $data = [
                'filters' => $filters,
                'inventories' => $inventories,
            ];

             return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return $e;
        }
        return response()->json(['error' => 'Failed to fetch inventory data'], 500);
    }

    public function getInvntoryListing($inventoryService,$locationId)
    {
        $allEdges = [];
        $after = null;

        $request = request();
        $filterCounts = [
            'make' => [],
            'exteriorColor' => [],
            'bodyStyle' => [],
        ];
        $filters = setFilters($locationId);
        do {
            $query = $inventoryService->setQuery($request,null,$filters);
            $data = $inventoryService->submitRequest($query);
            $data = @$data['data'];
            $edges = @$data['inventoryCollection']['edges'];
            if (!empty(@$edges)) {
                foreach ($edges as $edge) {
                    $node = $edge['node'] ?? [];
                    // Count Make
                    if (!empty($node['make'])) {
                        $make = $node['make'];
                        $filterCounts['make'][$make] = ($filterCounts['make'][$make] ?? 0) + 1;
                    }

                    // Count Exterior Color
                    if (!empty($node['exteriorColor'])) {
                        $color = $node['exteriorColor'];
                        $filterCounts['exteriorColor'][$color] = ($filterCounts['exteriorColor'][$color] ?? 0) + 1;
                    }

                    // Count Body Style
                    if (!empty($node['bodyStyle'])) {
                        $style = $node['bodyStyle'];
                        $filterCounts['bodyStyle'][$style] = ($filterCounts['bodyStyle'][$style] ?? 0) + 1;
                    }
            }
                $allEdges = array_merge($allEdges, $data['inventoryCollection']['edges']);
            }

            $pageInfo = $data['inventoryCollection']['pageInfo'] ?? [];
            $after = $pageInfo['hasNextPage'] ? $pageInfo['endCursor'] : false;
            $request['after'] = $after;
        } while ($after);

        return [$allEdges,$filterCounts];

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
