<?php

namespace App\LaravelDistributedKv\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KeyDeleted
{
    use Dispatchable, SerializesModels;

    public string $key;
    public mixed $oldValue;

    public function __construct(string $key, mixed $oldValue)
    {
        $this->key = $key;
        $this->oldValue = $oldValue;
    }
}
