<?php

namespace NathanDeBarros\AuditTrail\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRouteActivity
{
    public function handle(Request $request, Closure $next, ?string $action = null, ?string $module = null): Response
    {
        $response = $next($request);

        if ($response->isSuccessful()) {
            audit()->log(
                action: $action ?: $request->route()?->getName() ?: 'route.visited',
                description: 'A protected route was accessed.',
                metadata: [
                    'route' => $request->route()?->getName(),
                    'status' => $response->getStatusCode(),
                ],
                module: $module,
                event: 'manual',
            );
        }

        return $response;
    }
}
