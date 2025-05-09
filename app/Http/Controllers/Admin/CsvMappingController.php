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
use App\Jobs\Import\GetAccountsJob;


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

    public function ftpAccountsList(Request $request)
    {
        $accounts = FtpAccount::where('mapping_id',$request->csv_id)->get();
        $idd = $request->csv_id;

        $view = view('admin.mapings.csv.components.ftpSetting', get_defined_vars())->render();

        return response()->json(['success' => true, 'html' => $view]);
    }

    public function ftpForm(Request $request,$csvId, $id = null)
    {
        $account = null;
        $setting = supersetting('ftp_setting', '', 'ftp_%');
        if($id)
        {
            $account = FtpAccount::findOrFail($id);
        }

        return view('admin.mapings.csv.inbound.account', get_defined_vars());
    }

    public function ftp(Request $request)
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



    public function manage($id)
    {
        $map = CsvMapping::with(['locations','accounts'])->findOrFail($id);
        $items = json_decode($map->content,true) ?? [];

        return view('admin.mapings.csv.inbound.manage', get_defined_vars());
    }

    // .........................
    public function setCvsFiles(InventoryService $inventoryService,DealService $dealService)
    {
        // run job
        $accounts = FtpAccount::with('mapping')
        ->where('location_id','!=',null)
        ->select('username', 'mapping_id', 'id','location_id')
        ->get();

        $folders = $this->getFolders();

        foreach($accounts as $acc)
        {
            // run job
            $mapping = json_decode(@$acc->mapping->content, true) ?? [];
            $locations = json_decode(@$acc->location_id, true) ?? [];
            $unique = $acc->mapping->unique_field;
            $username = $acc->username;
            $files = @$folders[$username];

            foreach($locations as $loc)
            {
                // run job
                $locationId = $loc['key'];
                $fName = $loc['value'] . '.csv';
                $type = $loc['type'];

                if(in_array($files,$fName))
                {
                    $p = $username . '/' . $fname;
                    $sourcePath = base_path('../csvfiles/' . $p);

                    $relativePath = 'app/csvfiles/' . $username;
                    $storagePath = public_path($relativePath);

                    if (!File::exists($storagePath)) {
                        File::makeDirectory($storagePath, 0755, true);
                    }

                    $destinationPath = $storagePath . '/' . $fname;
                    File::copy($sourcePath, $destinationPath);

                    $csvData = [];
                    if (File::exists($destinationPath)) {
                        // run jobb
                        $rows = $this->parseCsvFile($destinationPath);

                        $existInventoryIds = $this->inventoryIds($locationId);
                        $rowStocks = [];
                        foreach($rows as $fields)
                        {
                            // run jobb
                            $val = @$fields[$unique];
                            $key = @$mapping[$unique];

                            if($key && $val)
                            {
                                $invType = 'createInventory';
                                $filters = $this->setFilter($val,$key['column']);
                                try{
                                    list($dealer_id,$dealership) =  $dealService->getDealership(request(),$locationId);
                                }catch(\Exception $e){
                                }

                                list($id, $existInventoryIds) = $this->isExist($key,$val,$filters,$inventoryService,$dealService,$dealer_id,$existInventoryIdss,$unique);

                                $data = [];
                                foreach($mapping as $k => $map)
                                {
                                    list($type,$value) = convertStringToArray('__', $map['column']);
                                    if (isset($fields[$k]) && $fields[$k] != '') {
                                        $data[$value] = $fields[$k].'__'.$type;
                                    }elseif(!isset($fields[$k])){
                                        $data[$value] = $k.'__'.$type;
                                    }
                                }

                                if($id && $type != 'manual')
                                {
                                    $data['id'] = $id.'__Int';
                                    $invType = 'updateInventory';
                                    try{
                                        $dId = supersetting('deal_dealership_col') ?? '';
                                        $data[$dId] = $dealer_id.'__Int';
                                    }catch(\Exception $e){
                                        Log::error('error file:'.$locationId.'=>' .$e);
                                    }
                                    $variables = ['graphqlPayload' => [arrayToGraphQL1($data,'inventoryCollection')]];

                                    dispatch((new UpdateMapInvJob($variables,$invType, $id)));
                                }
                            }
                            $rowStocks[] = @$fields[$unique] ?? null;
                        }

                        if(count($existInventoryIds) > 0 && $type != 'manual')
                        {
                            $result = array_filter($existInventoryIds, function($value) use ($rowStocks) {
                                return !in_array($value, $rowStocks);
                            });
                            foreach($result as $ke => $exitt)
                            {
                                $pl = [
                                    'id' => $ke.'__Int',
                                    'status' => 'SOLD__ENUM',
                                ];
                                $variables = ['graphqlPayload' => [arrayToGraphQL1($pl,'inventoryCollection')]];
                                dispatch((new UpdateMapInvJob($variables,'updateInventory', $ke)));
                            }
                        }

                    }


                }

            }


        }

    }

    public function isExist($key,$value,$filters,$inventoryService,$dealService,$dealer_id,$existInventoryIds = [],$unique)
    {
        $id = null;
        if(in_array($value, $existInventoryIds))
        {
            $is_update = true;
            $id = array_search($value, $existInventoryIds);
            unset($existInventoryIds[$id]);
        }
        else{
            $ar = [];
            $ar[strtolower($unique)] = $value.'__String';
            try{
                $dId = supersetting('deal_dealership_col') ?? '';
                $ar[$dId] = $dealer_id.'__Int';
            }catch(\Exception $e){
            }
            $graph = arrayToGraphQL($ar);
            try {
                $query = $inventoryService->setInventoryDataByCsv($graph,'createInventory');

                $inv = $inventoryService->submitRequest($query);
                $id = @$inv['data']['createInventory']['id'];
            } catch (\Exception $e) {
                $id = null;
            }
        }

        return [$id, $existInventoryIds];
    }
    public function setFilter($value,$key)
    {
        list($type,$col) = convertStringToArray('__', $key);
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

    public function getFolders()
    {
        $basePath = base_path('../csvfiles');
        $directories = File::directories($basePath);
        $result = [];

        foreach ($directories as $dirPath) {
            $dirName = basename($dirPath); // Get folder name only
            $csvFiles = [];

            // Get all .csv files in this directory
            foreach (File::files($dirPath) as $file) {
                if ($file->getExtension() === 'csv') {
                    $csvFiles[] = $file->getFilename();
                }
            }

            if (!empty($csvFiles)) {
                $result[$dirName] = $csvFiles;
            }
        }
        return $result;
    }


    public function testRun(Request $request)
    {
        dispatch((new GetAccountsJob()))->delay(5);
        // dispatch((new GetFoldersJob()))->delay(5);
        // $this->setCvsFiles();

        return response()->json(['success' => true],200);
    }

}