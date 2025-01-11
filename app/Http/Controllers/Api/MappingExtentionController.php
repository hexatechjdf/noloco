<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMapingUrl;
use Illuminate\Support\Str;
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
