<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Node',
                'slug' => 'starter-node',
                'description' => 'Perfect for independent developers and small shops.',
                'price' => 50000,
                'duration_days' => 30,
                'max_channels' => 1,
                'features' => ['1 Distribution Node', 'Real-time Relay', 'HMAC Security', 'Standard Support'],
            ],
            [
                'name' => 'Professional Matrix',
                'slug' => 'professional-matrix',
                'description' => 'Advanced routing for growing businesses.',
                'price' => 150000,
                'duration_days' => 30,
                'max_channels' => 5,
                'features' => ['5 Distribution Nodes', 'Priority Routing', 'Advanced Analytics', 'Business Support'],
            ],
            [
                'name' => 'Enterprise Infrastructure',
                'slug' => 'enterprise-infrastructure',
                'description' => 'Unlimited scaling for global operations.',
                'price' => 500000,
                'duration_days' => 30,
                'max_channels' => 20,
                'features' => ['20 Distribution Nodes', 'Custom Protocol', 'White-label Docs', '24/7 Dedicated Support'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
