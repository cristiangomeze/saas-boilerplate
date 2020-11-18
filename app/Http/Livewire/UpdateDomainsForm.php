<?php

namespace App\Http\Livewire;

use Livewire\Component;

class UpdateDomainsForm extends Component
{
    public function render()
    {
        return view('application.update-domains-form');
    }

    /**
     * Get the domains.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getdomainsProperty()
    {
        return collect(
            auth()->user()->tenant->domains
        )->map(fn ($domain) => (object) [
                'domain' => $domain->domain,
                'type' => true ? 'Subdomain' : 'Domain',
                'is_primary' => false,
                'created_at' => $domain->created_at->format('M d, Y a h:m'),
        ]);
    }
}
