<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Jobs\SetDealsOBjectJob;
use App\Helper\CRM;

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

        // dd($availableObjects['contact'] );
        $availableObjects['vehicle'] = $this->getVehicle($request);
        $data = $this->setQueryData($availableObjects,$dealer_id,@$availableObjects['vehicle']['id'] ?? null);
        $query = $this->dealService->createDealQuery($data);
        $data = $this->inventoryService->submitRequest($query, 1);
        $deal_id = @$data['data']['createDeals']['id'] ?? null;
        }
        catch(\Exception $e){
            $deal_id = null;
            throw $e;
        }

        if($deal_id && $availableObjects['contact'])
        {
            $contact = (object) $availableObjects['contact'];
            // ->delay(Carbon::now()->addMinutes(5)))
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

    public function setQueryData($availableObjects,$dealershipId,$vehicleId = null)
    {
        $filteredData = json_decode(supersetting('dealsMapping'), true) ?? [];
         $replacedData = array_reduce(array_keys($filteredData), function ($result, $keyf) use ($filteredData,$availableObjects) {
            $value = $filteredData[$keyf];
            $updatedData = preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($keyf, &$result,$availableObjects) {
                $key = $matches[1];
                return $key;
            }, $value);

            $val = $this->getObjectData($updatedData['column'],$availableObjects) ?? null;
            $result[$keyf] = ['column' => $val, 'type' => $updatedData['type']];
            return $result;
        }, []);
        $result = [];
        $result = setDataWithType($replacedData,$result,$dealershipId,$vehicleId);

        return  arrayToGraphQL($result);
    }



    public function getObjectData($string,$availableObjects)
    {
        $parts = explode('.', $string);
        $objectName = array_shift($parts);

        if (!isset($availableObjects[$objectName])) {
            return null;
        }
        $currentObject = $availableObjects[$objectName];
        foreach ($parts as $key) {
            if (is_array($currentObject) && array_key_exists($key, $currentObject)) {
                $currentObject = $currentObject[$key];
            }
            elseif (is_object($currentObject) && property_exists($currentObject, $key)) {
                $currentObject = $currentObject->$key;
            } else {
                return null;
            }
        }
        return $currentObject;
    }
}
