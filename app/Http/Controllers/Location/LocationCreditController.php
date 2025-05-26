<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditSetting;

class LocationCreditController extends Controller
{
    public function setting()
    {
        $locationId = auth()->user()->location_id;
        $set = CreditSetting::where('location_id',$locationId)->first();

        return view('locations.credit.setting',get_defined_vars());
    }

    public function settingStore(Request $request)
    {
        $locationId = auth()->user()->location_id;

        CreditSetting::updateOrCreate([
            'location_id' => $locationId,
        ],$request->all());

        return response()->json(['success' => 'success']);
    }
}
