<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CsvMapping;
use App\Models\CsvMappingLocation;
use App\Http\Requests\Admin\CsvMappingRequest;
use App\Http\Requests\Admin\FtpAccountRequest;
use App\Http\Requests\Admin\CsvLocationRequest;
use App\Services\FtpService;
use App\Models\FtpAccount;
use Illuminate\Support\Facades\File;
use App\Services\Api\InventoryService;
use Illuminate\Support\Facades\DB;
use App\Services\Api\DealService;
use App\Jobs\GetFoldersJob;


class CsvMappingController extends Controller
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
        $items = CsvMapping::withCount(['locations','accounts'])->whereNull('type')->paginate(10);

        return view('admin.mapings.csv.inbound.index', get_defined_vars());
    }

    public function create($id = null)
    {
        // $fields = fields('inventoryCollection');
        $fields = json_decode(supersetting('invCustomTypeColumns'), true) ?? $this->nolocoCustomColumnsWithType('inventoryCollection', 'invCustomTypeColumns');
        $item = null;
        $title = null;
        $mapping = [];
        $unique_field = '';
        if($id)
        {
            $item = CsvMapping::where('id',$id)->first();
            $mapping = json_decode($item->content, true) ?? [];
            $unique_field = $item->unique_field;
            $title = $item->title;
        }
        return view('admin.mapings.csv.inbound.create', get_defined_vars());
    }

    public function nolocoCustomColumnsWithType($tableName = 'dealsCollection',$key = 'dealsCustomTypeColumns')
    {
        $final = [];
        try {
            $query = $this->inventoryService->setTableQuery([$tableName]);
            $data = $this->inventoryService->submitRequest($query,1);
            $fields = $data['data'][$tableName]['fields'];
            $final = $this->fetchNonObjectColumns($fields) ?? [];

            save_settings($key, $final);
            return $final;
        } catch (\Exception $e) {
            return $final;
        }
    }

    public function fetchNonObjectColumns($fields, $parent_key = null)
    {
        $final = [];

        foreach ($fields as $f) {
            $k = @$f['type']['kind'];
            $m = @$f['type']['name'];
            $name = $f['name'];
            $t = $k == 'SCALAR' ? $m : $k;
            if($t != 'OBJECT')
            {
                $final[$name] =  $t;
            }
        }

        return $final;
    }

    public function store(CsvMappingRequest $request,$id = null)
    {
        $headers = $request->headerss ?? [];
        $count = 0;
        $data = [];
        foreach($request->mapping as $key => $map)
        {
            if(!$map)
            {
                $count++;
                continue;
            }

              if(in_array($key, $headers))
              {
                 $data[$key] = ['column' => $map];
              }elseif(isset($headers[$count])){
                     $ky = $headers[$count];
                     $data[$ky] = ['column' => $map];
              }
              $count++;

        }

        $mapping = json_encode($data);
        $item = CsvMapping::updateOrCreate(['id'=>$id],
        [
            'content' => $mapping,
            'title' => $request->title,
            'unique_field' => $request->unique_field,
        ]
       );

        return response()->json(['success' => true, 'route' => route('admin.mappings.csv.index')]);
    }

    public function ftp(FtpAccountRequest $request)
    {
        $options = [];
        foreach ($request['options']['keys'] as $index => $key) {
            $options[] = [
                'key' => $key,
                'value' => $request['options']['values'][$index] ?? null,
                'type' => $request['options']['types'][$index] ?? null
            ];
        }
        $request['location_ids'] = json_encode($options);
        $acc = FtpAccount::when($request->id, function($q)use($request){
                     $q->where('id','!=',$request->id);
        })->where('location_id',$request->location_id)->first();

        if($acc)
        {
                return response()->json([
                    'errors' => [['Location already exist']], // Expected format by the front-end
                    'message' => 'Validation failed',
                ], 422);
        }
        list($res,$errors)  = $this->ftpService->createAccount($request);

        if($errors)
        {
            return response()->json([
                'errors' => [$errors], // Expected format by the front-end
                'message' => 'Validation failed',
            ], 422);
        }

        return response()->json(['success' => true, 'route' => route('admin.mappings.csv.index')]);
    }

    public function ftpDelete(Request $request)
    {
        list($res,$errors)  = $this->ftpService->deleteAccount($request);

        if($errors)
        {
            return response()->json([
                'errors' => [$errors], // Expected format by the front-end
                'message' => 'Validation failed',
            ], 422);
        }

        return response()->json(['success' => true]);
    }

    public function ftpAccountsList(Request $request)
    {
        $accounts = FtpAccount::where('mapping_id',$request->csv_id)->get();
        $idd = $request->csv_id;

        $view = view('admin.mapings.csv.components.ftpSetting', get_defined_vars())->render();

        return response()->json(['success' => true, 'html' => $view]);
    }
    public function ftpForm(Request $request, $id = null)
    {
        $account = null;
        $setting = supersetting('ftp_setting', '', 'ftp_%');
        if($id)
        {
            $account = FtpAccount::findOrFail($id);
        }

        return view('admin.mapings.csv.inbound.account', get_defined_vars());
    }

    public function manage($id)
    {
        $map = CsvMapping::with(['locations','accounts'])->findOrFail($id);
        $items = json_decode($map->content,true) ?? [];

        return view('admin.mapings.csv.inbound.manage', get_defined_vars());
    }

    // .........................
    public function setCvsFiles()
    {
        // $folders = $this->getFolders();
        // dd($folders);
        $folders  = [
            "mapping1" => [
                "unique" => "ID",
                "mapping" => [
                    "ID" => [
                        "column" => "id__NON_NULL"
                    ],
                    "VIN_Number" => [
                        "column" => "vin__String"
                    ],
                    "Year_Made" => [
                        "column" => "year__Int"
                    ],
                    "Car_Brand" => [
                        "column" => "make__String"
                    ],
                    "Car_Model" => [
                        "column" => "model__String"
                    ],
                    "Model_Type" => [
                        "column" => "fuelType__String"
                    ],
                    "Status" => [
                        "column" => "status__ENUM"
                    ],
                    "Type" => [
                        "column" => "fuelType__String"
                    ],
                    "Selling_Price" => [
                        "column" => "vehicleCost__Float"
                    ],
                    "Cost_Price" => [
                        "column" => "listedPrice__Float"
                    ],
                    "Final_Price" => [
                        "column" => "vehicleCost__Float"
                    ],
                    "Stock_Availability" => [
                        "column" => "stock__Int"
                    ],
                ],
                "files" => [
                    "app/csvfiles/mapping1/geAOl3NEW1iIKIWheJcj_20250410111613_csv1.csv",
                    "app/csvfiles/mapping1/geAOl3NEW1iIKIWheJcj_20250410111613_csv12.csv"
                ],
                "locationId" => "geAOl3NEW1iIKIWheJcj"
            ]
        ];


        foreach($folders as $folder => $data)
        {
            $files = $data['files'];
            $unique = $data['unique'];
            $mapping = $data['mapping'];
            $locationId = $data['locationId'];


            foreach ($files as $csvFile) {
                $rows = $this->parseCsvFile($csvFile);
                $existInventoryIds = $this->inventoryIds($locationId);
                // foreach ($rows as $fields) {
                //         $val = @$fields[$unique];
                //         $key = @$mapping[$unique];

                //         // if($key && $val)
                //         // {

                //         // }
                //         $invType = 'createInventory';
                //         $filters = $this->setFilter($val,$key['column']);
                //         // list($dealer_id,$dealership) =  $this->dealService->getDealership(request(),$locationId);
                //         // $dealer_id = 9;

                //         list($id, $existInventoryIds) = $this->isExist($key,$val,$filters,$locationId,$existInventoryIds);

                //         $data = [];

                //         foreach($mapping as $k => $map)
                //         {
                //             list($type,$value) = convertStringToArray('__', $map['column']);
                //             if (isset($fields[$k]) && $fields[$k] != '') {
                //                 $data[$value] = $fields[$k].'__'.$type;
                //             }elseif(!isset($fields[$k])){
                //                 $data[$value] = $k.'__'.$type;
                //             }
                //         }

                //         if($id)
                //         {
                //             $data['id'] = $id.'__Int';
                //             $invType = 'updateInventory';
                //             try{
                //                 $dId = supersetting('deal_dealership_col') ?? '';
                //                 $data[$dId] = $dealer_id.'__Int';
                //             }catch(\Exception $e){
                //             }
                //             $variables = ['graphqlPayload' => [arrayToGraphQL1($data,'inventoryCollection')]];

                //             dd($variables);

                //             try {
                //                 $query = $this->inventoryService->setInventoryDataByCsv($variables,$invType);
                //                 $data = $this->inventoryService->submitRequest($query,1,$variables);

                //                 dd($data);

                //             } catch (\Exception $e) {
                //                dd($e);
                //             }
                //         }


                // }

                $invType = 'updateInventory';
                if(count($existInventoryIds) > 0)
                {
                    foreach($existInventoryIds as $ke => $exitt)
                    {
                        $pl = [
                            'id' => $ke.'__Int',
                            'status' => 'SOLD__ENUM',
                        ];
                        $variables = ['graphqlPayload' => [arrayToGraphQL1($pl,'inventoryCollection')]];
                        $query = $this->inventoryService->setInventoryDataByCsv($variables,$invType);
                        $data = $this->inventoryService->submitRequest($query,1,$variables);
                    }
                }
            }
        }

        dd(!23);

    }

    public function getFolders()
    {
        $basePath = base_path('../csvfiles'); // Parent folder containing multiple folders
        $folders = File::directories($basePath);

        $csvFolders = [];

        foreach ($folders as $folder) {
            $folderName = basename($folder); // Get folder name

            $acc = FtpAccount::with('mapping')
            ->where('location_id','!=',null)
            ->where('username',$folderName)
            ->select('username', 'mapping_id', 'id','location_id')
            ->first();

            if($acc)
            {
                $mapping = json_decode(@$acc->mapping->content, true) ?? [];
                $locationId =  $acc->location_id;
                $unique = $acc->mapping->unique_field;

                $files = File::files($folder); // Get all files in the folder
                $csvFiles = [];
                $path = 'app/csvfiles/';
                $storagePath = public_path($path.$folderName);
                if (!File::exists($storagePath)) {
                    File::makeDirectory($storagePath, 0755, true);
                }

                foreach ($files as $file) {
                    $file_name = $file->getFilename();
                    if ($file->getExtension() === 'csv' && !str_contains($file_name, 'export')) {
                        $newFileName = $locationId . '_' . now()->format('YmdHis') . '_' . $file_name;
                        $newFilePath = $storagePath . '/' . $newFileName;
                        File::copy($file->getPathname(), $newFilePath);
                        $csvFiles[] = $path. $folderName.'/' . $newFileName;
                    }
                }

                if (!empty($csvFiles)) {
                    $csvFolders[$folderName] = ['unique' => $unique,'mapping' => $mapping, 'files' => $csvFiles,'locationId' => $locationId];
                }
            }
        }
        return  $csvFolders;
    }

    public function setFilter($value,$key)
    {
        list($type,$col) = convertStringToArray('__', $key);

        // $col = 'id';
        // $value = '10';
        // $res = checkValueByType($type,$key,$value);
        // list($r1,$r2) = convertStringToArray(': ', $res);
        $filters = [
            "filters" => [
                "column" => $col,
                "value" => $value,
                "order" => "equals",
            ],
        ];

        return $filters;
    }

    public function inventoryIds($locationId)
    {
        $stockids = [];
        $filters = [
            "filters" => [
                "column" => 'dealershipSubAccountId',
                "value" => $locationId,
                "order" => "equals",
            ],
        ];
        $request = request();
        $request->merge(['filters' => $filters]);
        $id = null;
        try {
            $query = $this->inventoryService->setQueryInventoryIds($request,$locationId);
            $data = $this->inventoryService->submitRequest($query);
            $stocks = @$data['data']['inventoryCollection']['edges'];
            if($stocks)
            {
                foreach($stocks as $stock)
                {
                    $s = $stock['node'];
                    if(@$s['stock'] && @$s['id'])
                    {
                        $stockids[@$s['id']] = @$s['stock'];
                    }
                }
            }

        } catch (\Exception $e) {
        }
        return $stockids;
    }


    public function isExist($key,$value,$filters,$locationId,$existInventoryIds)
    {
        $id = null;
        if(in_array($value, $existInventoryIds))
        {
            $id = array_search($value, $existInventoryIds);
            unset($existInventoryIds[$id]);
        }
        else{
            $id =  rand(1,100);
            // try{
            //     list($dealer_id,$dealership) =  $this->dealService->getDealership(request(),$locationId);
            //     $dId = supersetting('deal_dealership_col') ?? '';
            //     $ar[$dId] = $dealer_id.'__Int';
            // }catch(\Exception $e){
            // }
            // $graph = arrayToGraphQL($ar);

            // try {
            //     $query = $this->inventoryService->setInventoryDataByCsv($graph,'createInventory');

            //     $inv = $this->inventoryService->submitRequest($query);
            //     $id = @$inv['data']['createInventory']['id'];
            // } catch (\Exception $e) {
            // }
        }

        return [$id, $existInventoryIds];
        $request = request();
        $request->merge(['filters' => $filters]);
        $id = null;
        try {
            $query = $this->inventoryService->setQuery($request);
            $data = $this->inventoryService->submitRequest($query);
            $id = @$data['data']['inventoryCollection']['edges'][0]['node']['id'];
            if(!$id)
            {
                try{
                    list($dealer_id,$dealership) =  $this->dealService->getDealership(request(),$locationId);
                    $dId = supersetting('deal_dealership_col') ?? '';
                    $ar[$dId] = $dealer_id.'__Int';
                }catch(\Exception $e){
                }
                $graph = arrayToGraphQL($ar);

                try {
                    $query = $this->inventoryService->setInventoryDataByCsv($graph,'createInventory');

                    $inv = $this->inventoryService->submitRequest($query);
                    $id = @$inv['data']['createInventory']['id'];
                } catch (\Exception $e) {
                }
            }

        } catch (\Exception $e) {

        }
        return $id;
    }


     /**
     * Parse the CSV file and map data to headers.
     *
     * @param string $filePath
     * @return array
     */
    private function parseCsvFile($filePath)
    {
        $filePath = public_path(trim($filePath));
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $data = []; // Initialize data array

        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if ($headers && count($headers) == count($row)) {
                    // Remove rows where all values are empty
                    if (!empty(array_filter($row))) {
                        $data[] = array_combine($headers, $row);
                    }
                }
            }

            fclose($handle);
        }
        return $data;
    }

    public function testRun(Request $request)
    {
        dispatch((new GetFoldersJob()))->delay(5);
        // $this->setCvsFiles();

        return response()->json(['success' => true],200);
    }

}
