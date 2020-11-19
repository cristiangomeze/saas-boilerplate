<?php

namespace App\Http\Livewire;

use App\Rules\Domain\Subdomain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CreateApplicationForm extends Component
{
    /**
     * Indicates if create application is being confirmed.
     *
     * @var bool
     */
    public $confirmingApplicationCreate = false;

    /**
     * The application's name.
     *
     * @var string
     */
    public $name = '';


    /**
     * The application's domain.
     *
     * @var string
     */
    public $domain = '';

    /**
     * Confirm that the user enters a valid name and domain
     *
     * @return void
     */
    public function confirmApplicationCreation()
    {
        $this->name = '';

        $this->domain = '';

        $this->dispatchBrowserEvent('confirming-create-application');

        $this->confirmingApplicationCreate = true;
    }

    /**
     * Create Application.
     *
     * @return void
     */
    public function createApplication()
    {
        if (auth()->user()->tenant) {
            return;
        }

        $this->resetErrorBag();

        Validator::make([
            'name' => $this->name,
            'domain' => $this->domain
        ], [
            'name' => ['required', 'string', 'min:5', 'max:60'],
            'domain' => ['required', 'string', 'max:255', 'unique:domains', new Subdomain],
        ])->validate();

        DB::transaction(function () {
            $tenant = auth()->user()->tenant()->create(['name' => $this->name]);
            $tenant->domains()->create([
                'domain' => $this->domain,
                'is_primary' => true,
            ]);
        });

        $this->confirmingApplicationCreate = false;

        $this->emit('Added');

        $this->dispatchBrowserEvent('confirming-redirect');
    }

    public function render()
    {
        return view('application.create-application-form');
    }
}
