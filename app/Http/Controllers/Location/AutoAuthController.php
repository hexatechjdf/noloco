<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helper\CRM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoAuthController extends Controller
{

    protected const VIEW = 'autoauth';
    public function index()
    {
        return view('admin.setting.index');
    }
    public function authChecking(Request $req)
    {
        if ($req->ajax()) {
            if ($req->has('location') && $req->has('token')) {
                $location = $req->location;
                $user = User::with('crmauth')->where('location_id', $req->location)->first();
                if (!$user) {
                    $user = new User();
                    $user->name = 'Location User';
                    //$user->first_name = 'User';
                    //$user->last_name = 'User';
                    $user->email = $location . '@presave.net';
                    $user->password = bcrypt('presave_' . $location);
                    $user->location_id = $location;
                    $user->ghl_api_key = '-';
                    $user->save();
                }
                $user->ghl_api_key = $req->token;
                $user->save();
                request()->merge(['user_id' => $user->id]);
                session([
                    'location_id' => $user->location_id,
                    'uid' => $user->id,
                    'user_id' => $user->id,
                    'user_loc' => $user->location_id,
                ]);

                $res = new \stdClass;
                $res->user_id = $user->id;
                $res->location_id = $user->location_id ?? null;
                $res->is_crm = false;
                request()->user_id = $user->id;
                $res->token = $user->ghl_api_key;
                $token = $user->crmauth;
                $res->crm_connected = false;
                if ($token) {
                    // request()->code = $token;
                    list($tokenx, $token) = CRM::go_and_get_token($token->refresh_token, 'refresh', $user->id, $token);
                    $res->crm_connected = $tokenx && $token;
                }
                if (!$res->crm_connected) {
                    $res->crm_connected = CRM::ConnectOauth($req->location, $res->token, false, $user->id);
                }

                if ($res->crm_connected) {
                    if (Auth::check()) {
                        Auth::logout();
                        sleep(1);
                    }
                    Auth::login($user);
                }

                $res->is_crm = $res->crm_connected;
                $res->token_id = encrypt($res->user_id);


                $res->route = route('location.credit-app.setting');
                return response()->json($res);
            }
        }
        return response()->json(['status' => 'invalid request']);
    }

    public function connect(Request $request)
    {
        return view(self::VIEW . '.connect');
    }

    public function authError()
    {
        return view(self::VIEW . '.error');
    }
}
