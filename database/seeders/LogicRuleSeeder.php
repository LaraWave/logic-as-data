<?php

namespace LaraWave\LogicAsData\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use LaraWave\LogicAsData\Models\LogicRule;
use LaraWave\LogicAsData\Enums\RuleStatus;
use Illuminate\Database\Seeder;

class LogicRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hooks = [
            'auth.login',
            'auth.logout',
            'cart.checkout',
            'order.created',
            'payment.failed'
        ];

        foreach ($hooks as $hook) {
            foreach (RuleStatus::cases() as $status) {
                LogicRule::factory()
                    ->count(rand(2, 5))
                    ->create([
                        'hook' => $hook,
                        'status' => $status->value,
                    ]);

                LogicRule::factory()
                    ->count(rand(2, 5))
                    ->simpleDefinition()
                    ->create([
                        'hook' => $hook,
                        'status' => $status->value,
                    ]);
            }
        }
    }
}
