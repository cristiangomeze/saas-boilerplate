<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    public function togglePrimary()
    {
        $this->update([
            'is_primary' => ! $this->is_primary
        ]);
    }
}
