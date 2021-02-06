<?php

namespace App\Console\Commands;

use Braintree\Plan as BraintreePlan;
use App\Models\Plan;
use Illuminate\Console\Command;

class SyncPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'braintree:sync-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronización con planes en línea en Braintree';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Plan::truncate();
        
        collect(BraintreePlan::all())->each(fn ($plan) => Plan::create([
            'name' => $plan->name,
            'braintree_plan' => $plan->id,
            'price' => $plan->price,
            'trial_duration' => $plan->trialDuration,
            'description' => $plan->description,
            'billing_frequency' => $plan->billingFrequency,
        ]));
    }
}
