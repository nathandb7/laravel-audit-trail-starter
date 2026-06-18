<?php

namespace NathanDeBarros\AuditTrail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RuntimeException;

class AuditLog extends Model
{
    public $timestamps = true;

    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('audit-trail.table_name', 'audit_logs');
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new RuntimeException('Audit logs are immutable and cannot be updated.');
        });

        static::deleting(function (): never {
            throw new RuntimeException('Audit logs are immutable and cannot be deleted.');
        });
    }
}
