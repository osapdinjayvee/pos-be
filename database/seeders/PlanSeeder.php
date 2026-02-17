<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Features
        $features = collect([
            ['name' => 'Basic POS', 'key' => 'basic_pos', 'description' => 'Core point-of-sale functionality'],
            ['name' => 'Inventory Management', 'key' => 'inventory', 'description' => 'Track stock levels and manage products'],
            ['name' => 'Customer Management', 'key' => 'customers', 'description' => 'Store and manage customer records'],
            ['name' => 'Sales Reports', 'key' => 'reports_basic', 'description' => 'Basic sales and transaction reports'],
            ['name' => 'Advanced Analytics', 'key' => 'analytics', 'description' => 'Detailed analytics, trends, and forecasting'],
            ['name' => 'Multi-Terminal', 'key' => 'multi_terminal', 'description' => 'Support for multiple POS terminals'],
            ['name' => 'Employee Management', 'key' => 'employees', 'description' => 'Staff accounts, roles, and shift tracking'],
            ['name' => 'Loyalty Program', 'key' => 'loyalty', 'description' => 'Customer loyalty points and rewards'],
            ['name' => 'API Access', 'key' => 'api_access', 'description' => 'Third-party integrations via API'],
            ['name' => 'Priority Support', 'key' => 'priority_support', 'description' => 'Dedicated priority customer support'],
            ['name' => 'White Labeling', 'key' => 'white_label', 'description' => 'Custom branding and white-label options'],
            ['name' => 'Multi-Branch', 'key' => 'multi_branch', 'description' => 'Manage multiple store branches from one account'],
        ])->mapWithKeys(function ($data) {
            $feature = Feature::updateOrCreate(['key' => $data['key']], $data);

            return [$data['key'] => $feature];
        });

        // Free tier
        $free = Plan::updateOrCreate(['name' => 'Free'], [
            'description' => 'Get started with essential POS features at no cost.',
            'price' => 0,
            'tier_level' => 'standard',
            'max_terminals' => 1,
            'max_users' => 1,
            'is_active' => true,
        ]);
        $free->features()->sync($features->only([
            'basic_pos',
            'reports_basic',
        ])->pluck('id'));

        // One Time (single purchase, no recurring)
        $oneTime = Plan::updateOrCreate(['name' => 'One Time'], [
            'description' => 'Single one-time purchase with essential POS and inventory features. No recurring fees.',
            'price' => 4999.00,
            'tier_level' => 'standard',
            'max_terminals' => 1,
            'max_users' => 2,
            'is_active' => true,
            'metadata' => ['billing' => 'one_time'],
        ]);
        $oneTime->features()->sync($features->only([
            'basic_pos',
            'inventory',
            'customers',
            'reports_basic',
        ])->pluck('id'));

        // Standard (one-time payment)
        $standard = Plan::updateOrCreate(['name' => 'Standard'], [
            'description' => 'One-time purchase for small businesses needing core POS and inventory tools.',
            'price' => 7999.00,
            'tier_level' => 'standard',
            'max_terminals' => 2,
            'max_users' => 3,
            'is_active' => true,
            'metadata' => ['billing' => 'one_time'],
        ]);
        $standard->features()->sync($features->only([
            'basic_pos',
            'inventory',
            'customers',
            'reports_basic',
            'employees',
        ])->pluck('id'));

        // Premium (one-time payment)
        $premium = Plan::updateOrCreate(['name' => 'Premium'], [
            'description' => 'One-time purchase for growing businesses with advanced analytics and loyalty.',
            'price' => 19999.00,
            'tier_level' => 'premium',
            'max_terminals' => 5,
            'max_users' => 10,
            'is_active' => true,
            'metadata' => ['billing' => 'one_time'],
        ]);
        $premium->features()->sync($features->only([
            'basic_pos',
            'inventory',
            'customers',
            'reports_basic',
            'analytics',
            'multi_terminal',
            'employees',
            'loyalty',
            'api_access',
        ])->pluck('id'));

        // Enterprise (one-time payment)
        $enterprise = Plan::updateOrCreate(['name' => 'Enterprise'], [
            'description' => 'Full-featured solution for multi-branch operations with white labeling and priority support.',
            'price' => 49999.00,
            'tier_level' => 'enterprise',
            'max_terminals' => 50,
            'max_users' => 100,
            'is_active' => true,
            'metadata' => ['billing' => 'one_time'],
        ]);
        $enterprise->features()->sync($features->pluck('id'));
    }
}
