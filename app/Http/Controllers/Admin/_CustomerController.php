<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MappingTable;
use App\Models\CoborrowMaping;
use Illuminate\Support\Str;
use App\Helper\CRM;
use Illuminate\Support\Facades\Cache;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;
use App\Services\Api\InventoryService;
use App\Services\Api\DealService;

class CustomerController extends Controller
{

    protected $inventoryService;
    protected $dealService;

    public function __construct(InventoryService $inventoryService, DealService $dealService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealService = $dealService;
    }





}
