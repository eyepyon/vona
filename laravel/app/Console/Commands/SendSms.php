<?php
namespace app\Console\Commands;

use App\Console\Command;
use App\Console\Notification;
use App\Notifications\VonageNotification;

class SendSms extends Command
{
    protected $signature = 'send:sms';

    public function handle()
    {
        $message = $this->ask('What message do you want to send?');

        $to = '+818048563939';
        Notification::route('vonage', $to)->notify(new VonageNotification($message));
    }
}


