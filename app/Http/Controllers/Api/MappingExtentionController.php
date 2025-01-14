<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMapingUrl;
use Illuminate\Support\Str;
 use App\Models\CrmAuths;
use App\Services\Api\InventoryService;

class MappingExtentionController extends Controller
{
    public function store(Request $request)
    {

        // return $request->all();
        $url = null;
        $token = null;
        if ($request->hasHeader('token')) {
            $token = $request->header('token');
            $url = AdminMapingUrl::where('uuid', $token)->first();
            if (!$url) {
                return response()->json(['success' => false, 'error' => 'Token doesnot match'], 500);
            }
        }

        $searchUrl = $request->baseUrl;
        $url = $url ?? $this->searchByUrl($searchUrl);

        $url = $url ?? new AdminMapingUrl();
        $attr = $request->fields_list;
        $related_urls = json_decode($url->related_urls, true) ?? [];
        $attr1 = json_decode($url->listed_attributes, true) ?? [];
        $attr = array_merge($attr1, $attr) ?? [];
        $attr = array_unique($attr);

        if ($url->url && $url->url != $searchUrl) {
            $related_urls[] = $searchUrl;
            $related_urls = array_unique($related_urls);
        } else {
            $url->url = $searchUrl;
        }

        $url->related_urls = json_encode($related_urls);
        $url->attributes = json_encode($attr);
        $url->listed_attributes = json_encode($attr);

        if (!$url->uuid) {
            $uuid = Str::random(12);
            $url->uuid = $uuid;
        }
        $url->save();
        // $url = AdminMapingUrl::updateOrCreate(['url' => $request->baseUrl], [
        //     'attributes' => json_encode($request->fiels_list),
        // ]);

        $data = [
            'token' => $url->uuid,
            'mapping_url' => route('admin.mappings.custom.form', $url->uuid),
        ];

        return response()->json(['success' => true, 'extention' => $data], 200);

    }

    public function getMapUrl(Request $request)
    {
        $url = $this->searchByUrl($request->url);
        if (!$url) {
            return response()->json(['success' => true, 'error' => 'Record Not Found'], 500);
        }
        $attr = $url->listed_attributes ?? $url->attributes;
        $data = [
            'uuid' => $url->uuid,
            'attributes' => json_decode($attr),
            'mapping' => json_decode($url->mapping),
            'searchable_fields' => json_decode($url->searchable_fields),
            'displayable_fields' => json_decode($url->displayable_fields),
            'related_urls' => json_decode($url->related_urls),
            'table' => json_decode($url->table),
        ];

        return response()->json(['success' => true, 'form' => $data], 200);
    }

    public function search(Request $request, InventoryService $inventoryService)
    {
        $messsage = 'location token is invalid';
        $filter = $request->dealsFilter;
        if(!$filter)
        {
            return response()->json(['error' => 'There is issue in your payload']);
        }

        $location = @$filter['location'] ?? null;
        if (!$location) {
            return response()->json(['error' => 'first']);
        }
        try {
            $loc = CrmAuths::where('location_id', $location)->first();
            if (!$loc) {
                $res = CRM::getLocationAccessToken(1, $location);
                $code = $res->statusCode ?? 200;
                if ($code != 200) {
                    return response()->json(['error' => $messsage]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $messsage]);
        }

        $whereClause = @$filter['ids'] ? $this->setIdsFilter($filter['ids']) : null;
        // if($whereClause)
        // {
        //     $request->merge(['filters' => $fil]);
        // }

        try {
            $query = $inventoryService->setQuery($request, null, $whereClause, 'dealsCollection' );
            // return $query;
            $data = $inventoryService->submitRequest($query);
            $data = @$data['data']['dealsCollection']['edges'] ?? [];
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['data' => [],'error' => 'there is some issues']);
        }
    }

    public function setIdsFilter($ids)
    {
        $column = 'uuid';
        $order = 'in';
        $quotedIds = array_map(fn($id) => '"' . $id . '"', $ids);

// Create the string of IDs
$string = implode(', ', $quotedIds);

// Construct the where clause
$whereClause = "{ {$column}: { {$order}: [{$string}] } }";

// Return the query
return $whereClause;
        // where: { uuid: { in: ["64", "66"] } }
        $filters = [];
        foreach($ids as $id)
        {
          $filters[] = [
            "column" => "uuid",
            "value" =>  $id,
            "order" => "equals"
          ];
        }
        return $filters;
    }

    public function _search(Request $request, InventoryService $inventoryService)
    {
        $url = $this->searchByUrl($request->url);
        if (!$url || !$url->mapping) {
            return response()->json(['success' => false, 'error' => 'Record Not Found'], 500);
        }
        $searchable_fields = json_decode($url->searchable_fields, true) ?? [];
        list($whereClause, $filterFields) = $inventoryService->setFilters($request, null, true);
        $diff = array_diff($filterFields, $searchable_fields);
        if (count($diff) > 0) {
            return response()->json(['success' => false, 'error' => 'Filter does not match'], 500);
        }
        try {
            $query = $inventoryService->setQuery($request, null, $whereClause, $url->table);
            $data = $inventoryService->submitRequest($query);
        } catch (\Exception $e) {
            return $e;
        }

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function searchByUrl($searchUrl)
    {
        return AdminMapingUrl::where('url', $searchUrl)
            ->orWhereJsonContains('related_urls', $searchUrl)
            ->first();
    }

}
