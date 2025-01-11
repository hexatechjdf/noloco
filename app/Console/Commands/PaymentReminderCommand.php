<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Jobs\PaymentReminderJob;

class PaymentReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payment-reminder-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dispatch(new PaymentReminderJob());
        return 0;
    }
}
