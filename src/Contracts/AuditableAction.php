<?php

namespace NathanDeBarros\AuditTrail\Contracts;

interface AuditableAction
{
    public function auditAction(): string;

    public function auditDescription(): ?string;

    public function auditModule(): ?string;
}
