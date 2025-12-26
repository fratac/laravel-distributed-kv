<?php

namespace Fratac\LaravelDistributedKv\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KeyUpdated
{
    use Dispatchable, SerializesModels;

    public string $key;
    public mixed $oldValue;
    public mixed $newValue;

    public function __construct(string $key, mixed $oldValue, mixed $newValue)
    {
        $this->key = $key;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }
}
