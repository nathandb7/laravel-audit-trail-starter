<?php

use NathanDeBarros\AuditTrail\Services\AuditTrailService;

if (! function_exists('audit')) {
    function audit(): AuditTrailService
    {
        return app(AuditTrailService::class);
    }
}
