<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;
use App\Models\CreditSetting;
use App\Models\Scripting;

class CreditController extends Controller
{

    protected $inventoryService;
    protected $dealService;

    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }

    public function list(Request $request)
    {
        $contact= [];
        $data= [];
        try{
            $query = $this->inventoryService->getCreditList($request->locationId, $request->contactId);
            $data = $this->inventoryService->submitRequest($query);
            $contact = $this->dealService->getContact($request->locationId, $request->contactId);

        }catch(\Excption $e){

        }
        return response()->json(['credit' => $data, 'contact' => $contact]);
    }

    public function checkValidLocation(Request $request)
    {
        $remainingDigits = substr($request->uid ?? 00, 1);
        $scripting = Scripting::where('uuid',$remainingDigits)->first();
        // dd($scripting, $remainingDigits, $request->all());
        if($scripting)
        {
            $locations = json_decode($scripting->locations ?? "" , true) ?? [];
            if(in_array($request->locationId, $locations))
            {
                return response()->json(['status' => true]);
            }
        }
        return response()->json(['status' => false]);
    }




    public function extractIframeSrcOrErrorMessage($xmlData)
    {
        // Load the XML string
        $xml = simplexml_load_string($xmlData);

        // Check if the XML was loaded successfully
        if (!$xml) {
            return [false,'Invalid XML'];
        }

        // Check if Creditsystem_Error exists
        if (isset($xml->Creditsystem_Error)) {
            // Retrieve the message attribute from Creditsystem_Error if it exists
            $errorMessage = (string)$xml->Creditsystem_Error['message'];

            if ($errorMessage) {
                // Return the error message if it exists
                return [false,$errorMessage];
            }
        }

        // If no Creditsystem_Error or no message, proceed to custom_report
        if (isset($xml->custom_report)) {
            // Retrieve the HTML inside custom_report (contains the iframe)
            $customReportHtml = (string)$xml->custom_report;

            // Parse the HTML to extract the iframe src
            $dom = new \DOMDocument();
            @$dom->loadHTML($customReportHtml);  // Suppress warnings from malformed HTML

            // Find the iframe tag and get its src attribute
            $iframe = $dom->getElementsByTagName('iframe')->item(0);

            // Check if iframe exists and has the 'src' attribute
            if ($iframe && $iframe->hasAttribute('src')) {
                $iframeSrc = $iframe->getAttribute('src');
                return [true,$iframeSrc];
            }
        }

        // If no Creditsystem_Error and no iframe src, return null
        return [false,'Invalid XML'];
    }

    public function makeAPICall($path,$data,$headers=[])
    {
        $url = "https://gateway.700creditsolution.com/";
        $liveURL = "https://gateway.700dealer.com/";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url.$path);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);


        $resp = curl_exec($curl);
        curl_close($curl);

        return $resp;
    }
    public function setReport(Request $request)
    {
      
        $dats = [];
        $creditBureau = [];
        foreach(getBureau() as $key => $b)
        {
            if(isset($request[$b]))
            {
                $dats[] = $key;
                $creditBureau[] = $b == 'transunion' ? 'TRANS_UNION' : strtoupper($b);
            }
        }

        $locationId =  $request->location_id;
        // $request->merge(['contact_id' => $request->contact_id,'location_id' => $locationId]);
        $set = CreditSetting::where('location_id',$locationId)->first();

        $dataArray = [
            "ACCOUNT" => $set->account, //each location has their own account, password, clientId, clientSecret
            "PASSWD" => $set->password,
            "Product" => "CREDIT",
            "PASS" => "2",
            "PROCESS" => "PCCREDIT", //static almost still here
            "NAME" => $request->firstName.' ' . $request->middle_name.' '. $request->lastName, //from $request till last at the moment
            "SSN" => $request->social_security_number,
            "ADDRESS" => $request->address1,
            "CITY" => $request->city,
            "STATE" => $request->state,
            "ZIP" => $request->postalCode
        ];
        if(count($dats) > 0)
        {
            $dataArray['MULTIBUR'] =  implode(':', $dats);
        }else{
            $dataArray['BUREAU'] =  $dats[0];
        }


        //incase more bureu BUREAU will change with MULTIBUR=TU:EFX:XPN , after PROCESS rest can be set by $request check

        $serializedData = http_build_query($dataArray);
        $resp = $this->makeAPICall('Request',$serializedData,[ "Content-Type: text/plain"]);
        list($status,$iframeSrc) = $this->extractIframeSrcOrErrorMessage($resp);
        if($status){
            //save all above information to noloco Credit application table with iframe src as well
            $this->submitDataToNoloco($locationId, $request,$iframeSrc,$creditBureau);

            // make api call to get auth token for future data iframe request any time
            $response=$this->getIframeFinalSrc($iframeSrc,$set); // this function can be easily called for already saved iframe src
            return response()->json(['res' => $response, 'status' => true,'src' => $iframeSrc]);

        }else{
            \Log::info($resp);
          return response()->json(["status"=>false,"message"=>$iframeSrc]);
        }

    }

    public function submitDataToNoloco($locationId, $request,$iframeSrc,$creditBureau = [])
    {
        $creditBureauFormatted = '[' . implode(', ', $creditBureau) . ']';
        try{
            list($dealer_id,$dealership) =  $this->dealService->getDealership(request(),$locationId);
            $query = $this->inventoryService->createCreditList($request, $iframeSrc,$creditBureauFormatted,$dealer_id);
            $data = $this->inventoryService->submitRequest($query);
        }catch(\Excption $e){
        }
    }

function getIframeFinalSrc($src,$set)
{
$accessToken = $this->getAuthToken($set);

if($accessToken){
$url = $this->getSignedIframe($accessToken,$src);
if(!$url){
    return ["status"=>false,"message"=>'unable to generate report'];
}
return ["status"=>true,"url"=>$url];
}else{
return ["status"=>false,"message"=>'Unable to auth with platform'];
}

}

public function getAuthHeaders($token=null){
$headers= [
   "Content-Type: application/json"];
   if($token){
   $headers[]="Authorization: Bearer ".$token;
   }
   return $headers;
}

public function getSignedIframe($token,$src,$user='IFRAMEP'){ // $user will be each location own name

$data =json_encode(["url"=>$src,
"duration"=> "30",
"signedBy"=> $user]);

$resp = $this->makeAPICall('.auth/sign',$data,$this->getAuthHeaders($token));
$token = json_decode($resp);

$url= @$token->url ?? null;
return $url;
}

public function getAuthToken($set){
//get location id as param to get clientid and secret
$data = json_encode(["ClientId"=> $set->client_id,
"ClientSecret"=> $set->client_secret]);
$resp = $this->makeAPICall('.auth/token',$data,$this->getAuthHeaders());
curl_close($curl);
$token = json_decode($resp);

$accessToken= @$token->access_token ?? null;

/*
success response

{
"access_token":" eyJhbGciOiJSUzI1NiIsImtpZCI6ljIlg1ZVhrNHh5b2pORnVtMWtsMll0d...",
"token_type":"Bearer",
"not_before":1701353103,
"expires_in":3600,
"expires_on":1701356703
,"resource":"53d3b0ee-3fa4-49e6-b235-79d8d6254eec"
}

incaseError

{
    "error": "invalid_grant",
    "error_description": "AADB2C90085: The service has encountered an internal error. Please reauthenticate and try again.\r\nCorrelation ID: 3868cb7d-19e7-4e8e-82df-e4cf05e7468e\r\nTimestamp: 2025-03-20 20:09:18Z\r\n"
}
*/

return $accessToken;
}
}