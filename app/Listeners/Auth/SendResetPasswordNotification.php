<?php

namespace App\Listeners\Auth;

use App\Events\Auth\SendResetPassword;
use App\Mail\Auth\ResetPasswordInstruction;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     *
     * @param \App\Events\SendResetPassword $event
     * @return void
     */
    public function handle(SendResetPassword $event)
    {
        try {
            Mail::to($event->data['email'])->send(new ResetPasswordInstruction([
                'code'      => $event->data['code'],
                'email'     => $event->data['email'],
            ]));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
