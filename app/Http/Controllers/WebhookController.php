<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helper\CRM;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Jobs\GetDealsJob;

class WebhookController extends Controller
{

    protected $inventoryService;
    protected $dealService;

    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }

    public function ghlContactToNoloco(Request $request)
    {
        $contact = $request->all();
        $contact =  (object)$contact;
        $locationId = @$contact->location['id'] ?? null;
        $contactId = @$contact->contact_id ?? null;

        if($locationId && $contactId)
        {
            dispatch((new GetDealsJob($contact,$contactId,$locationId,'customerMapping')))->delay(5);
            dispatch((new GetDealsJob($contact,$contactId,$locationId,'coborrowerMapping')))->delay(5);
        }

        return response()->json(['success',true],200);
    }
}
