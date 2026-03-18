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

        // Check if user is banned from using this invite code
        if (\App\Models\TeamInvitationBan::where('team_id', $team->id)
            ->where('user_id', auth()->id())
            ->where('invite_code', $request->invite_code)
            ->exists()) {
            return back()->with('error', 'You cannot join this workspace using this invite code.');
        }

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

    /**
     * Remove member from workspace
     */
    public function removeMember($teamId, $userId)
    {
        $team = Team::findOrFail($teamId);

        // Only owner can remove
        if ($team->user_id !== auth()->id()) {
            return back()->with('error', 'Only workspace owner can remove members');
        }

        // Owner cannot remove themselves
        if ($userId == auth()->id()) {
            return back()->with('error', 'You cannot remove yourself from the workspace');
        }

        // Check if user is a member
        if (!$team->users()->where('user_id', $userId)->exists()) {
            return back()->with('error', 'User is not a member of this workspace');
        }

        // Add to ban list to prevent re-joining with the same code
        if ($team->invite_code) {
            \App\Models\TeamInvitationBan::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => $userId,
                'invite_code' => $team->invite_code,
            ]);
        }

        // Detach user
        $team->users()->detach($userId);

        // Update removed user's current team if they were actively using this one
        $removedUser = \App\Models\User::find($userId);
        if ($removedUser && $removedUser->current_team_id == $team->id) {
            $fallbackTeam = $removedUser->ownedTeams()->first();
            $removedUser->update([
                'current_team_id' => $fallbackTeam ? $fallbackTeam->id : null,
            ]);
        }

        return back()->with('success', 'Member successfully removed.');
    }

    /**
     * Leave a workspace
     */
    public function leaveWorkspace($teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        // Owner cannot leave their own workspace
        if ($team->user_id === $user->id) {
            return back()->with('error', 'You cannot leave a workspace that you own.');
        }

        // Check if user is a member
        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'You are not a member of this workspace.');
        }

        // Detach current user
        $team->users()->detach($user->id);

        // Fallback to own team if this was their active team
        if ($user->current_team_id == $team->id) {
            $fallbackTeam = $user->ownedTeams()->first();
            $user->update([
                'current_team_id' => $fallbackTeam ? $fallbackTeam->id : null,
            ]);
            
            return redirect()->route('dashboard')->with('success', 'You have successfully left the workspace: ' . $team->name);
        }

        return redirect()->route('dashboard')->with('success', 'You have successfully left the workspace.');
    }
}
