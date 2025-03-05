<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use App\Services\GhlService;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->locationId;
        $vin = $request->vin;
        return view('locations.inventory.index', get_defined_vars());
    }

    public function getList(Request $request,GhlService $ghlservice)
    {
        $contacts = [];
        $locationId = $request->location;
        try{
            $idd = $ghlservice->searchField('vin',['vin','vin_'],'contact.',$locationId);

            if ($idd) {
                $contacts = $ghlservice->contactSearchByField($locationId,$idd,$request->vin);
            }
        }catch(\Exception $e){

        }
        $view =  view('locations.inventory.list', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }
}
