<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSentEmail
{
    /**
     * Handle the MessageSent event fired by Laravel after every successful mail dispatch.
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;

            // Collect all recipient addresses
            $to = collect($message->getTo())->keys()->implode(', ');
            $from = collect($message->getFrom())->keys()->first();

            // Try to derive the mailable class name from the mailer data
            $type = null;
            if (isset($event->data['__laravel_mailable'])) {
                $type = class_basename($event->data['__laravel_mailable']);
            } elseif (isset($event->data['mailable'])) {
                $type = class_basename($event->data['mailable']);
            }

            EmailLog::create([
                'to'      => $to ?: '—',
                'subject' => $message->getSubject(),
                'from'    => $from,
                'mailer'  => config('mail.default'),
                'type'    => $type,
                'status'  => 'sent',
            ]);
        } catch (\Throwable $e) {
            // Never let logging crash the application
        }
    }
}
