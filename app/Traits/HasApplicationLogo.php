<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasApplicationLogo
{
      /**
     * Update the tenant company's logo.
     *
     * @param  \Illuminate\Http\UploadedFile  $logo
     * @return void
     */
    public function updateApplicationLogo(UploadedFile $logo)
    {
        tap($this->application_logo_path, function ($previous) use ($logo) {
            $this->forceFill([
                'application_logo_path' => $logo->storePublicly(
                    'aplication-logos', ['disk' => $this->applicationLogoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->applicationLogoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the tenant company's photo.
     *
     * @return void
     */
    public function deleteApplicationLogo()
    {
        Storage::disk($this->applicationLogoDisk())->delete($this->application_logo_path);

        $this->forceFill([
            'application_logo_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the tenant company's logo.
     *
     * @return string
     */
    public function getApplicationLogoUrlAttribute()
    {
        return $this->application_logo_path
            ? Storage::disk($this->applicationLogoDisk())->url($this->application_logo_path)
            : $this->defaultApplicationLogUrl();
    }

    /**
     * Get the default application logo URL if no application logo has been uploaded.
     *
     * @return string
     */
    protected function defaultApplicationLogUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that application logo should be stored on.
     *
     * @return string
     */
    protected function applicationLogoDisk()
    {
        return 'public';
    }    
}
