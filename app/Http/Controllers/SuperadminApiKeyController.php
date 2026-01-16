<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\ApiKeyLog;
use App\Services\ApiAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuperadminApiKeyController extends Controller
{
    protected $analyticsService;

    public function __construct(ApiAnalyticsService $analyticsService)
    {
        $this->middleware('auth:superadmin');
        $this->analyticsService = $analyticsService;
    }

    /**
     * Check if 2FA is verified for superadmin
     */
    protected function check2FA(): bool
    {
        // For now, use session-based 2FA similar to HOD
        // Can be enhanced with proper TOTP later
        return session('superadmin_2fa_verified', false);
    }

    /**
     * Require 2FA for sensitive actions
     */
    protected function require2FA()
    {
        if (!$this->check2FA()) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Two-factor authentication required',
                    'requires_2fa' => true,
                ], 403);
            }
            return redirect()->route('superadmin.api-keys.index')
                ->with('error', 'Two-factor authentication required for this action.');
        }
        return null;
    }

    /**
     * Display a listing of API keys
     */
    public function index(Request $request)
    {
        $query = ApiKey::with('creator');

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('client_contact', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->get('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->get('status') === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }

        $apiKeys = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('superadmin.api-keys.index', compact('apiKeys'));
    }

    /**
     * Show the form for creating a new API key
     */
    public function create()
    {
        return view('superadmin.api-keys.create');
    }

    /**
     * Store a newly created API key
     */
    public function store(Request $request)
    {
        $check2FA = $this->require2FA();
        if ($check2FA) {
            return $check2FA;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'rate_limit_per_minute' => 'nullable|integer|min:1|max:1000',
            'rate_limit_per_hour' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $keyPair = ApiKey::generateKeyPair();

        $apiKey = ApiKey::create([
            'name' => $request->name,
            'key' => $keyPair['key_hash'],
            'secret_hash' => $keyPair['secret_hash'],
            'client_name' => $request->client_name,
            'client_contact' => $request->client_contact,
            'is_active' => true,
            'rate_limit_per_minute' => $request->rate_limit_per_minute ?? config('api.default_rate_limit.per_minute', 60),
            'rate_limit_per_hour' => $request->rate_limit_per_hour ?? config('api.default_rate_limit.per_hour', 1000),
            'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : null,
            'created_by' => Auth::guard('superadmin')->id(),
        ]);

        // Show the key and secret to the user (only shown once)
        return redirect()->route('superadmin.api-keys.show', $apiKey->id)
            ->with('success', 'API key created successfully.')
            ->with('api_key_plain', $keyPair['key'])
            ->with('api_secret_plain', $keyPair['secret']);
    }

    /**
     * Display the specified API key
     */
    public function show($id)
    {
        $apiKey = ApiKey::with(['creator', 'logs' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(100);
        }])->findOrFail($id);

        // Get usage stats
        $usageStats = $this->analyticsService->getUsageStats($apiKey);
        $recentLogs = $apiKey->logs()->orderBy('created_at', 'desc')->limit(50)->get();

        return view('superadmin.api-keys.show', compact('apiKey', 'usageStats', 'recentLogs'));
    }

    /**
     * Show the form for editing the specified API key
     */
    public function edit($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        return view('superadmin.api-keys.edit', compact('apiKey'));
    }

    /**
     * Update the specified API key
     */
    public function update(Request $request, $id)
    {
        $check2FA = $this->require2FA();
        if ($check2FA) {
            return $check2FA;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'rate_limit_per_minute' => 'nullable|integer|min:1|max:1000',
            'rate_limit_per_hour' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $apiKey = ApiKey::findOrFail($id);

        $apiKey->update([
            'name' => $request->name,
            'client_name' => $request->client_name,
            'client_contact' => $request->client_contact,
            'rate_limit_per_minute' => $request->rate_limit_per_minute ?? $apiKey->rate_limit_per_minute,
            'rate_limit_per_hour' => $request->rate_limit_per_hour ?? $apiKey->rate_limit_per_hour,
            'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : $apiKey->expires_at,
        ]);

        return redirect()->route('superadmin.api-keys.show', $apiKey->id)
            ->with('success', 'API key updated successfully.');
    }

    /**
     * Remove the specified API key
     */
    public function destroy($id)
    {
        $check2FA = $this->require2FA();
        if ($check2FA) {
            return $check2FA;
        }

        $apiKey = ApiKey::findOrFail($id);
        $apiKey->delete();

        return redirect()->route('superadmin.api-keys.index')
            ->with('success', 'API key deleted successfully.');
    }

    /**
     * Regenerate API key secret
     */
    public function regenerate($id)
    {
        $check2FA = $this->require2FA();
        if ($check2FA) {
            return $check2FA;
        }

        $apiKey = ApiKey::findOrFail($id);
        $keyPair = ApiKey::generateKeyPair();

        $apiKey->update([
            'key' => $keyPair['key_hash'],
            'secret_hash' => $keyPair['secret_hash'],
        ]);

        return redirect()->route('superadmin.api-keys.show', $apiKey->id)
            ->with('success', 'API key regenerated successfully.')
            ->with('api_key_plain', $keyPair['key'])
            ->with('api_secret_plain', $keyPair['secret']);
    }

    /**
     * Toggle API key active status
     */
    public function toggleStatus($id)
    {
        $check2FA = $this->require2FA();
        if ($check2FA) {
            return $check2FA;
        }

        $apiKey = ApiKey::findOrFail($id);
        $apiKey->update([
            'is_active' => !$apiKey->is_active,
        ]);

        $status = $apiKey->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "API key {$status} successfully.");
    }

    /**
     * Get usage statistics for an API key
     */
    public function usageStats($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $stats = $this->analyticsService->getUsageStats($apiKey);

        return response()->json($stats);
    }

    /**
     * View logs for an API key
     */
    public function logs($id, Request $request)
    {
        $apiKey = ApiKey::findOrFail($id);

        $query = $apiKey->logs()->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            if ($request->get('status') === 'success') {
                $query->successful();
            } elseif ($request->get('status') === 'error') {
                $query->failed();
            }
        }

        // Filter by endpoint
        if ($request->has('endpoint')) {
            $query->where('endpoint', 'like', "%{$request->get('endpoint')}%");
        }

        $logs = $query->paginate(50);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($logs);
        }

        return view('superadmin.api-keys.logs', compact('apiKey', 'logs'));
    }

    /**
     * Show API documentation
     */
    public function documentation()
    {
        return view('superadmin.api-keys.documentation');
    }
}
