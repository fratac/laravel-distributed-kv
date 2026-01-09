<?php

namespace App\LaravelDistributedKv\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KeySynced
{
    use Dispatchable, SerializesModels;

    public string $key;
    public mixed $localValue;
    public mixed $remoteValue;
    public string $sourceClient;
    public string $direction; // 'pull' o 'push'

    public function __construct(string $key, mixed $localValue, mixed $remoteValue, string $sourceClient, string $direction)
    {
        $this->key = $key;
        $this->localValue = $localValue;
        $this->remoteValue = $remoteValue;
        $this->sourceClient = $sourceClient;
        $this->direction = $direction;
    }
}
