<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateApplicationInformationForm extends Component
{
    use WithFileUploads;
    
    public $tenant;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * The new logo for the application.
     *
     * @var mixed
     */
    public $logo;

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->tenant = Auth::user()->tenant;

        $this->state = $this->tenant->only('name');
    }

    /**
     * Update the application company's information.
     *
     * @return void
     */
    public function updateApplicationInformation()
    {
        $this->resetErrorBag();

        Validator::make($this->logo
        ? array_merge($this->state, ['logo' => $this->logo])
        : $this->state, [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:1024'],
        ])->validate();

        if (isset($this->logo)) {
            $this->tenant->updateApplicationLogo($this->logo);
        }

        $this->tenant->forceFill([
            'name' => $this->state['name'],
        ])->save();


        if (isset($this->logo)) {
            return redirect()->route('application.show');
        }

        $this->emit('saved');
    }

    /**
     * Delete application company's logo.
     *
     * @return void
     */
    public function deleteApplicationLogo()
    {
        $this->tenant->deleteApplicationLogo();
    }

    public function render()
    {
        return view('application.update-application-information-form');
    }
}
