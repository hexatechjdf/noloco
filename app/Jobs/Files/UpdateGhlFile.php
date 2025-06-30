<?php

namespace App\Jobs\Files;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper\CRM;
use Illuminate\Support\Str;
use App\Models\CustomFields;


class UpdateGhlFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $key;
    public $data;
    public $filee;
    /**
     * Create a new job instance.
     */
    public function __construct($filee,$key,$data)
    {
        $this->key = $key;
        $this->data = $data;
        $this->filee = $filee;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contactId  = $this->data['contactId'] ?? null;
        $locationId = $this->data['locationId'] ?? null;


        $c = CustomFields::where('key',$locationId)->first();
        try{
            if($c)
            {
                $js = json_decode($c->content?? '' , true) ?? [];
                $targetFieldKey = $this->key;
                $matchedKey = collect($js)->search(function ($item) use ($targetFieldKey) {
                    return $item['fieldKey'] === $targetFieldKey;
                });

                if($matchedKey)
                {
                    $token = CRM::getCrmToken(['location_id' => $locationId]);
                    if($token)
                    {
                        $payload[$matchedKey."_".uniqid(Str::random(5))] =  json_decode($this->filee,true);
                        $urll = 'https://services.leadconnectorhq.com/forms/upload-custom-files?contactId='. $contactId . '&locationId=' . $locationId;

                        $response = $this->sendRequest($token,$urll, $payload, "POST", 'multipart/form-data', false);
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
        dd($decodedResponse);

        if ($httpCode >= 400) {
            return $this->logApiCall($url, $payload, $headers, $method, [
                'error' => 'HTTP Error',
                'status' => $httpCode,
                'response' => $decodedResponse ?? $response
            ], false, $httpCode, $save_log);
        }

        return true;
    }
}
