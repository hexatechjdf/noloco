<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class UpdateRefreshToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $rf = $this->user;
            if ($rf) {
                $status = $rf->urefresh();
                if ($status == 500) {
                    dispatch((new UpdateRefreshToken($this->user))->delay(Carbon::now()->addMinutes(5)));
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
