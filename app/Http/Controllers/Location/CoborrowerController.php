<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use App\Models\User;

class CoborrowerController extends Controller
{
    public function index(Request $request)
    {
        $deal_id = $request->dealId;
        $location_id = $request->locationId;

        return view('locations.coborrowers.index', get_defined_vars());
    }

    public function contactsSearch(Request $request)
    {
        //this code is only useable if need to store locations in database or connect with already saved locations in database using agency token
        $user = User::where('id', 3)->first();
        $token = $user->crmauth ?? null;
        $status = false;
        $message = 'Connect to Agency First';
        $type = '';
        $detail = '';
        $load_more = false;
        if ($token) {
            $type = $token->user_type;
            $query = '';
            $limit = 100;
            if ($request->term) {
                $query = '&query=' . $request->term;
            }

            $query = 'contacts/?locationId=' . $token->location_id . $query . '&limit=' . $limit;
            $detail = CRM::crmV2($user->id, $query, 'get', '', [], false, $token->location_id);
            $contacts = [];
            if ($detail && property_exists($detail, 'contacts')) {
                $detail = $detail->contacts;
                foreach ($detail as $det) {
                    $contacts[] = ['name' => $det->contactName, 'id' => json_encode(['id' => $det->id, 'name' => $det->contactName, 'email' => $det->email, 'location_id' => $det->locationId])];
                }
            }

            return response()->json($contacts);
        }

        return response()->json(['status' => $status, 'message' => $message]);

    }
}
