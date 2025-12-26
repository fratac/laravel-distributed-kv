<?php

namespace Fratac\LaravelDistributedKv\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KeyCreated
{
    use Dispatchable, SerializesModels;

    public string $key;
    public mixed $value;

    public function __construct(string $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}
