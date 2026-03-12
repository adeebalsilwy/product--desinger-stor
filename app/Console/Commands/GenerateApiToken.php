<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-api-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = \App\Models\User::first();
        $token = $user->createToken('test-token')->plainTextToken;
        $this->info("Token generated for user {$user->email}:");
        $this->line($token);
    }
}
