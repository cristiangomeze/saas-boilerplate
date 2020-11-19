<?php

namespace App\Http\Livewire;

use Livewire\Component;

class UpdateDomainsForm extends Component
{
    /**
     * The component's listeners.
     *
     * @var array
     */
    protected $listeners = [
        'refresh-list-domains' => '$refresh',
    ];

    /**
     * Get the domains.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getdomainsProperty()
    {
        return collect(
            auth()->user()->tenant->domains
        )->map(fn ($domain, $index) => (object) [
            'name' => $domain->domain,
            'type' => 0 === $index ? 'Subdomain' : 'Domain',
            'is_primary' => $domain->is_primary,
            'created_at' => $domain->created_at->format('M d, Y h:m a'),
        ]);
    }

    public function render()
    {
        return view('application.update-domains-form');
    }
}
