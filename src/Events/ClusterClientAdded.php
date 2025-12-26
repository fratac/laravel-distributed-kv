<?php

namespace Fratac\LaravelDistributedKv\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClusterClientAdded
{
    use Dispatchable, SerializesModels;

    public string $name;
    public string $url;

    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }
}
