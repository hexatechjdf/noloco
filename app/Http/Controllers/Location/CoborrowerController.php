<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use App\Models\User;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use Illuminate\Support\Str;
use App\Jobs\SetDealsOBjectJob;

class CoborrowerController extends Controller
{
    protected $inventoryService;
    protected $dealService;

    // Constructor to inject the services
    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }
    public function index(Request $request)
    {
        $deal_id = $request->dealId;
        $location_id = $request->locationId;

        return view('locations.coborrowers.index', get_defined_vars());
    }

    public function setDeal(Request $request)
    {
        $contact = $this->dealService->getContact($request->locationId,$request->contactId);
        $contact = (object)$contact;
        dispatch((new SetDealsOBjectJob($contact, $request->dealId,'coborrowerMapping')));

        return response()->json(['success' => 'Successfully Updated']);
    }


    public function contactsSearch(Request $request)
    {
        $user = User::where('id', 1)->first();
        $token = $user->crmauth ?? null;
        $locationId = $request->locationId;
        $status = false;
        $message = 'Connect to Agency First';
        $type = '';
        $detail = '';
        $load_more = false;
        $query = '';
        $limit = 100;
        if ($request->term) {
            $query = '&query=' . $request->term;
        }

        $query = 'contacts/?locationId=' . $locationId . $query . '&limit=' . $limit;
        $detail = CRM::crmV2Loc(1, $locationId, $query, 'get');

        // $detail = CRM::crmV2($user->id, $query, 'get', '', [], false, $token->location_id);
        $contacts = [];
        if ($detail && property_exists($detail, 'contacts')) {
            $detail = $detail->contacts;
            foreach ($detail as $det) {
                $nameParts = [$det->contactName];

if (!empty($det->email)) {
    $nameParts[] = $det->email;
}

if (!empty($det->phone)) {
    $nameParts[] = $det->phone;
}

$contacts[] = [
    'name' => implode(' / ', $nameParts),
    'id' => $det->id
];
            }
        }

        return response()->json($contacts);

        return response()->json(['status' => $status, 'message' => $message]);

    }


}
