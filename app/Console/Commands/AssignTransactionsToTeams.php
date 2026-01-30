<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use DB;

class AssignTransactionsToTeams extends Command
{
    protected $signature = 'transactions:assign-team';
    protected $description = 'Assign NULL team_id transactions to the user\'s current team (or first team)';

    public function handle()
    {
        $this->info('Scanning transactions with NULL team_id...');

        $count = Transaction::whereNull('team_id')->count();
        if ($count === 0) {
            $this->info('No transactions require assignment.');
            return 0;
        }

        $this->info("Found {$count} transactions. Processing...");

        Transaction::whereNull('team_id')->with('user.teams')->chunkById(200, function($transactions) {
            foreach ($transactions as $tx) {
                $user = $tx->user;
                if (! $user) {
                    $this->warn("Transaction {$tx->id} has no user, skipping.");
                    continue;
                }

                $teamId = $user->current_team_id ?? null;

                if (! $teamId) {
                    $firstTeam = $user->teams->first();
                    $teamId = $firstTeam->id ?? null;
                }

                if (! $teamId) {
                    $this->warn("User {$user->id} has no team, skipping transaction {$tx->id}.");
                    continue;
                }

                $tx->team_id = $teamId;
                $tx->save();
                $this->line("Assigned transaction {$tx->id} -> team {$teamId}");
            }
        });

        $this->info('Done.');
        return 0;
    }
}
