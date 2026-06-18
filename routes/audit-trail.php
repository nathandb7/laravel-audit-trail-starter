<?php

use Illuminate\Support\Facades\Route;
use NathanDeBarros\AuditTrail\Http\Controllers\AuditLogController;

Route::middleware(config('audit-trail.middleware', ['web', 'auth']))
    ->prefix(config('audit-trail.route_prefix', 'audit-trail'))
    ->as('audit-trail.')
    ->group(function (): void {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [AuditLogController::class, 'show'])->name('show');
    });
