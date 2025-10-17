<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\LocationSharingService;
use App\Models\User;
use App\Models\LocationShare;
use Illuminate\Http\Request;

class LocationSharingController extends Controller
{
    protected $sharingService;

    public function __construct(LocationSharingService $sharingService)
    {
        $this->sharingService = $sharingService;
    }

    /**
     * Show location sharing dashboard
     */
    public function index()
    {
        $activeShares = $this->sharingService->getActiveShares(auth()->user());
        $sharedLocations = $this->sharingService->getSharedLocations(auth()->user());
        $stats = $this->sharingService->getSharingStats(auth()->user());

        return view('location-sharing.index', compact('activeShares', 'sharedLocations', 'stats'));
    }

    /**
     * Show create share form
     */
    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('location-sharing.create', compact('users'));
    }

    /**
     * Store new location share
     */
    public function store(Request $request)
    {
        $request->validate([
            'shared_with_user_id' => 'required|exists:users,id',
            'share_type' => 'required|in:realtime,trip,location',
            'tracking_session_id' => 'nullable|exists:tracking_sessions,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'expires_at' => 'nullable|date|after:now',
            'permissions' => 'nullable|array',
        ]);

        $sharedWithUser = User::findOrFail($request->shared_with_user_id);
        
        $share = $this->sharingService->shareLocation(
            auth()->user(),
            $sharedWithUser,
            $request->all()
        );

        return redirect()->route('location-sharing.index')
            ->with('success', 'Location shared successfully!');
    }

    /**
     * Show edit share form
     */
    public function edit(LocationShare $share)
    {
        $this->authorize('update', $share);
        
        return view('location-sharing.edit', compact('share'));
    }

    /**
     * Update location share
     */
    public function update(Request $request, LocationShare $share)
    {
        $this->authorize('update', $share);

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $updated = $this->sharingService->updateSharedLocation($share, $request->all());

        return redirect()->route('location-sharing.index')
            ->with('success', $updated ? 'Location updated successfully!' : 'Failed to update location');
    }

    /**
     * Revoke location share
     */
    public function revoke(LocationShare $share)
    {
        $this->authorize('update', $share);

        $revoked = $this->sharingService->revokeShare($share);

        return redirect()->route('location-sharing.index')
            ->with('success', $revoked ? 'Location share revoked!' : 'Failed to revoke share');
    }

    /**
     * Show real-time updates
     */
    public function realtime()
    {
        $updates = $this->sharingService->getRealtimeUpdates(auth()->user());

        return view('location-sharing.realtime', compact('updates'));
    }
}