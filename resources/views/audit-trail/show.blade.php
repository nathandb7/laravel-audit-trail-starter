<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $auditLog->action }} - Audit Trail</title>
    <link rel="stylesheet" href="{{ asset('vendor/audit-trail/audit-trail.css') }}">
</head>
<body class="audit-body">
    <main class="audit-shell">
        <header class="audit-header audit-header-row">
            <div>
                <p class="audit-eyebrow">Audit detail</p>
                <h1>{{ $auditLog->action }}</h1>
                <p>{{ $auditLog->description ?: 'Registro de auditoria sin descripcion.' }}</p>
            </div>
            <a class="audit-button audit-button-ghost" href="{{ route('audit-trail.index') }}">Volver al historial</a>
        </header>

        <section class="audit-detail-grid">
            <article class="audit-detail-item">
                <span>Evento</span>
                @include('audit-trail::audit-trail.components.badge', ['event' => $auditLog->event])
            </article>
            <article class="audit-detail-item"><span>Usuario</span><strong>{{ $auditLog->user_id ? class_basename($auditLog->user_type) . ' #' . $auditLog->user_id : 'Sistema' }}</strong></article>
            <article class="audit-detail-item"><span>Modelo afectado</span><strong>{{ $auditLog->auditable_type ? class_basename($auditLog->auditable_type) : '-' }}</strong></article>
            <article class="audit-detail-item"><span>ID afectado</span><strong>{{ $auditLog->auditable_id ?? '-' }}</strong></article>
            <article class="audit-detail-item"><span>Fecha</span><strong>{{ $auditLog->created_at?->format('Y-m-d H:i:s') }}</strong></article>
            <article class="audit-detail-item"><span>IP</span><strong>{{ $auditLog->ip_address ?? '-' }}</strong></article>
            <article class="audit-detail-item audit-detail-wide"><span>User Agent</span><strong>{{ $auditLog->user_agent ?? '-' }}</strong></article>
            <article class="audit-detail-item audit-detail-wide"><span>URL</span><strong>{{ $auditLog->url ?? '-' }}</strong></article>
            <article class="audit-detail-item"><span>Metodo HTTP</span><strong>{{ $auditLog->method ?? '-' }}</strong></article>
            <article class="audit-detail-item"><span>Modulo</span><strong>{{ $auditLog->module ?? '-' }}</strong></article>
        </section>

        <section class="audit-json-grid">
            <article>
                <h2>Old values</h2>
                <pre>{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: 'null' }}</pre>
            </article>
            <article>
                <h2>New values</h2>
                <pre>{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: 'null' }}</pre>
            </article>
            <article>
                <h2>Metadata</h2>
                <pre>{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: 'null' }}</pre>
            </article>
        </section>
    </main>
</body>
</html>
