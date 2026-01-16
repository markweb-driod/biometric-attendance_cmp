<?php

namespace App\Services;

use App\Models\UserSession;
use App\Models\Superadmin;
use App\Mail\LoginNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class SessionNotificationService
{
    /**
     * Send email notification to all superadmins about a new login
     */
    public function notifySuperadmins(UserSession $session): void
    {
        try {
            // Get all active superadmins
            $superadmins = Superadmin::where('is_active', true)->get();

            if ($superadmins->isEmpty()) {
                Log::warning('No active superadmins found to send login notification');
                return;
            }

            // Queue email to each superadmin
            foreach ($superadmins as $superadmin) {
                try {
                    Mail::to($superadmin->email)
                        ->queue(new LoginNotificationMail($session));

                    Log::info('Login notification queued', [
                        'recipient' => $superadmin->email,
                        'session_id' => $session->session_id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to queue login notification', [
                        'recipient' => $superadmin->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify superadmins about login', [
                'error' => $e->getMessage(),
                'session_id' => $session->session_id ?? null,
            ]);
        }
    }

    /**
     * Send immediate email notification (non-queued) - use with caution
     */
    public function notifySuperadminsImmediate(UserSession $session): void
    {
        try {
            $superadmins = Superadmin::where('is_active', true)->get();

            if ($superadmins->isEmpty()) {
                return;
            }

            foreach ($superadmins as $superadmin) {
                try {
                    Mail::to($superadmin->email)
                        ->send(new LoginNotificationMail($session));
                } catch (\Exception $e) {
                    Log::error('Failed to send immediate login notification', [
                        'recipient' => $superadmin->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send immediate notifications', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}

