<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Jobs\Dealers\DispatchDealersJobs;

class DealersController extends Controller
{
    public $prefix_route = 'admin.dealers.';

    public function index()
    {
        $dealers = Dealer::paginate(10);

        return view($this->prefix_route . 'index', get_defined_vars());
    }

    public function updateStatus(Request $request)
    {
        $dealer = Dealer::where('id',$request->id)->firstOrFail();
        $dealer->status = $dealer->status == 1 ? 0 : 1;
        $dealer->save();

        return response()->json(['message' => 'Successfully Updated']);
    }

    public function fetchDealersRecord()
    {
        DispatchDealersJobs::dispatch();
    }
}
