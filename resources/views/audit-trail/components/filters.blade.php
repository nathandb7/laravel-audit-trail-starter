<form class="audit-filters" method="GET" action="{{ route('audit-trail.index') }}" data-audit-filters>
    <label>
        <span>Buscar</span>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Accion, descripcion, IP...">
    </label>

    <label>
        <span>Usuario</span>
        <input type="text" name="user" value="{{ request('user') }}" placeholder="ID de usuario">
    </label>

    <label>
        <span>Accion</span>
        <input type="text" name="action" value="{{ request('action') }}" placeholder="sale.created">
    </label>

    <label>
        <span>Modulo</span>
        <select name="module">
            <option value="">Todos</option>
            @foreach ($modules as $module)
                <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
            @endforeach
        </select>
    </label>

    <label>
        <span>Evento</span>
        <select name="event">
            <option value="">Todos</option>
            @foreach ($events as $event)
                <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
            @endforeach
        </select>
    </label>

    <label>
        <span>Desde</span>
        <input type="date" name="from" value="{{ request('from') }}">
    </label>

    <label>
        <span>Hasta</span>
        <input type="date" name="to" value="{{ request('to') }}">
    </label>

    <div class="audit-filter-actions">
        <button class="audit-button" type="submit">Filtrar</button>
        <a class="audit-button audit-button-ghost" href="{{ route('audit-trail.index') }}">Limpiar</a>
    </div>
</form>
