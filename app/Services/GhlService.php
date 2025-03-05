<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Helper\CRM;

class GhlService
{
    public function searchField($key,$search_keys,$replace_key,$locationId)
    {
        $idd = null;
        try{
            $query = 'locations/'. $locationId . '/customFields/search?query='.$key;
            $detail = $this->hitFieldSearchRequest($locationId,$query);

            if ($detail && property_exists($detail, 'customFields')) {
                foreach ($detail->customFields as $det) {
                    $daya[] = $det->fieldKey;
                    $fk = str_replace($replace_key, '', $det->fieldKey);
                    if(in_array($fk,$search_keys))
                    {
                        $idd = $det->id;
                        break;
                    }

                }
            }
        }catch(\Exception $e){
        }
        return $idd;
    }

    public function hitFieldSearchRequest($locationId,$query,$retry = 1)
    {
        $detail = null;

        try{
            $detail = CRM::crmV2Loc(1, $locationId, $query, 'get');
            if($detail->error && $detail->error == 'CustomField with id search1 not found' && $retry <=1)
            {
                $retry++;
                $q = 'locations/'. $locationId . '/customFields';
                return $this->hitFieldSearchRequest($locationId,$q,$retry);
            }
        }catch(\Exception $e){
        }

        return $detail;
    }

    public function contactSearchByField($locationId,$id,$value)
    {
        $contacts = [];
        try{
            $payload = [
                "locationId" => $locationId,
                "page" => 1,
                "pageLimit" => 100,
                "filters" => [
                    [
                        "field" => "customFields.".$id,
                        "operator" => "eq",
                        "value" => $value
                    ]
                ]
           ];
           $url = 'contacts/search';
           $detail = CRM::crmV2Loc(1, $locationId, $url, 'post',$payload);
           if ($detail && property_exists($detail, 'contacts')) {
                foreach ($detail->contacts as $con) {
                    $contacts[] = ['id' => $con->id, 'firstName' => $con->firstNameLowerCase,'lastName' => $con->lastNameLowerCase,'email' => $con->email,'phone' => $con->phone,'locationId' => $con->locationId ];
                }
           }
        }catch(\Exception $e){

        }
        return $contacts;
    }
}
