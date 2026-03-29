<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    /**
     * Zobrazí nastavení pracovního prostoru
     */
    public function settings()
    {
        $team = auth()->user()->currentTeam;
        
        if (!$team) {
            return redirect('/dashboard')->with('error', __('No workspace selected'));
        }

        return view('workspace.settings', [
            'team' => $team,
            'members' => $team->users()->get(),
            'owner' => $team->owner,
        ]);
    }

    /**
     * Vygeneruje nový zvací kód
     */
    public function generateInviteCode()
    {
        $team = auth()->user()->currentTeam;

        if ($team->user_id !== auth()->id()) {
            return back()->with('error', __('Only workspace owner can generate invite codes'));
        }

        $team->update([
            'invite_code' => Str::random(5),
        ]);

        return back()->with('success', __('Invite code generated: :code', ['code' => $team->invite_code]));
    }

    /**
     * Zobrazí formulář pro připojení do prostoru
     */
    public function joinForm()
    {
        return view('workspace.join');
    }

    /**
     * Připojí uživatele do prostoru pomocí zvacího kódu
     */
    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|max:50|exists:teams,invite_code',
        ]);

        $team = Team::where('invite_code', $request->invite_code)->firstOrFail();

        if (\App\Models\TeamInvitationBan::where('team_id', $team->id)
            ->where('user_id', auth()->id())
            ->where('invite_code', $request->invite_code)
            ->exists()) {
            return back()->with('error', __('You cannot join this workspace using this invite code.'));
        }

        if ($team->users()->where('user_id', auth()->id())->exists()) {
            return back()->with('info', __('You are already member of this workspace'));
        }

        $team->users()->attach(auth()->id(), ['role' => 'editor']);

        auth()->user()->update(['current_team_id' => $team->id]);
        auth()->setUser(auth()->user()->fresh());

        return redirect('/dashboard')->with('success', __('Successfully joined workspace: :name', ['name' => $team->name]));
    }

    /**
     * Přepne aktuální pracovní prostor
     */
    public function switchWorkspace($teamId)
    {
        $team = Team::findOrFail($teamId);

        if (!$team->users()->where('user_id', auth()->id())->exists() && $team->user_id !== auth()->id()) {
            return back()->with('error', __('You do not have access to this workspace'));
        }

        auth()->user()->update(['current_team_id' => $team->id]);
        auth()->setUser(auth()->user()->fresh());

        return back()->with('success', __('Switched to workspace: :name', ['name' => $team->name]));
    }

    /**
     * Změní výchozí měnu prostoru
     */
    public function updateCurrency(Request $request)
    {
        $team = auth()->user()->currentTeam;

        if ($team->user_id !== auth()->id()) {
            return back()->with('error', __('Only workspace owner can change currency settings'));
        }

        $request->validate([
            'default_currency' => 'required|string|size:3|in:CZK,EUR,USD,GBP,JPY,CHF,PLN,SEK,NOK,DKK',
        ]);

        $team->update([
            'default_currency' => strtoupper($request->default_currency),
        ]);

        return back()->with('success', __('Default currency updated to :currency', ['currency' => $request->default_currency]));
    }

    /**
     * Změní název prostoru
     */
    public function updateName(Request $request, $teamId)
    {
        $team = Team::findOrFail($teamId);

        if ($team->user_id !== auth()->id()) {
            return back()->with('error', __('Only workspace owner can change workspace name'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $team->update([
            'name' => $request->name,
        ]);

        return back()->with('success', __('Workspace name updated successfully.'));
    }

    /**
     * Změní roli člena v prostoru
     */
    public function updateRole(Request $request, $teamId, $userId): RedirectResponse
    {
        $team = \App\Models\Team::findOrFail($teamId);
        if ((int) auth()->id() !== (int) $team->user_id) abort(403);

        $data = $request->validate([
            'role' => 'required|in:editor,reader'
        ]);

        if (!$team->users()->where('user_id', (int) $userId)->exists()) {
            return back()->with('error', __('User is not a member of this workspace'));
        }

        $team->users()->updateExistingPivot((int) $userId, ['role' => $data['role']]);

        return back()->with('success', __('Member role updated successfully.'));
    }

    public function removeMember($teamId, $userId)
    {
        $team = Team::findOrFail($teamId);

        if ($team->user_id !== auth()->id()) {
            return back()->with('error', __('Only workspace owner can remove members'));
        }

        if ((int) $userId === (int) auth()->id()) {
            return back()->with('error', __('You cannot remove yourself from the workspace'));
        }

        if (!$team->users()->where('user_id', $userId)->exists()) {
            return back()->with('error', __('User is not a member of this workspace'));
        }

        if ($team->invite_code) {
            \App\Models\TeamInvitationBan::firstOrCreate([
                'team_id' => $team->id,
                'user_id' => $userId,
                'invite_code' => $team->invite_code,
            ]);
        }

        $team->users()->detach($userId);

        $removedUser = \App\Models\User::find($userId);
        if ($removedUser && (int) $removedUser->current_team_id === (int) $team->id) {
            $fallbackTeam = $removedUser->ownedTeams()->first();
            $removedUser->update([
                'current_team_id' => $fallbackTeam ? $fallbackTeam->id : null,
            ]);
        }

        return back()->with('success', __('Member successfully removed.'));
    }

    /**
     * Opustí pracovní prostor
     */
    public function leaveWorkspace($teamId)
    {
        $team = Team::findOrFail($teamId);
        $user = auth()->user();

        if ($team->user_id === $user->id) {
            return back()->with('error', __('You cannot leave a workspace that you own.'));
        }

        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', __('You are not a member of this workspace.'));
        }

        $team->users()->detach($user->id);

        if ((int) $user->current_team_id === (int) $team->id) {
            $fallbackTeam = $user->ownedTeams()->first();
            $user->update([
                'current_team_id' => $fallbackTeam ? $fallbackTeam->id : null,
            ]);
            
            return redirect()->route('dashboard')->with('success', __('You have successfully left the workspace: :name', ['name' => $team->name]));
        }

        return redirect()->route('dashboard')->with('success', __('You have successfully left the workspace.'));
    }
}
