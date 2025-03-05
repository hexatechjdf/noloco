<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Jobs\SetDealsOBjectJob;
use App\Jobs\UpdateDealJob;
use App\Helper\CRM;
use Carbon\Carbon;

class DealsController extends Controller
{
    protected $inventoryService;
    protected $dealService;

    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }
    public function index(Request $request)
    {
        $contact_id = $request->contactId;
        $location_id = $request->locationId;

        return view('locations.deals.index', get_defined_vars());
    }

    public function searchInventory(Request $request)
    {
        $res = [];
        if ($request->term) {
            $filters = [
                "filters" => [
                    "column" => "name",
                    "value" => $request->term,
                    "order" => "contains",
                ],
            ];
            $request->merge(['filters' => $filters]);
        }

        try {
            $query = $this->inventoryService->setQuery($request);
            $data = $this->inventoryService->submitRequest($query);

            if (isset($data['data']['inventoryCollection']['edges'])) {
                foreach ($data['data']['inventoryCollection']['edges'] as $item) {
                    $node = $item['node'];
                    $res[$node['id']] = ['name' => $node['name'], 'image' => explode(',', $node['photosUrls'] ?? '')[0] ?? '', 'stock' => $node['stock']];
                }
                $res = collect($res)->map(function ($value, $key) {
                    return (object) [
                        'id' => $key,
                        'name' => $value['name'],
                        'image' => $value['image'],
                        'stock' => $value['stock'],
                    ];
                });
            }
        } catch (\Exception $e) {
            dd($e);
            return $res;
        }

        return response()->json($res);
    }

    public function getDeals(Request $request)
    {
        $contact =  $this->dealService->getContact($request->locationId,$request->contactId);
        $customer_name = @$contact['firstName'] . ' '. @$contact['lastName'];
        $deals = $this->dealService->getContactDeals($request->locationId,$request->contactId);
        // dd($deals);
        $view = view('locations.deals.components.listView', get_defined_vars())->render();

        return response()->json(['view' => $view, 'customer_name' => $customer_name]);
    }

    public function create(Request $request)
    {
        $availableObjects = [];
        $deal_id = null;
        try {
            list($dealer_id,$dealership) =  $this->dealService->getDealership($request,$request->locationId);
            $availableObjects['dealership'] = $dealership;
            $availableObjects['contact'] = $this->dealService->getContact($request->locationId,$request->contactId);

            $availableObjects['vehicle'] = $this->getVehicle($request);
            $variables = updateDealQueryData(null,$dealer_id,@$availableObjects['vehicle']['id'] ?? null);
            $query = $this->dealService->createDealQuery($variables);

            // $data = $this->inventoryService->submitRequest($query, 1);
            // $deal_id = @$data['data']['createDeals']['id'] ?? null;

            $deal_id = 132;
        }
        catch(\Exception $e){
            $deal_id = null;
            throw $e;
        }

        if($deal_id && count($availableObjects) > 0)
        {
            dispatch((new UpdateDealJob($availableObjects, $deal_id)));
        }
        if($deal_id && $availableObjects['contact'])
        {
            $contact = (object) $availableObjects['contact'];
            dispatch((new SetDealsOBjectJob($contact, $deal_id)));
        }

        return response()->json(['success' => 'Succeffully created']);
    }

    public function getVehicle($request)
    {
        try{
            $filters = [
                "filters" => [
                    "column" => "id",
                    "value" => $request->vehicle_id,
                    "order" => "equals",
                ],
            ];
            $request->merge(['filters' => $filters]);
            $query = $this->inventoryService->setQuery($request);
            $data = $this->inventoryService->submitRequest($query);
            return  $this->getDataFromObject($data,'inventoryCollection');
        }catch(Exception $e)
        {

        }
        return [];
    }

    public function getDataFromObject($data,$table_name)
    {
       return   $res = @$data['data'][$table_name]['edges'][0]['node'] ?? [];
    }

    public function dealForm(Request $request)
    {
        $contact_id = $request->contactId;
        $location_id = $request->locationId;

        return view('locations.deals.form.index', get_defined_vars());
    }

}
