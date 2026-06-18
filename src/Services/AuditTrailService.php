<?php

namespace NathanDeBarros\AuditTrail\Services;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use NathanDeBarros\AuditTrail\Models\AuditLog;

class AuditTrailService
{
    public function __construct(
        protected ?Request $request = null,
        protected ?AuthFactory $authFactory = null,
    ) {
    }

    public function log(
        string $action,
        ?Model $auditable = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?string $module = null,
        ?string $event = 'manual',
        ?Model $user = null,
    ): ?AuditLog {
        if (! config('audit-trail.enabled', true)) {
            return null;
        }

        $user ??= $this->resolveUser();

        return AuditLog::query()->create([
            'user_id' => $user?->getKey(),
            'user_type' => $user ? $user::class : null,
            'event' => $event,
            'action' => $action,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->getKey(),
            'module' => $module,
            'description' => $description,
            'old_values' => $this->sanitizeValues($oldValues),
            'new_values' => $this->sanitizeValues($newValues),
            'metadata' => $metadata,
            'ip_address' => $this->shouldTrack('track_ip') ? $this->request?->ip() : null,
            'user_agent' => $this->shouldTrack('track_user_agent') ? $this->request?->userAgent() : null,
            'url' => $this->shouldTrack('track_url') ? $this->request?->fullUrl() : null,
            'method' => $this->request?->method(),
        ]);
    }

    public function sanitizeValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return Arr::except($values, config('audit-trail.ignored_fields', []));
    }

    protected function shouldTrack(string $key): bool
    {
        return (bool) config("audit-trail.{$key}", true);
    }

    protected function resolveUser(): ?Model
    {
        if (! $this->authFactory) {
            return null;
        }

        $user = $this->authFactory->guard()->user();

        return $user instanceof Model ? $user : null;
    }
}
