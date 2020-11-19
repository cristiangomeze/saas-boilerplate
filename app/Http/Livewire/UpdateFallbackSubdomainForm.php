<?php

namespace App\Http\Livewire;

use App\Rules\Domain\Subdomain;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateFallbackSubdomainForm extends Component
{
    public $subdomain;

    public function mount()
    {
        $this->subdomain = $this->fallbackSubdomain->domain;
    }

    public function updateFallbackSubdomain()
    {
        $this->resetErrorBag();

        Validator::make([
            'domain' => $this->subdomain
        ], [
            'domain' => ['required', 'string', 'max:255', new Subdomain(), Rule::unique('domains')->ignore($this->fallbackSubdomain->id)],
        ])->validate();

        tap($this->fallbackSubdomain, function ($model) {
            $model->forceFill([
                'domain' => $this->subdomain,
            ])->save();
        });

        $this->emit('saved');

        $this->emit('refresh-list-domains');
    }

    /**
     * Get the fallback subdomain.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFallbackSubdomainProperty()
    {
        return auth()->user()->tenant->domains()->first();
    }

    public function render()
    {
        return view('application.update-fallback-subdomain-form');
    }
}
