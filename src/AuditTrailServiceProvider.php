<?php

namespace NathanDeBarros\AuditTrail;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use NathanDeBarros\AuditTrail\Console\InstallAuditTrailCommand;
use NathanDeBarros\AuditTrail\Http\Middleware\LogRouteActivity;
use NathanDeBarros\AuditTrail\Services\AuditTrailService;

class AuditTrailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/audit-trail.php', 'audit-trail');

        $this->app->singleton(AuditTrailService::class, fn ($app) => new AuditTrailService(
            request: $app->bound('request') ? $app['request'] : null,
            authFactory: $app->bound('auth') ? $app['auth'] : null,
        ));

        $this->app->alias(AuditTrailService::class, 'audit-trail');
    }

    public function boot(Router $router): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'audit-trail');
        $this->loadRoutesFrom(__DIR__ . '/../routes/audit-trail.php');

        $router->aliasMiddleware('audit.route', LogRouteActivity::class);

        $this->publishes([
            __DIR__ . '/../config/audit-trail.php' => config_path('audit-trail.php'),
        ], 'audit-trail-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_audit_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_audit_logs_table.php'),
        ], 'audit-trail-migrations');

        $this->publishes([
            __DIR__ . '/../resources/css/audit-trail.css' => public_path('vendor/audit-trail/audit-trail.css'),
            __DIR__ . '/../resources/js/audit-trail.js' => public_path('vendor/audit-trail/audit-trail.js'),
        ], 'audit-trail-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallAuditTrailCommand::class,
            ]);
        }
    }
}
