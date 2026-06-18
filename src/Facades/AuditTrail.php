<?php

namespace NathanDeBarros\AuditTrail\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \NathanDeBarros\AuditTrail\Models\AuditLog|null log(string $action, ?\Illuminate\Database\Eloquent\Model $auditable = null, ?string $description = null, ?array $oldValues = null, ?array $newValues = null, ?array $metadata = null, ?string $module = null, ?string $event = 'manual', ?\Illuminate\Database\Eloquent\Model $user = null)
 */
class AuditTrail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'audit-trail';
    }
}
