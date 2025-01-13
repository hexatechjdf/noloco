<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class DealService
{
    public function getCustomerQuery($dealershipSubAccountId, $highlevelClientId)
    {
        $query = <<<GRAPHQL
            query {
              customersCollection(where: { dealershipSubAccountId: { equals: "%s" }, highlevelClientId: { equals: "%l" } }) {
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
            ['%s', '%l'],
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

}
