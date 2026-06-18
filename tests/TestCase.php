<?php

namespace NathanDeBarros\AuditTrail\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use NathanDeBarros\AuditTrail\AuditTrailServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            AuditTrailServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('audit-trail.middleware', ['web']);
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->nullableMorphs('user');
            $table->string('event')->index();
            $table->string('action')->index();
            $table->nullableMorphs('auditable');
            $table->string('module')->nullable()->index();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 16)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });

        Schema::create('audit_test_products', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->integer('price')->default(0);
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    protected function assertNoDeleteAuditTrailRoute(): void
    {
        $routes = collect(Route::getRoutes())->filter(fn ($route) => str($route->uri())->startsWith('audit-trail'));

        $this->assertFalse($routes->contains(fn ($route) => in_array('DELETE', $route->methods(), true)));
    }
}

class Product extends Model
{
    use \NathanDeBarros\AuditTrail\Traits\Auditable;

    protected $table = 'audit_test_products';

    protected $guarded = [];
}
