<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GhlService;
use App\Services\Api\DealService;
use App\Helper\CRM;
use Illuminate\Support\Str;
use App\Models\CustomFields;
use App\Jobs\Files\UpdateGhlFile;

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
            $req = setCotactFieldsPayload($request);
            $cont_id =  $this->dealService->createContact($request->locationId,$req);
            $request->merge(['contactId' => $cont_id]);
            $data = $cont_id;
        }
        else{
            $inp = $request->except(['drivers_licence','insurance_card']);
            if($request->file('drivers_licence'))
            {
                $this->uploadFile($request,'drivers_licence');
            }
            if($request->file('insurance_card'))
            {
                $this->uploadFile($request,'insurance_card');
            }
            $data  = $this->ghlService->updateContact($request->locationId, $request->contactId,$inp);
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

    public function uploadFile($request,$targetFieldKey)
    {
        try{
            $data = $request;
            $contactId  = $data['contactId'] ?? null;
            $locationId = $data['locationId'] ?? null;
            $c = CustomFields::where('key',$locationId)->first();

            if($c)
            {
                $js = json_decode($c->content?? '' , true) ?? [];
                $matchedKey = collect($js)->search(function ($item) use ($targetFieldKey) {
                    return $item['fieldKey'] === $targetFieldKey;
                });

                if($matchedKey)
                {
                    $file = $request->file($targetFieldKey);
                    $token = CRM::getCrmToken(['location_id' => $locationId]);
                    if ($file && $file->isValid()) {
                        if($token)
                        {
                            $payload[$matchedKey."_".uniqid(Str::random(5))] =  new \CURLFile($file->getRealPath(), $file->getMimeType(), $file->getClientOriginalName());
                            $urll = 'https://services.leadconnectorhq.com/forms/upload-custom-files?contactId='. $contactId . '&locationId=' . $locationId;
                            $response = $this->sendRequest($token,$urll, $payload, "POST", 'multipart/form-data', false);
                        }
                    }
                }
            }
        }catch(\Exception $e){
            \Log::error($e);
        }
    }


    private function sendRequest($token,$url, $data, $method, $content_type = null, $save_log = false)
    {
        $curl = curl_init();
        $content_type = $content_type ? $content_type : 'application/json';
        // logger(['content_type'=>$content_type]);
        $headers = [
            "Accept: application/json",
            "Authorization: Bearer " . $token->access_token,
            "Content-Type: " . $content_type,
            "Version: 2021-07-28"
        ];

        $payload = $content_type ? $data : (is_array($data) ? json_encode($data) : $data);


        if (empty($token)) {
            return false;
        }

        if ($payload === false) {
            return false;
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
        ]);


        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
             return false;
        }

        $decodedResponse = json_decode($response, true);

        return true;
    }
}
