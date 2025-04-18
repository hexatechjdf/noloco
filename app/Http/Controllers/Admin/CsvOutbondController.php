<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FtpService;
use App\Models\CsvMapping;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;

class CsvOutbondController extends Controller
{
    protected $ftpService;
    protected $inventoryService;
    protected $dealService;

    public function __construct(FtpService $ftpService,InventoryService $inventoryService,DealService $dealService)
    {
        $this->ftpService = $ftpService;
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;

    }

    public function index()
    {
        $items = CsvMapping::withCount(['locations','accounts'])->where('type','export')->paginate(10);

        return view('admin.mapings.csv.outbound.index', get_defined_vars());
    }

    public function create($id = null)
    {
        $fields = json_decode(supersetting('invCustomTypeColumns'), true);

        $item = null;
        $title = null;
        $mapping = [];
        if($id)
        {
            $item = CsvMapping::where('id',$id)->first();
            $fields = json_decode($item->content, true) ?? [];
            $title = $item->title;
        }

        return view('admin.mapings.csv.outbound.create', get_defined_vars());
    }

    public function store(Request $request,$id = null)
    {
        dd($request->all());
        DB::transaction(function () use(&$result) {
            // logic here
            $result = someGetResult();
        });
        $mapping = json_encode($request->maps);
        $item = CsvMapping::updateOrCreate(['id'=>$id],
                [
                    'content' => $mapping,
                    'type' => 'export',
                    'title' => $request->title,
                ]);

        return response()->json(['success' => true, 'route' => route('admin.mappings.csv.outbound.index')]);
    }

    public function exportInv()
    {
        $locationId = 'geAOl3NEW1iIKIWheJcj';
        $allEdges = $this->getList($locationId);
        $mapping = CsvMapping::where('type', 'export')->first();

        $fields = json_decode($mapping->content, true) ?? [];
        $filename = 'inventory_export_' . now()->format('Ymd_His') . '.csv';
        $filePath = storage_path('app/public/export/' . $filename);

        // Create directory if not exists
        if (!file_exists(storage_path('app/public/export'))) {
            mkdir(storage_path('app/public/export'), 0755, true);
        }

        $file = fopen($filePath, 'w');

        fputcsv($file, array_values($fields));
        foreach ($allEdges as $item) {
            $node = $item['node'];
            $row = [];

            foreach ($fields as $key => $header) {
                $row[] = $node[$key] ?? '';
            }
            fputcsv($file, $row);
        }
        fclose($file);
    }

    public function getList($locationId)
    {
        $allEdges = [];
        $after = null;
        $filters = [
            "filters" => [
                "column" => 'dealershipSubAccountId',
                "value" => $locationId,
                "order" => "equals",
            ],
            "after" => $after,
        ];
        $request = request();
        $request->merge(['filters' => $filters]);
        do {
            $query = $this->inventoryService->setQuery($request);
            $data = $this->inventoryService->submitRequest($query);
            $data = @$data['data'];

            if (!empty(@$data['inventoryCollection']['edges'])) {
                $allEdges = array_merge($allEdges, $data['inventoryCollection']['edges']);
            }

            $pageInfo = $data['inventoryCollection']['pageInfo'] ?? [];
            $after = $pageInfo['hasNextPage'] ? $pageInfo['endCursor'] : false;
            $request['after'] = $after;
        } while ($after);

        return $allEdges;
    }
}
