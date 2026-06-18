@php
    $event = $event ?? 'manual';
    $type = match ($event) {
        'created' => 'success',
        'updated' => 'info',
        'deleted', 'critical' => 'danger',
        'login' => 'cyan',
        'logout' => 'muted',
        'failed', 'failed_login' => 'warning',
        default => 'primary',
    };
@endphp

<span class="audit-badge audit-badge-{{ $type }}">{{ $event }}</span>
