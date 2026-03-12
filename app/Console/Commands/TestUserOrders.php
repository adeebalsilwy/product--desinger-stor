<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestUserOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-user-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user orders relationship';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing user orders relationship...');
        
        $user = User::first();
        
        if (!$user) {
            $this->error('No users found');
            return;
        }
        
        $this->info('User: ' . $user->name . ' (ID: ' . $user->id . ')');
        
        try {
            $orders = $user->orders;
            $this->info('Orders count: ' . $orders->count());
            
            $designs = $user->savedDesigns;
            $this->info('Designs count: ' . $designs->count());
            
            $this->info('User data loaded successfully!');
        } catch (\Exception $e) {
            $this->error('Error loading user data: ' . $e->getMessage());
        }
    }
}