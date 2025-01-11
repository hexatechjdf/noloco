<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Contact;
use App\Helper\CRM;
use Illuminate\Http\Request;

class CRMController extends Controller
{

    public function crmCallback(Request $request)
    {
        $code = $request->code ?? null;

        if ($code) {
            $user_id = auth()->user()->id;
            $code = CRM::crm_token($code, '');
            $code = json_decode($code);
            $user_type = $code->userType ?? null;
            $main = route('admin.setting.index');
            if ($user_type) {
                $token = $user->crmauth ?? null;
                list($connected, $con) = CRM::go_and_get_token($code, '', $user_id, $token);

                
                if ($connected) {
                    return redirect($main)->with('message', 'Connected Successfully');
                }
                return redirect($main)->with('error', json_encode($code));

            }
            return redirect($main)->with('error', 'Not allowed to connect');
        }
    }
}
