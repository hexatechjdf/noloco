<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;

class DealsController extends Controller
{
    protected $inventoryService;
    protected $dealService;

    // Constructor to inject the services
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
                    $res[$node['id']] = $node['name'];
                }

                $res = collect($res)->map(function ($value, $key) {
                    return (object) [
                        'id' => $key,
                        'name' => $value,
                    ];
                });
            }
        } catch (\Exception $e) {
            return $res;
        }

        return response()->json($res);
    }

    public function getCustomers(Request $request)
    {
        $customers = [];
        $customer_name = "";
        $customer_id = 7;
        $dealership_id = null;
        $dealership_name = "";
        $deals = [];
        try {
            $data = $this->dealService->getCustomerInfo($request);
            $res = $data['data']['customersCollection'];
            if (isset($res['edges'])) {
                $node = @$res['edges'][0]['node'];
                $customer_id = $node['id'];
                $customer_name = $node['name'];
                if (isset($node['dealership'])) {
                    $dealership_id = $node['dealership']['id'];
                    $dealership_name = $node['dealership']['name'];
                }
                $deals = $this->getDeals($customer_id);
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'There is something wrong with location id or contact id']);
        }
        $view = view('locations.deals.components.listView', get_defined_vars())->render();

        return response()->json(['view' => $view, 'customer_name' => $customer_name, 'customer_id' => $customer_id, 'dealership_id' => $dealership_id]);


        dd($data);
        if ($request->contactId) {
            $filters = [
                "filters" => [
                    "column" => "dealershipSubAccountId",
                    "value" => $conId,
                    "order" => "equals",
                ],
            ];
            $request->merge(['filters' => $filters]);
            try {
                $query = $this->inventoryService->setQuery($request, null, null, 'customersCollection');
                $data = $this->inventoryService->submitRequest($query);
                $res = $data['data']['customersCollection'];
                if (isset($res['edges'])) {
                    foreach ($res['edges'] as $item) {
                        $uuid = $item['node']['uuid'];
                        $id = $item['node']['id'];
                        $id = ['id' => $id, 'uuid' => $uuid];
                        $customers[json_encode($id)] = $uuid;
                    }

                    $this->getDeals($customer_id = null);
                }
            } catch (\Exception $e) {
            }
        }

        $view = view('locations.deals.components.customers', get_defined_vars())->render();
        return response()->json(['view' => $view]);
    }

    public function getDeals($customer_id)
    {

        try {
            $query = $this->dealService->getDealsByCustomerQuery($customer_id);
            $data = $this->inventoryService->submitRequest($query, 1);
            $res = $data['data']['dealsCollection'];
            $deals = [];
            if (isset($res['edges'])) {
                foreach ($res['edges'] as $edge) {
                    $item = $edge['node'];
                    $deals[] = ['id' => $item['id'], 'status' => $item['dealStatus'], 'type' => $item['dealType'], 'uuid' => $item['uuid'], 'docType' => $item['docFee']];
                }
            }
            return $deals;
        } catch (\Exception $e) {
        }

        return [];
    }

    public function create(Request $request)
    {
        $filters = [
            "filters" => [
                "column" => "id",
                "value" => $request->id,
                "order" => "equals",
            ],
        ];
        $customer = $this->dealService->getCustomerInfo($request);
        list($dealer_id,$dealership) =  $this->dealService->getDealership($request,$request->contactId);
        $contact = $this->dealService->getContact($request->locationId,$request->contactId);
        try {
            $query = $this->inventoryService->setQuery($request, null, null, 'inventoryCollection');
            $data = $this->inventoryService->submitRequest($query);}
            catch(\Exception $e){

            }
        dd($data);

    }
}
