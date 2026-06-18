<?php

namespace NathanDeBarros\AuditTrail\Traits;

use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            if (! static::shouldAuditModel($model)) {
                return;
            }

            audit()->log(
                action: static::auditActionFor($model, 'created'),
                auditable: $model,
                description: static::auditDescriptionFor($model, 'created'),
                newValues: static::auditValues($model->getAttributes()),
                module: static::auditModuleFor($model),
                event: 'created',
            );
        });

        static::updated(function (Model $model): void {
            if (! static::shouldAuditModel($model)) {
                return;
            }

            $changed = array_keys(static::auditValues($model->getChanges()));

            if ($changed === []) {
                return;
            }

            $oldValues = [];
            $newValues = [];

            foreach ($changed as $field) {
                $oldValues[$field] = $model->getOriginal($field);
                $newValues[$field] = $model->getAttribute($field);
            }

            audit()->log(
                action: static::auditActionFor($model, 'updated'),
                auditable: $model,
                description: static::auditDescriptionFor($model, 'updated'),
                oldValues: $oldValues,
                newValues: $newValues,
                module: static::auditModuleFor($model),
                event: 'updated',
            );
        });

        static::deleted(function (Model $model): void {
            if (! static::shouldAuditModel($model)) {
                return;
            }

            audit()->log(
                action: static::auditActionFor($model, 'deleted'),
                auditable: $model,
                description: static::auditDescriptionFor($model, 'deleted'),
                oldValues: static::auditValues($model->getOriginal()),
                module: static::auditModuleFor($model),
                event: 'deleted',
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model): void {
                if (! static::shouldAuditModel($model)) {
                    return;
                }

                audit()->log(
                    action: static::auditActionFor($model, 'restored'),
                    auditable: $model,
                    description: static::auditDescriptionFor($model, 'restored'),
                    newValues: static::auditValues($model->getAttributes()),
                    module: static::auditModuleFor($model),
                    event: 'restored',
                );
            });
        }
    }

    protected static function shouldAuditModel(Model $model): bool
    {
        if (! config('audit-trail.track_model_events', true)) {
            return false;
        }

        return ! in_array($model::class, config('audit-trail.ignored_models', []), true);
    }

    protected static function auditActionFor(Model $model, string $event): string
    {
        $name = str($model::class)->classBasename()->snake()->value();

        return "{$name}.{$event}";
    }

    protected static function auditDescriptionFor(Model $model, string $event): string
    {
        $name = str($model::class)->classBasename()->headline()->lower()->value();

        return "The {$name} model was {$event}.";
    }

    protected static function auditModuleFor(Model $model): ?string
    {
        return property_exists($model, 'auditModule') ? $model->auditModule : null;
    }

    protected static function auditValues(array $values): array
    {
        return audit()->sanitizeValues($values) ?? [];
    }
}
