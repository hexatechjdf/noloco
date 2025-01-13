<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\Api\InventoryService;
use App\Models\Setting;
use App\Helper\CRM;
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



    public function getDealsByCustomerQuery($customerId, $first = 100)
    {
        $query = <<<GRAPHQL
        query {
          dealsCollection(first: 100 ,where: {customerId: {equals: "%c"} dealStatus:{equals: "OPEN"}}) {
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
            ['%c'],
            [$customerId],
            $query
        );

        return sprintf($query, $first, $customerId);
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

    public function updateDealQuery($customer_id,$dealId)
    {
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


    public function getCustomerInfo($request)
    {
       $conId = $request->contactId;
       $locId = $request->locationId;
       $keyy = 'customer_'.$conId.$locId;
    //    Cache::forget($keyy);
   try{
       $data = Cache::remember($keyy, 60 * 60, function () use ($conId,$locId) {
           $query = $this->getCustomerQuery($conId, $locId);
           $data = $this->inventoryService->submitRequest($query, 1);
           return $data;
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
        $keyy = 'dealerhip_'.$conId;
        try{
            $data = Cache::remember($keyy, 60 * 60, function () use ($conId,$request) {
            $query = $this->inventoryService->setQuery($request, null, null, 'dealershipCollection');
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

    public function getContact($locationId,$contact_id)
    {
         $contact_id = "Aiml0qxtPRr1fiK5mOf3";
        // dd($locationId,$contact_id);
        try {
            $response = CRM::crmV2Loc('1', $locationId, 'contacts/' . $contact_id, 'get');
            return $response->contact;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
