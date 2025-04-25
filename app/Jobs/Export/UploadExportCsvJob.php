<?php

namespace App\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadExportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ftp_account;
    public $localPath;
    public $remotePath;
    /**
     * Create a new job instance.
     */
    public function __construct($localPath,$remotePath,$ftp_account)
    {
        $this->ftp_account = $ftp_account;
        $this->localPath = $localPath;
        $this->remotePath = $remotePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->uploadFtpFile($this->localPath,$this->remotePath,$this->ftp_account);
    }

    public function uploadFtpFile($localPath,$remotePath,$ftp)
    {
        $ftpHost = $ftp->domain;
        $ftpUser = $ftp->username;
        $ftpPass = $ftp->password;
        $ftpPort = 21;

        $connId = ftp_connect($ftpHost, $ftpPort, 30);
        if (!$connId) {
            throw new \Exception("Could not connect to FTP server.");
        }
        $loginResult = ftp_login($connId, $ftpUser, $ftpPass);
        if (!$loginResult) {
            ftp_close($connId);
            throw new \Exception("FTP login failed.");
        }
        ftp_pasv($connId, true);
        $upload = ftp_put($connId, $remotePath, $localPath, FTP_BINARY);
        if (!$upload) {
            ftp_close($connId);
            throw new \Exception("FTP upload failed.");
        }
        ftp_close($connId);
    }
}
