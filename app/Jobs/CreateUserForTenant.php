<?php

namespace App\Jobs;

use App\Mail\TenantUserCreated;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class CreateUserForTenant implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase|Model */
    protected $tenant;

    /** @var String */
    protected $password;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;

        $this->password = Str::random(10);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        tenancy()->initialize($this->tenant);
        
        // Make this users as admin
        tap(User::create([
            'name' => $this->tenant->user->name,
            'email' => $this->tenant->user->email,
            'password' => Hash::make($this->password),
        ]), function ($user) {
            Mail::to($user)->queue(new TenantUserCreated($this->tenant, $this->password));
        });
        
        tenancy()->end();
    }
}
