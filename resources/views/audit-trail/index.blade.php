<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit Trail</title>
    <link rel="stylesheet" href="{{ asset('vendor/audit-trail/audit-trail.css') }}">
</head>
<body class="audit-body">
    <main class="audit-shell">
        <header class="audit-header">
            <div>
                <p class="audit-eyebrow">Security logs</p>
                <h1>Audit Trail</h1>
                <p>Registro inalterable de acciones importantes del sistema.</p>
            </div>
        </header>

        <section class="audit-stats" aria-label="Resumen">
            @include('audit-trail::audit-trail.components.stat-card', ['icon' => '#', 'value' => number_format($stats['total']), 'label' => 'Total logs', 'description' => 'Registros guardados'])
            @include('audit-trail::audit-trail.components.stat-card', ['icon' => '+', 'value' => number_format($stats['today']), 'label' => 'Logs de hoy', 'description' => 'Actividad reciente'])
            @include('audit-trail::audit-trail.components.stat-card', ['icon' => '!', 'value' => number_format($stats['critical']), 'label' => 'Acciones criticas', 'description' => 'Eventos sensibles'])
            @include('audit-trail::audit-trail.components.stat-card', ['icon' => '@', 'value' => number_format($stats['users']), 'label' => 'Usuarios activos', 'description' => 'Usuarios auditados'])
        </section>

        @include('audit-trail::audit-trail.components.filters', ['modules' => $modules, 'events' => $events])

        <section class="audit-panel">
            <div class="audit-table-wrap">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Accion</th>
                            <th>Modulo</th>
                            <th>Evento</th>
                            <th>IP</th>
                            <th>Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $log->user_id ? class_basename($log->user_type) . ' #' . $log->user_id : 'Sistema' }}</td>
                                <td>
                                    <strong>{{ $log->action }}</strong>
                                    @if ($log->description)
                                        <small>{{ $log->description }}</small>
                                    @endif
                                </td>
                                <td>{{ $log->module ?? '-' }}</td>
                                <td>@include('audit-trail::audit-trail.components.badge', ['event' => $log->event])</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td><a class="audit-link" href="{{ route('audit-trail.show', $log) }}">Detalle</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="audit-empty">No hay logs para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="audit-mobile-list">
                @forelse ($logs as $log)
                    <article class="audit-log-card">
                        <div class="audit-log-card-head">
                            <strong>{{ $log->action }}</strong>
                            @include('audit-trail::audit-trail.components.badge', ['event' => $log->event])
                        </div>
                        <p>{{ $log->description ?: 'Sin descripcion.' }}</p>
                        <dl>
                            <div><dt>Usuario</dt><dd>{{ $log->user_id ? class_basename($log->user_type) . ' #' . $log->user_id : 'Sistema' }}</dd></div>
                            <div><dt>Modulo</dt><dd>{{ $log->module ?? '-' }}</dd></div>
                            <div><dt>Fecha</dt><dd>{{ $log->created_at?->format('Y-m-d H:i') }}</dd></div>
                        </dl>
                        <a class="audit-button audit-button-full" href="{{ route('audit-trail.show', $log) }}">Ver detalle</a>
                    </article>
                @empty
                    <div class="audit-empty">No hay logs para mostrar.</div>
                @endforelse
            </div>

            <div class="audit-pagination">
                {{ $logs->links() }}
            </div>
        </section>
    </main>

    <script src="{{ asset('vendor/audit-trail/audit-trail.js') }}" defer></script>
</body>
</html>
