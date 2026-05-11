<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSending;

class LogFailedEmail
{
    /**
     * Catch mail failures by hooking into MessageSending and wrapping it.
     * Laravel fires a separate MessageSent on success; failures surface as exceptions
     * in the controller/queue — we log them from there via this helper.
     */
    public static function record(string $to, string $subject, string $type = null, string $error = null): void
    {
        try {
            EmailLog::create([
                'to'      => $to,
                'subject' => $subject,
                'type'    => $type,
                'status'  => 'failed',
                'mailer'  => config('mail.default'),
                'error'   => $error,
            ]);
        } catch (\Throwable) {}
    }
}
