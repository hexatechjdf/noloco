<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GhlService;
use App\Services\Api\DealService;
use App\Helper\CRM;

class IndexController extends Controller
{
    protected $ghlService;
    protected $dealService;

    public function __construct(GhlService $ghlService, DealService $dealService)
    {
        $this->ghlService = $ghlService;
        $this->dealService = $dealService;
    }
    public function getOpportunities(Request $request)
    {
        $contactFields = null;
        $contactView = null;
        if($request->type =='both')
        {
          $contactFields = $this->dealService->getContact($request->locationId, $request->contactId);
          $contactView = view('locations.deals.components.leadContactFields',get_defined_vars())->render();
        }

        $data = $this->ghlService->oppertunityList($request->locationId, $request->contactId);
        $view = view('locations.deals.components.oppertunitiesList',get_defined_vars())->render();

        return response()->json(['view' => $view,'contactView' => $contactView]);
    }

    public function createOpportunities(Request $request)
    {
        $data = $this->ghlService->addTag($request->locationId, $request->contactId);
        if(!$data)
        {
            return response()->json(['error' => 'Something Wrong']);
        }
        return response()->json(['success' => 'Add Tag Successfully']);
    }

    public function manageContactFields(Request $request)
    {

        $data = true;
        if(!$request->contactId)
        {
            $req = setCotactFieldsPayload($request->formData);
            $cont_id =  $this->dealService->createContact($request->locationId,$req);
            $request->merge(['contactId' => $cont_id]);
            $data = $cont_id;
        }
        else{
            $data  = $this->ghlService->updateContact($request->locationId, $request->contactId,$request->formData);
        }
        if(!$data)
        {
            return response()->json(['error' => 'Something Wrong']);
        }
        if($request->is_tag)
        {
            $this->ghlService->addTag($request->locationId, $request->contactId);
        }
        return response()->json(['success' => 'Successfully Updated']);
    }
}
