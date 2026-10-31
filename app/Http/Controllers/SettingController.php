<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('group', ['smtp', 'preferences'])->pluck('value', 'key')->all();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',

            // SMTP Fields (nullable)
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',

            // Provider Fields (nullable)
            'postmark_token' => 'nullable|string',
            'mailgun_domain' => 'nullable|string',
            'mailgun_secret' => 'nullable|string',
            'ses_key' => 'nullable|string',
            'ses_secret' => 'nullable|string',
            'ses_region' => 'nullable|string',
        ]);

        // Connection Settings
        foreach ($validated as $key => $value) {
            // Do not override passwords/secrets if they are empty
            if (empty($value) && in_array($key, ['mail_password', 'postmark_token', 'mailgun_secret', 'ses_secret'])) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value, 'group' => 'smtp']
            );
        }

        // Toggles / Preferences
        $toggles = ['email_welcome_enabled', 'email_assignment_enabled', 'email_return_enabled'];
        foreach ($toggles as $toggle) {
            Setting::updateOrCreate(
                ['key' => $toggle],
                ['value' => $request->has($toggle) ? 'true' : 'false', 'group' => 'preferences']
            );
        }

        return back()->with('success', 'Email Connections updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            Mail::raw('This is a test email from your Material Allotment system. Your Universal Email Connection setup is working flawlessly!', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Connection - Material Allotment');
            });

            return back()->with('success', 'Test email dispatched successfully! Please check your inbox or logs.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to dispatch test email. Error: '.$e->getMessage());
        }
    }
}
