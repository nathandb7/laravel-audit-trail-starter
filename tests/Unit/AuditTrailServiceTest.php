<?php

namespace NathanDeBarros\AuditTrail\Tests\Unit;

use NathanDeBarros\AuditTrail\Facades\AuditTrail;
use NathanDeBarros\AuditTrail\Models\AuditLog;
use NathanDeBarros\AuditTrail\Services\AuditTrailService;
use NathanDeBarros\AuditTrail\Tests\TestCase;

class AuditTrailServiceTest extends TestCase
{
    public function test_it_can_register_a_manual_log(): void
    {
        audit()->log(
            action: 'payment.received',
            description: 'A payment was registered.',
            module: 'finance',
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'payment.received',
            'event' => 'manual',
            'module' => 'finance',
        ]);
    }

    public function test_it_stores_old_and_new_values(): void
    {
        audit()->log(
            action: 'product.price_updated',
            oldValues: ['price' => 1200],
            newValues: ['price' => 1500],
        );

        $log = AuditLog::query()->firstOrFail();

        $this->assertSame(['price' => 1200], $log->old_values);
        $this->assertSame(['price' => 1500], $log->new_values);
    }

    public function test_it_ignores_sensitive_fields(): void
    {
        $service = app(AuditTrailService::class);

        $this->assertSame(['name' => 'Nathan'], $service->sanitizeValues([
            'name' => 'Nathan',
            'password' => 'secret',
            'remember_token' => 'token',
        ]));
    }

    public function test_helper_and_facade_work(): void
    {
        $this->assertInstanceOf(AuditTrailService::class, audit());

        AuditTrail::log('sale.created');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'sale.created',
        ]);
    }
}
