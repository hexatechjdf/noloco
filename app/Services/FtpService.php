<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\FtpAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FtpService
{

     public function createAccount($request, $typee= 'inbond')
     {
        try{
            $errors = null;
            $acc= null;

            if($typee= 'outbond')
            {
                $acc = $this->setupAccount($request,$request->host);
                return $acc;
            }else{
                $domain = supersetting('ftp_domain');
                // $url = 'https://'.rawurlencode($user).':'.rawurlencode($pass).'@'.$ip.':2003/index.php?api=json&act=ftp_account';
                if($request->id)
                {
                    $acc = FtpAccount::where('id',$request->id)->first();
                    if(!$acc)
                    {
                        return [null,'Account doest not exist'];
                    }
                    if($acc->username == $request->username)
                    {
                        $acc->location_id = $request->location_id;
                        $acc->save();
                        return ['successfully updated',null];
                    }
                }

                $post = $this->getFtpArray($request,$domain,$acc);
                $res = $this->sendRequest($post,$acc ? 'ftp' : 'ftp_account');
                // dd($res);
                if(!empty($res['done'])){
                    $res = $res['done'];
                    $acc = $this->setupAccount($request,$domain);
                }else{
                    $errors = $res['error'];
                    $res = null;
                }
            }
        }catch(\Excaption $e){
            $errors = ['There is some issues'];
            $res = null;
        }
        return [$res,$errors];

     }

    public function setupAccount($request,$domain)
     {
        $acc = FtpAccount::updateOrCreate(['mapping_id' => $request->csv_id],[
            'username' => $request->username,
            'domain' => $domain,
            'directory' => $request->username,
            'location_id' => $request->location_id ?? $request->location_ids,
        ]);
        if(!$request->id)
        {
            $acc->password = $request->password;
            $acc->save();
        }

        return $acc;
     }

     public function getFtpArray($request,$domain,$acc=null)
     {
        if($acc)
        {
            $name = $acc->username.'_'.$acc->domain;
            return [
                'edit_record' => '1',
                'edit_ftp_user' => $name,
                'quota' => 'limited',
                'quota_limit' => '200'];
        }


        return  [
        'create_acc' => '1',
        'login' => $request->username,
        'newpass' => $request->password,
        'conf' => $request->password,
        'ftpdomain' => $domain,
        'dir' => 'csvfiles/'.$request->username,
        'quota' => 'limited',
        'quota_limit' => '200'];

     }

     public function deleteAccount($request)
     {
        try{
            $errors = null;
            $domain = supersetting('ftp_domain');
            $acc = FtpAccount::where('id',$request->id)->first();
            if(!$acc)
            {
                return [null,'Account doest not exist'];
            }
            $name = $acc->username.'_'.$acc->domain;
            $post = array('delete_fuser_id' => '1', 'delete_ftp_user' =>$name , 'delete_home_dir' => '1');

            $res = $this->sendRequest($post,'ftp');
            if(!empty($res['done'])){
                $res = $res['done'];
                $acc->delete();
            }else{
                $errors = @$res['error'] ?? 'There is some issues';
                $res = null;
            }

         }catch(\Excaption $e){
            $errors = ['There is some issues'];
            $res = null;
        }
        return [$res,$errors];
     }

     public function sendRequest($post = null,$act = 'ftp_account')
     {
        $user = supersetting('ftp_username');
        $apikey = supersetting('ftp_api');
        $ip = supersetting('ftp_ip');
        // $apikey = '2uHeuZQ3gadi2wrYvNkrOJC2OD6VXf37';

        $url = 'https://'.$ip.':2003/index.php?api=json&act='.$act.'&apiuser='.$user.'&apikey='.$apikey;

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

        return json_decode($resp, true);
     }

    public function uploadToDynamicFtp($ftpDetails, $localFilePath, $remoteFilePath)
    {
        // Build dynamic FTP disk
        $ftpDisk = Storage::build([
            'driver'   => 'ftp',
            'host'     => $ftpDetails['host'],
            'username' => $ftpDetails['username'],
            'password' => $ftpDetails['password'],
            'port'     => $ftpDetails['port'] ?? 21,
            'root'     => $ftpDetails['root'] ?? '',
            'passive'  => true,
            'ssl'      => false,
            'timeout'  => 30,
        ]);

        // Upload CSV
        $success = $ftpDisk->put($remoteFilePath, fopen($localFilePath, 'r+'));

        if ($success) {
            echo "✅ File uploaded to FTP: {$ftpDetails['host']}";
        } else {
            echo "❌ Failed to upload file to FTP: {$ftpDetails['host']}";
        }
    }
}
