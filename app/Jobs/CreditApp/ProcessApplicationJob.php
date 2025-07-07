<?php

namespace App\Jobs\CreditApp;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\CreditApp\SearchContactJob;

class ProcessApplicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $type;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $type='dealscustomerMapping')
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $is_tag = $this->type == 'creditAppscustomerMapping' ? 'true' : 'false';
        $phone = $this->type == 'creditAppscustomerMapping' ? @$this->data['phoneNumber'] : @$this->data['coBorrowerPhone'];
        $phone = formatPhoneNumberWithCountryCode($phone);
        $email = $this->type=='creditAppscustomerMapping' ? @$this->data['emailAddress'] : @$this->data['coBorrowerEmail'];
        $locationId = 'geAOl3NEW1iIKIWheJcj' ?? $this->data['dealerGhlId'];
        $payload = [
            "locationId" => $locationId,
            "page" => 1,
            "pageLimit" => 1,
            "filters" => [
                [
                    "group" => "OR",
                    "filters" => [
                        [
                            "field" => "phone",
                            "operator" => "eq",
                            "value" => $phone,
                        ],
                        [
                            "field" => "email",
                            "operator" => "eq",
                            "value" => $email,
                        ],
                    ],
                ],
            ],
        ];

        SearchContactJob::dispatch($this->data,$locationId,$payload,$this->type,$is_tag);

    }
}
