<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetMasterTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:set-master {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a specific tenant as the Master Billing Tenant for Dogfooding subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $tenant = \App\Models\Tenant::where('email', $email)->first();
        
        if (!$tenant) {
            $this->error("Tenant with email {$email} not found!");
            return self::FAILURE;
        }

        // Reset all others
        \App\Models\Tenant::where('is_master', true)->update(['is_master' => false]);

        $tenant->update(['is_master' => true, 'is_active' => true]);

        $this->info("Tenant '{$tenant->name}' has been set as the Master Billing Tenant!");
        
        return self::SUCCESS;
    }
}
