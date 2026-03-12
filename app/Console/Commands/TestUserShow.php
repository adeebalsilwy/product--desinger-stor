<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestUserShow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-user-show {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user show page data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $this->info("Testing user show page for user ID: {$userId}");
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User ID {$userId} not found");
            return;
        }
        
        $this->info('User: ' . $user->name . ' (' . $user->email . ')');
        
        try {
            // Test all the relationships that the show page needs
            $this->info('Loading user relationships...');
            
            $user->loadMissing(['orders', 'savedDesigns', 'roles', 'permissions']);
            
            $this->info('Orders: ' . $user->orders->count());
            $this->info('Designs: ' . $user->savedDesigns->count());
            $this->info('Roles: ' . $user->roles->count());
            $this->info('Permissions: ' . ($user->permissions ? $user->permissions->count() : '0'));
            
            // Test pagination
            $orders = $user->orders()->with('items')->latest()->paginate(10);
            $designs = $user->savedDesigns()->latest()->paginate(10);
            
            $this->info('Paginated orders: ' . $orders->count());
            $this->info('Paginated designs: ' . $designs->count());
            
            $this->info('All data loaded successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error loading user data: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}