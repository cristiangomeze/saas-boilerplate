<?php

namespace App\Http\Livewire;

use App\Models\Domain;
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

    public function makePrimary(Domain $domain)
    {
        transform($this->domains->filter->is_primary->first(), 
            fn($item) => Domain::find($item->id)->togglePrimary() 
        );

        $domain->togglePrimary();

        $this->emit('refresh-list-domains');
    }

    public function deleteCustomDomain(Domain $domain)
    {
        if ($this->domains->filter->is_primary->first()->id === $domain->id) {
            return;
        }

        $domain->delete();

        $this->emit('refresh-list-domains');
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
        )->map(fn ($domain, $index) => (object) [
            'id' => $domain->id,
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
