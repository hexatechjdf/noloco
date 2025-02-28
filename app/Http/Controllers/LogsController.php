<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ErrorLog;
use App\Jobs\UpdateDealJob;

class LogsController extends Controller
{
    public function history($type = 'deals')
    {
        $logs = ErrorLog::where('for',$type)->select('table_id','table','for','created_at')->get()->groupBy('table_id');

        return view('admin.logs.index',get_defined_vars());
    }

    public function historyForm(Request $request)
    {
        $id = $request->id;
        $logs = ErrorLog::where('table_id',$request->id)->get();
        $view =  view('admin.logs.form',get_defined_vars())->render();

        return response()->json(['view' => $view]);
    }

    public function historyManage(Request $request,$id)
    {
        $e  = ErrorLog::where('table_id',$id)->first();
        if($e)
        {
            $ar = [];
            $types = $request->type;
            foreach($request->data as $key => $d)
            {
                $t = $types[$key];

                $ar[$key] = ['column' => $d, 'type' => $t];
            }
            $result = setDataWithType($ar,[]);
            $result['id'] = "%s";
            $data = ['graphqlPayload' => [arrayToGraphQL1($result)]];
            if($e->for == 'csv')
            {
                dispatch((new UpdateMapInvJob($data,'updateInventory', $id,3, $id)));
            }else{
                dispatch((new UpdateDealJob([], $id,$data,3,$id)));
            }
            return response()->json(['success' => "Request sent successfully"]);
        }

    }
}
