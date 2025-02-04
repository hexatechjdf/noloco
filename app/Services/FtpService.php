<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\FtpAccount;
use Illuminate\Support\Facades\Http;

class FtpService
{

     public function createAccount($request)
     {
        try{
            $errors = null;
            $user = supersetting('ftp_username');
            $apikey = supersetting('ftp_api');
            $domain = supersetting('ftp_domain');
            // $apikey = '2uHeuZQ3gadi2wrYvNkrOJC2OD6VXf37';

            $url = 'https://162.213.249.87:2003/index.php?api=json&act=ftp_account&apiuser='.$user.'&apikey='.$apikey;
            // $url = 'https://'.rawurlencode($user).':'.rawurlencode($pass).'@'.$ip.':2003/index.php?api=json&act=ftp_account';

            $post = array('create_acc' => '1',
                        'login' => $request->username,
                        'newpass' => $request->password,
                        'conf' => $request->password,
                        'ftpdomain' => $domain,
                        'dir' => 'csvfiles/'.$request->username,
                        'quota' => 'limited',
                        'quota_limit' => '200');


    // Set the curl parameters
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if(!empty($post)){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }

    // Get response from the server.
    $resp = curl_exec($ch);

    // The response will hold a string as per the API response method. In this case its PHP JSON
    $res = json_decode($resp, true);

    // Done ?
    if(!empty($res['done'])){
        $res = $res['done'];

        FtpAccount::create([
            'mapping_id' => $request->csv_id,
            'username' => $request->username,
            'password' => $request->password,
            'domain' => $domain,
            'directory' => $request->username,
        ]);
    // Error
    }else{
        $errors = $res['error'];
        $res = null;
    }

        }catch(\Excaption $e){
            $errors = ['There is some issues'];
            $res = null;
        }
        return [$res,$errors];

     }

}
