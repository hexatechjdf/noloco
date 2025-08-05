<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\Api\InventoryService;
use App\Models\Setting;
use App\Helper\CRM;
use App\Models\CustomFields;
use Illuminate\Support\Facades\Http;

class DealService
{

    protected $inventoryService;

    // Constructor to inject the services
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function getCustomerQuery($highlevelClientId,$dealershipSubAccountId)
    {
        $query = <<<GRAPHQL
            query {
              customersCollection(where: { dealershipSubAccountId: { equals: "%l" }, highlevelClientId: { equals: "%s" } }) {
                edges {
                   node {
                            id
                            uuid
                            name
                            dealershipSubAccountId
                            highlevelClientId
                            dealership {
                                id
                                name
                            }
                        }
                }
              }
            }
    GRAPHQL;

        $query = str_replace(
            [ '%l','%s'],
            [$dealershipSubAccountId, $highlevelClientId],
            $query
        );


        return $query;
    }



    public function getDealsByCustomerQuery($locId,$conId, $first = 100)
    {

        $query = <<<GRAPHQL
        query {
          dealsCollection(first: 100 ,where: {dealershipSubAccountId: {equals: "%l"} highlevelClientId: {equals: "%c"} dealStatus:{equals: "OPEN"}}) {
            edges {
              node {
                    id
                    uuid
                    createdAt
                    updatedAt
                    dealStatus
                    dealType
                    salesPrice
                    downPayment
                    term

                    vehicles {
                      id
                      uuid
                      name
                      __typename
                    }

                    docFee
                    titleFee
                    licenseFee
                    lienFee
                  }
            }
            pageInfo {
               startCursor
               endCursor
               hasNextPage
               hasPreviousPage
            }
          }
        }
        GRAPHQL;

        $query = str_replace(
            ['%l','%c'],
            [$locId,$conId],
            $query
        );
        return $query;
    }

    public function getDealsByCoborrowerQuery($locId,$conId, $first = 100)
    {

        $query = <<<GRAPHQL
        query {
          dealsCollection(first: 100 ,where: {dealershipSubAccountId: {equals: "%l"} coBorrowerHighlevelClientId : {equals: "%c"} dealStatus:{equals: "OPEN"}}) {
            edges {
              node {
                    id
                    uuid
                    createdAt
                    updatedAt
                    dealStatus
                    dealType
                    salesPrice
                    downPayment
                    term

                    vehicles {
                      id
                      uuid
                      name
                      __typename
                    }

                    docFee
                    titleFee
                    licenseFee
                    lienFee
                  }
            }
            pageInfo {
               startCursor
               endCursor
               hasNextPage
               hasPreviousPage
            }
          }
        }
        GRAPHQL;

        $query = str_replace(
            ['%l','%c'],
            [$locId,$conId],
            $query
        );
        return $query;
    }


    public function setCustomerCreateQuery($graphqlPayload,$dealer_id)
    {
        $dealer_id = (int)$dealer_id;
        $mutation = <<<GRAPHQL
        mutation {
            createCustomers($graphqlPayload) {
                id
            }
        }
        GRAPHQL;

        $mutation = str_replace(
            ['%c'],
            [$dealer_id],
            $mutation
        );
        return $mutation;
    }

    public function updateDealQuery($graphqlPayload)
    {
        $mutation = <<<GRAPHQL
            mutation bulkUpdateDeals(\$graphqlPayload: [DealsInput!]!) {
                bulkUpdateDeals(values: \$graphqlPayload) {
                    id
                }
            }
        GRAPHQL;
        // $mutation = <<<GRAPHQL
        // mutation {
        //     updateDeals($graphqlPayload) {
        //         id
        //     }
        // }
        // GRAPHQL;
        return $mutation;

        $customer_id = (int)$customer_id;
        $mutation = <<<GRAPHQL
        mutation {
            updateDeals(
            id: "{$dealId}",
            coBorrowerId: {$customer_id}
            ) {
            id

             }
        }
        GRAPHQL;

        // $mutation = str_replace(
        //     ['%c'],
        //     [$dealer_id],
        //     $mutation
        // );
        return $mutation;
    }

    public function createDealQuery($graphqlPayload)
    {
        $mutation = <<<GRAPHQL
        mutation {
            createDeals($graphqlPayload) {
                id
            }
        }
        GRAPHQL;
        return $mutation;
    }


    public function getCustomerInfo($request)
    {
       $conId = $request->contactId;
       $locId = $request->locationId;
       $keyy = 'customer_'.$conId.$locId;

       $filters =  [[
        "column" => "dealershipSubAccountId",
        "value" =>  $locId,
        "order" => "equals"
    ],
    [
        "column" => "highlevelClientId",
        "value" => $conId,
        "order" => "equals"
    ]
];

    $request->merge(['filters' => $filters]);

       Cache::forget($keyy);
   try{
       $data = Cache::remember($keyy, 60 * 60, function () use ($request) {
        $query = $this->inventoryService->setQuery($request,null,null,'customersCollection');
        return $this->inventoryService->submitRequest($query);
        //    $query = $this->getCustomerQuery($conId, $locId);
        //    $data = $this->inventoryService->submitRequest($query, 1);
       });
       return $data;
   } catch (\Exception $e) {
       throw $e;
   }

    }

    public function getDealership($request, $conId)
    {
        $customer_id = null;
        // $conId = 'geAOl3NEW1iIKIWheJcj';
        $dealer_id = null;
        $filters = [
            "filters" => [
                "column" => "subAccountId",
                "value" => $conId,
                "order" => "equals",
            ],
        ];
        $request->merge(['filters' => $filters]);
        $keyy = 'dealerhipp_'.$conId;
            Cache::forget($keyy);
        try{
            $data = Cache::remember($keyy, 60 * 60, function () use ($conId,$request) {
            $query = $this->inventoryService->setQuery($request, null, null, 'dealershipCollection');
            // dd($query);
            return  $this->inventoryService->submitRequest($query, 1);
            });
            $res = $data['data']['dealershipCollection'];
            if (isset($res['edges']) && count($res['edges']) > 0) {
                $dealer_id = $res['edges'][0]['node']['id'];
            }

            return [$dealer_id,$res['edges'][0]['node']];
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function createContact($locationId,$data)
    {
      $id = null;
      try {
        $detail = CRM::crmV2Loc('1', $locationId, 'contacts/', 'post',$data);
        if ($detail && property_exists($detail, 'contact')) {
          $id = @$detail->contact->id;
     
        }
      }catch(\Exception $e){
      \Log::error($e);
        $id = null;
      }
      return $id;
    }
    public function getContact($locationId,$contact_id)
    {
        $fields = CustomFields::where('key',$locationId)->select('content')->first();
        $customFields = $fields ?  (json_decode($fields->content,true) ?? []) : [];
        $array = [];
        try {
            $response = CRM::crmV2Loc('1', $locationId, 'contacts/' . $contact_id, 'get');
            foreach($response->contact as $key => $c)
            {
                if($key == 'customFields')
                {
                    foreach($c as $cf)
                    {
                       $k = @$customFields[$cf->id] ?? null;
                       if($k)
                       {
                         $array[$k['fieldKey']] = $cf->value;
                       }
                    }
                }
                if(is_array($c) || is_object($c))
                {
                    continue;
                }
                $array[$key] = $c;
            }
            return $array;
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function getContactDeals($locationId,$contactId,$type = 'customerMapping',$only_id = null)
    {
        $deals = [];
        try {
            $query = $type == 'customerMapping' ? $this->getDealsByCustomerQuery($locationId,$contactId) : $this->getDealsByCoborrowerQuery($locationId,$contactId);
            $data = $this->inventoryService->submitRequest($query, 1);
            // dd($data);
            $res = $data['data']['dealsCollection'];
            if (isset($res['edges'])) {
                foreach ($res['edges'] as $edge) {
                    $item = $edge['node'];
                    if($only_id)
                    {
                       $deals[] = $item['id'];
                    }else{
                        $deals[] = ['id' => $item['id'], 'status' => $item['dealStatus'], 'type' => $item['dealType'], 'uuid' => $item['uuid'], 'docType' => $item['docFee']];
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $deals;
    }

}