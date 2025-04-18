<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class InventoryService
{
    public function setQuery($request, $id = null, $filters = null, $table_name = null,$isAll = null)
    {
        $table_name = $table_name ?? 'inventoryCollection';
        $after = $request->after;
        $before = $request->before;
        $query = $this->getQuery($table_name,'title','ASC', $isAll);
        // return $query;
        $whereClause = ($filters ? $filters : $this->setFilters($request, $id)) ?: '{}';
        $sortingClause = $table_name == 'inventoryCollection' ? $this->getSorting($request) : "";
        // return $whereClause;

        $afterParam = $after ? ", after: \"$after\"" : "";
        $beforeParam = $before ? ", before: \"$before\"" : "";
        // $str = $str != '' || $request->before ? 'before: $request->before' : '';
        $query = str_replace(
            ['%a', '%b', '%s', '%t'],
            [$afterParam, $beforeParam, $whereClause, $sortingClause],
            $query
        );

        return $query;
    }

    public function setQueryInventoryIds($request,$locationId = null,$isAll= null)
    {
        $table_name = 'inventoryCollection';

        $query = <<<GRAPHQL
        query {
          inventoryCollection( where: {dealershipSubAccountId: {equals: %s}, OR: [{status: {equals: "INACTIVE"}}, {status: {equals: "ACTIVE"}}]}) {
            edges {
              node {
                id
                stock
                vin
                status
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
            ['%s'],
            ["\"$locationId\""],
            $query
        );

        return $query;
    }

    public function getSorting($request)
    {

        $sorting = $request->sorting;
        $sortColumn = 'name';
        $sortDirection = 'ASC';
        if (!empty($sorting) && isset($sorting['column']) && isset($sorting['direction'])) {
            $sortColumn = $sorting['column'];
            $sortDirection = $sorting['direction'];

            if (!in_array(strtoupper($sortDirection), ['ASC', 'DESC'])) {
                $sortDirection = 'ASC';
            }
        }
        return ", orderBy: { field :  \"$sortColumn\", direction: $sortDirection }";
    }

    public function getQuery($tableName = null, $sortColumn = 'title', $sortDirection = 'ASC',$isAll = false)
    {
        $tableName = $tableName ?? 'inventoryCollection';
        $fields = fields($tableName,$isAll); // Get the array of fields from the helper function

        $fieldsString = '';

        if($isAll)
        {
           $fieldsString = buildGraphQLFields($fields);
        }else{
            foreach ($fields as $field) {
                $fieldsString .= is_array($field)
                    ? sprintf("%s { %s } ", key($field), implode(' ', current($field)))
                    : $field . "\n";
            }
        }

        $query = <<<GRAPHQL
        query {
          $tableName(first: 5  %a %b %t , where: %s) {
            edges {
              node {
                $fieldsString
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

        return $query;
    }


    public function setTableQuery($tables)
    {
        $tables = $tables ?? [];
        $queryParts = [];
        foreach ($tables as $table) {
            $name = ucfirst(str_replace('Collection', '', $table));
            $queryParts[] = <<<GRAPHQL
            $table: __type(name: "$name") {
              fields {
                name
                type {
                  name
                  kind
                  ofType {
                    name
                    kind
                  }
                  fields {
                    name
                    type {
                      name
                      kind

                      fields {
                        name
                        type {
                          name
                          kind
                        }
                      }
                    }
                  }
                }
              }
            }
            GRAPHQL;
        }
        $combinedQueryParts = implode("\n", $queryParts);
        return <<<GRAPHQL
            query {
              {$combinedQueryParts}
            }
            GRAPHQL;
    }

    public function updateQuery($id, $images, $featuredImageId)
    {
        // return [$images, $id];
        $query = <<<GRAPHQL
        mutation {
          updateInventory(
            id: "%id",
            photosUrls: "%photos"
            featuredPhotoId: %featuredImageId
          ) {
            id
            photosUrls
             featuredPhoto {
                    url
            }
          }
        }
        GRAPHQL;

        $query = str_replace(
            ['%id', '%photos', '%featuredImageId'],
            [$id, $images, $featuredImageId],
            $query
        );

        return $query;
    }

    public function createFileQuery($url)
    {
        $query = <<<GRAPHQL
          mutation {
            createFile(
              url: "%url",
              name: "image.jpg",
            ) {
              id
              url
              name
            }
          }
        GRAPHQL;

        $query = str_replace(
            ['%url'],
            [$url],
            $query
        );
        return $query;

    }

    public function getTableQuery($request)
    {
        $query = <<<GRAPHQL
        {
            __type(name: "Query") {
              fields {
                name
                type {
                  name
                  kind
                }
              }
            }
          }
        GRAPHQL;

        return $query;
    }

    public function setFilters($request, $id = null, $is_optional = false)
    {
        $whereClause = '';
        $filterParts = [];
        $filterFields = [];
        if ($request->filters) {
            try {
                $filters = $request->filters;
                if (!empty($filters)) {
                    foreach ($filters as $key => $filter) {
                        if (is_array($filter) && array_keys($filter) === range(0, count($filter) - 1)) {
                            $filterGroupParts = [];
                            $name = "";
                            foreach ($filter as $f) {
                                $filterGroupParts[$f['order']] = $f['value'];
                                $name = $f['column'];
                            }

                            $filterParts[] = ' OR: [{' . $name . ': {' . implode(', ', array_map(fn($key, $value) => "$key: $value", array_keys($filterGroupParts), $filterGroupParts)) . '}}]';
                        } else {
                            $filterParts[] = $this->filterField($filter);
                            if (isset($filter['column'])) {
                                $filterFields[] = $filter['column'];
                            }
                        }
                    }
                    if (!empty($filterParts)) {
                        $whereClause = '{ ' . implode(', ', $filterParts) . ' }';
                        // if ($is_optional) {
                        //     $whereClause = '{ OR: [' . implode(', ', $filterParts) . '] }';
                        // } else {
                        //     $whereClause = '{ ' . implode(', ', $filterParts) . ' }';
                        // }
                    }
                }
            } catch (\Exception $e) {
                // Handle any errors that might occur
            }
        }

        if ($id) {
            $filterParts[] = $this->setFilterColumns('uuid', $id, 'equals');
            $whereClause = '{ ' . implode(', ', $filterParts) . ' }';
        }
        if ($is_optional) {
            return [$whereClause, $filterFields];
        }

        return $whereClause;
    }



    public function filterField($filter, $type = null)
    {
        if (isset($filter['column'], $filter['value'], $filter['order'])) {
            return $this->setFilterColumns($filter['column'], $filter['value'], $filter['order'], $type);
        }
    }

    public function setFilterColumns($column, $value, $order, $type = null)
    {
        $formattedValue = is_numeric($value) ? $value : "\"$value\"";

        if ($type == 'array') {
            return "$order: $formattedValue ";
        }
        // if ($order == 'equals') {
        //     $v = "^(?i){$value}$";
        //     return "{$column}: { equals: $v }";
        // }
        return "{$column}: { {$order}: $formattedValue }";
    }

    public function submitRequest($query,$test = 1,$values = null,$retry = 0)
    {
        $try = 1;
        $appApiKey = supersetting('noloco_app_key', null);
        $appName = supersetting('noloco_app_name', null);

        if ($appApiKey && $appName) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $appApiKey,
                    'Content-Type' => 'application/json',
                ])->post("https://api.portals.noloco.io/data/{$appName}", [
                            'query' => $query,
                            'variables' => $values
                        ]);
                        
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data;
                }else{
                    $data = $response->json();
                    if(isset($data['errors']) && $values)
                    {
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                // dd($e);
                throw $e;
            }
        }
        return [];
    }

    public function setInventoryDataByCsv($graphqlPayload,$invMutationType)
    {
        if( $invMutationType == 'updateInventory')
        {
            $mutation = <<<GRAPHQL
                mutation bulkUpdateInventory(\$graphqlPayload: [InventoryInput!]!) {
                    bulkUpdateInventory(values: \$graphqlPayload) {
                        id
                    }
                }
           GRAPHQL;
        }
        else{
            $mutation = <<<GRAPHQL
                mutation {
                    $invMutationType($graphqlPayload) {
                        id
                    }
                }
            GRAPHQL;
        }
        return $mutation;
    }
}