<?php

namespace App\Traits;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

trait ProcessesBatch
{
    public $processBatchId = null;

    public $batchProgress = 0;

    public function startBatch()
    {
        $batch = Bus::batch($this->processesPipes())
            ->name(get_class($this))
            ->allowFailures()
            ->dispatch();

        $this->processBatchId = $batch->id;
    }

    public function getProcessBatchProperty(): ?Batch
    {
        if (! $this->processBatchId) return null;

        return Bus::findBatch($this->processBatchId);
    }

    public function updateBatchProgress()
    {
        $this->batchProgress = $this->processBatch->progress();
    }
}