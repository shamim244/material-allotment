<?php

namespace App\Providers;

use App\Listeners\LogSentEmail;
use App\Models\Setting;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Loads SMTP / API provider settings from the database and injects them
     * into Laravel's live config so ALL mail paths (HTTP requests, queued jobs,
     * password resets, welcome emails, etc.) use the admin-configured connection.
     */
    public function boot(): void
    {
        // Register email activity logger for all outbound mail
        Event::listen(MessageSent::class, LogSentEmail::class);

        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $settings = Setting::where('group', 'smtp')->pluck('value', 'key')->all();

            if (empty($settings)) {
                return;
            }

            // Map the stored mailer name to a valid Laravel driver
            $driver = $settings['mail_mailer'] ?? config('mail.default');

            // SSL → smtps scheme mapping required by Symfony Mailer
            $encryption = $settings['mail_encryption'] ?? null;
            $scheme = ($encryption === 'ssl') ? 'smtps' : (($encryption === 'tls') ? 'smtp' : null);

            config([
                // Active driver
                'mail.default' => $driver,

                // SMTP connection overrides
                'mail.mailers.smtp.host' => $settings['mail_host'] ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => $settings['mail_port'] ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.encryption' => $encryption,
                'mail.mailers.smtp.scheme' => $scheme,
                'mail.mailers.smtp.username' => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $settings['mail_password'] ?? config('mail.mailers.smtp.password'),

                // Third-party API overrides
                'services.mailgun.domain' => $settings['mailgun_domain'] ?? config('services.mailgun.domain'),
                'services.mailgun.secret' => $settings['mailgun_secret'] ?? config('services.mailgun.secret'),
                'services.postmark.token' => $settings['postmark_token'] ?? config('services.postmark.token'),
                'services.ses.key' => $settings['ses_key'] ?? config('services.ses.key'),
                'services.ses.secret' => $settings['ses_secret'] ?? config('services.ses.secret'),
                'services.ses.region' => $settings['ses_region'] ?? config('services.ses.region'),

                // Global sender identity
                'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
            ]);

            // Purge ALL cached mailer instances so the next Mail::send() / notification
            // re-creates a fresh transport using the config we just injected above.
            // This is the critical step that fixes Password Reset & Welcome emails.
            if ($this->app->resolved('mail.manager')) {
                $manager = $this->app->make('mail.manager');
                // Purge each known mailer name to clear internal caches
                foreach (['smtp', 'smtps', 'mailgun', 'postmark', 'ses', 'log', $driver] as $mailerName) {
                    try {
                        $manager->purge($mailerName);
                    } catch (\Throwable) {
                    }
                }
            }

        } catch (\Throwable $e) {
            // Fail silently — DB may not exist yet (first deploy / running migrations)
        }
    }
}
