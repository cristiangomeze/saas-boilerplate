<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Stancl\Tenancy\Jobs\MigrateDatabase as Base;

class MigrateDatabase extends Base
{
    use Batchable;
}
