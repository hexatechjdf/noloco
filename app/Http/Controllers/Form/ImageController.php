<?php

namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\InventoryService;
use App\Jobs\CollectAllImagesJob;
use App\Jobs\UpdateInventoryJob;

class ImageController extends Controller
{
    public function index()
    {
        return view('forms.images.index');
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        // $data = json_decode($request->data);
        // $allImages = is_array($data->allImages) ? implode(',', $data->allImages) : $data->allImages;
        // dd($data, $allImages);
        try {
            $data = json_decode($request->data);
            $allImages = is_array($data->allImages) ? implode(',', $data->allImages) : $data->allImages;
            dispatch((new UpdateInventoryJob($data->featuredFile, $data->id, $allImages)));
            if ($data->newImages) {
                dispatch((new CollectAllImagesJob($data->newImages, $data->locationId)));
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
        return response()->json(['success' => true], 200);
    }
}
