<?php

namespace NathanDeBarros\AuditTrail\Tests\Feature;

use Illuminate\Support\Facades\Route;
use NathanDeBarros\AuditTrail\Models\AuditLog;
use NathanDeBarros\AuditTrail\Tests\Product;
use NathanDeBarros\AuditTrail\Tests\TestCase;

class AuditTrailTest extends TestCase
{
    public function test_it_has_no_delete_route(): void
    {
        $this->assertNoDeleteAuditTrailRoute();
    }

    public function test_panel_index_route_is_available(): void
    {
        AuditLog::query()->create([
            'event' => 'manual',
            'action' => 'system.checked',
            'description' => 'System check.',
        ]);

        $this->get('/audit-trail')
            ->assertOk()
            ->assertSee('Audit Trail')
            ->assertSee('system.checked');
    }

    public function test_trait_registers_created(): void
    {
        Product::query()->create([
            'name' => 'Notebook',
            'price' => 1200,
            'password' => 'hidden',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'created',
            'action' => 'product.created',
        ]);

        $this->assertStringNotContainsString('hidden', AuditLog::query()->firstOrFail()->toJson());
    }

    public function test_trait_registers_updated(): void
    {
        $product = Product::query()->create([
            'name' => 'Notebook',
            'price' => 1200,
        ]);

        $product->update(['price' => 1500]);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'updated',
            'action' => 'product.updated',
        ]);

        $log = AuditLog::query()->where('event', 'updated')->firstOrFail();

        $this->assertSame(1200, $log->old_values['price']);
        $this->assertSame(1500, $log->new_values['price']);
    }

    public function test_audit_route_middleware_logs_successful_route(): void
    {
        Route::middleware('audit.route:demo.visited,demo')->get('/demo-audited-route', fn () => 'ok');

        $this->get('/demo-audited-route')->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'demo.visited',
            'module' => 'demo',
        ]);
    }
}
