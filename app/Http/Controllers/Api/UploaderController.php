<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helper\CRM;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Services\Api\InventoryService;
use App\Jobs\CollectAllImagesJob;

class UploaderController extends Controller
{
    public function uploadFile(Request $request, InventoryService $inventoryService)
    {
        $data = json_decode($request->data, true);
        dd($data, 1234);

        try {
            $allImages = is_array($request->allImages) ? implode(',', $request->allImages) : $request->allImages;

            $query = $inventoryService->updateQuery($request->uuid, $allImages);
            $inventoryService->submitRequest($query);

            if ($request->newImages) {
                dispatch((new CollectAllImagesJob($request->newImages, $request->location_id)));
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true], 200);
    }

}
