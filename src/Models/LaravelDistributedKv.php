<?php

namespace App\LaravelDistributedKv\Models;

use Illuminate\Database\Eloquent\Model;

class DistributedKvEntry extends Model
{
    protected $table = 'distributed_kv_entries';
    protected $primaryKey = 'key';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'version',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function isDeleted(): bool
    {
        return ! is_null($this->deleted_at);
    }
}

