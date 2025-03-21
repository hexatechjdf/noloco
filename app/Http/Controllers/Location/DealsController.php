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

    public function setFilters($locationId, $vin = null, $term = null)
    {
        $filters = [];

        // Always include dealershipSubAccountId filter
        if (!empty($locationId)) {
            $filters[] = 'dealershipSubAccountId: { equals: "' . $locationId . '" }';
        }

        // Add vin or term filter
        if (!empty($vin) || !empty($term)) {
            $column = $vin ? 'vin' : 'name';
            $value = $vin ?? $term;
            $filters[] = $column . ': { contains: "' . $value . '" }';
        }

        // Return the final GraphQL `where` condition
        return '{ ' . implode(', ', $filters) . ' }';
    }

    public function searchInventory(Request $request)
    {
        // dealershipSubAccountId
        // dd($request->all());
        $res = [];

        $filters = $this->setFilters($request->locationId, $request->vin, $request->term);
        try {
            $query = $this->inventoryService->setQuery($request,null, $filters);
            // dd($query);
            $data = $this->inventoryService->submitRequest($query);
            // dd($data);

            if (isset($data['data']['inventoryCollection']['edges'])) {
                foreach ($data['data']['inventoryCollection']['edges'] as $item) {
                    $node = $item['node'];
                    $res[$node['id']] = [
                        'vin'=> $node['vin'],
                        'year'=> $node['year'],
                        'make'=> $node['make'],
                        'model'=> $node['model'],
                        'trim'=> $node['trim'],
                        'miles'=> $node['miles'],
                        'listedPrice'=> $node['listedPrice'],
                        'vehicleCost'=> $node['vehicleCost'],
                        'name' => $node['name'],
                        'image' => explode(',', $node['photosUrls'] ?? '')[0] ?? '',
                        'stock' => $node['stock']
                    ];
                }
                $res = collect($res)->map(function ($value, $key) {
                    return (object) [
                        'id' => $key,
                        'name' => $value['name'],
                        'image' => $value['image'],
                        'stock' => $value['stock'],
                        'vin'=> $value['vin'],
                        'year'=> $value['year'],
                        'make'=> $value['make'],
                        'model'=> $value['model'],
                        'trim'=> $value['trim'],
                        'miles'=> $value['miles'],
                        'listedPrice'=> $value['listedPrice'],
                        'vehicleCost'=> $value['vehicleCost'],
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
        if(!$request->contactId)
        {
            $req = $request->formData;
            if(!$req)
            {
                return response()->json(['error' => 'Something wrong']);
            }
            try{
              $req = setCotactFieldsPayload($req);
              $cont_id =  $this->dealService->createContact($request->locationId,$req);
              $request->merge(['contactId' => $cont_id]);
            }catch(\Exception $e){

            }
        }
        if($request->contactId)
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

                $data = $this->inventoryService->submitRequest($query, 1);
                $deal_id = @$data['data']['createDeals']['id'] ?? null;

                // $deal_id = 144;
            }
            catch(\Exception $e){
                $deal_id = null;
                // throw $e;
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

    public function startDealForm(Request $request)
    {
        $contact_id = $request->contactId ?? null;
        $location_id = $request->locationId;

        return view('locations.deals.form.index', get_defined_vars());
    }

    public function updateContactForm(Request $request)
    {
        $contact_id = $request->contactId;
        $location_id = $request->locationId;
        $contact = [];
        try{
            $contact = $this->dealService->getContact($request->locationId,$request->contactId);
        }catch(\Exception $e){
            $contact = [];
        }
        $vin = @$contact['vin_'];


        return view('locations.deals.form.contact', get_defined_vars());
    }

    public function leadForm(Request $request)
    {
        $contact_id = $request->contactId ?? null;
        $location_id = $request->locationId;

        return view('locations.deals.form.lead', get_defined_vars());
    }

}
