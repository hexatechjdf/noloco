<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\InventoryService;

class CreditController extends Controller
{

    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function list(Request $request)
    {
        try{
            $query = $this->inventoryService->getCreditList($request->locationId, $request->contactId);
            $data = $this->inventoryService->submitRequest($query);
        }catch(\Excption $e){

        }
        return response()->json($data);
    }
}
