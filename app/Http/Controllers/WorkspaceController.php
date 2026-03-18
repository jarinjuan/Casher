<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    /**
     * Show workspace settings
     */
    public function settings()
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team) {
            return redirect('/dashboard')->with('error', 'No workspace selected');
        }

        return view('workspace.settings', [
            'team' => $team,
            'members' => $team->users()->get(),
            'owner' => $team->owner,
        ]);
    }

    /**
     * Generate new invite code
     */
    public function generateInviteCode()
    {
        $team = auth()->user()->currentTeam;

        if ($team->user_id !== auth()->id()) {
            return back()->with('error', 'Only workspace owner can generate invite codes');
        }

        $team->update([
            'invite_code' => Str::random(12),
        ]);

        return back()->with('success', 'Invite code generated: ' . $team->invite_code);
    }

    /**
     * Show join workspace form
     */
    public function joinForm()
    {
        return view('workspace.join');
    }

    /**
     * Join workspace with invite code
     */
    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|max:50|exists:teams,invite_code',
        ]);

        $team = Team::where('invite_code', $request->invite_code)->firstOrFail();

        // Check if user already member
        if ($team->users()->where('user_id', auth()->id())->exists()) {
            return back()->with('info', 'You are already member of this workspace');
        }

        // Add user to team
        $team->users()->attach(auth()->id(), ['role' => 'member']);

        // Set as current team and refresh auth
        auth()->user()->update(['current_team_id' => $team->id]);
        auth()->setUser(auth()->user()->fresh());

        return redirect('/dashboard')->with('success', 'Successfully joined workspace: ' . $team->name);
    }

    /**
     * Switch current workspace
     */
    public function switchWorkspace($teamId)
    {
        $team = Team::findOrFail($teamId);

        // Check if user is member of this team
        if (!$team->users()->where('user_id', auth()->id())->exists() && $team->user_id !== auth()->id()) {
            return back()->with('error', 'You do not have access to this workspace');
        }

        // Update current team
        auth()->user()->update(['current_team_id' => $team->id]);
        auth()->setUser(auth()->user()->fresh());

        return back()->with('success', 'Switched to workspace: ' . $team->name);
    }

    /**
     * Update workspace currency
     */
    public function updateCurrency(Request $request)
    {
        $team = auth()->user()->currentTeam;

        if ($team->user_id !== auth()->id()) {
            return back()->with('error', 'Only workspace owner can change currency settings');
        }

        $request->validate([
            'default_currency' => 'required|string|size:3|in:CZK,EUR,USD,GBP,JPY,CHF,PLN,SEK,NOK,DKK',
        ]);

        $team->update([
            'default_currency' => strtoupper($request->default_currency),
        ]);

        return back()->with('success', 'Default currency updated to ' . $request->default_currency);
    }
}
