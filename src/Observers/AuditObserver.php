<?php

namespace NathanDeBarros\AuditTrail\Observers;

use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        audit()->log(
            action: $this->action($model, 'created'),
            auditable: $model,
            description: $this->description($model, 'created'),
            newValues: audit()->sanitizeValues($model->getAttributes()),
            event: 'created',
        );
    }

    public function updated(Model $model): void
    {
        $fields = array_keys(audit()->sanitizeValues($model->getChanges()) ?? []);

        if ($fields === []) {
            return;
        }

        $oldValues = [];
        $newValues = [];

        foreach ($fields as $field) {
            $oldValues[$field] = $model->getOriginal($field);
            $newValues[$field] = $model->getAttribute($field);
        }

        audit()->log(
            action: $this->action($model, 'updated'),
            auditable: $model,
            description: $this->description($model, 'updated'),
            oldValues: $oldValues,
            newValues: $newValues,
            event: 'updated',
        );
    }

    public function deleted(Model $model): void
    {
        audit()->log(
            action: $this->action($model, 'deleted'),
            auditable: $model,
            description: $this->description($model, 'deleted'),
            oldValues: audit()->sanitizeValues($model->getOriginal()),
            event: 'deleted',
        );
    }

    protected function action(Model $model, string $event): string
    {
        return str($model::class)->classBasename()->snake()->append(".{$event}")->value();
    }

    protected function description(Model $model, string $event): string
    {
        return str($model::class)->classBasename()->headline()->lower()->prepend('The ')->append(" model was {$event}.")->value();
    }
}
