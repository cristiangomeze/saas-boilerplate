<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Stancl\Tenancy\Jobs\CreateDatabase as Base;

class CreateDatabase extends Base
{
    use Batchable;
}
