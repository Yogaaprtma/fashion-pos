<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Models\ApiSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = ApiIntegration::withCount('logs')->latest()->get();
        $recentLogs   = ApiSyncLog::with('integration')->latest()->take(20)->get();

        return view('settings.integrations.index', compact('integrations', 'recentLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'channel_name'   => 'required|string|max:100',
            'sync_direction' => 'required|in:bidirectional,pos_to_online,online_to_pos',
        ]);

        ApiIntegration::create([
            'channel_name'      => $request->channel_name,
            'api_key'           => 'fpos_live_' . Str::random(32),
            'webhook_secret'    => 'whsec_' . Str::random(24),
            'sync_direction'    => $request->sync_direction,
            'auto_deduct_stock' => $request->boolean('auto_deduct_stock', true),
            'is_active'         => true,
        ]);

        return redirect()->route('settings.integrations.index')
            ->with('success', "Kanal integrasi {$request->channel_name} berhasil dibuat!");
    }

    public function toggle(ApiIntegration $integration)
    {
        $integration->update(['is_active' => !$integration->is_active]);
        return back()->with('success', 'Status integrasi diperbarui.');
    }

    public function destroy(ApiIntegration $integration)
    {
        $integration->delete();
        return back()->with('success', 'Kanal integrasi dihapus.');
    }
}
