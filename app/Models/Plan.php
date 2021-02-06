<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    const MONTLY = 1;
    const YEARLY = 12;

    protected $guarded = [];

    public function scopeMontly($query)
    {
        $query->whereBillingFrequency(static::MONTLY);
    }

    public function scopeYearly($query)
    {
        $query->whereBillingFrequency(static::YEARLY);
    }

    public function montly()
    {
        return $this->Billing_frequency == static::MONTLY;
    }

    public function yearly()
    {
        return $this->Billing_frequency == static::YEARLY;
    }
}
