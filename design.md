# design.md — Laravel Audit Trail Starter

## Nombre del proyecto

**Laravel Audit Trail Starter**

Nombre sugerido para GitHub:

```txt
laravel-audit-trail-starter
```

Descripción corta:

```txt
A modern Laravel starter/package to record important user actions in an immutable audit trail.
```

Descripción en español:

```txt
Starter moderno para Laravel que permite registrar acciones importantes del sistema en un historial/auditoría inalterable, ideal para sistemas administrativos, ventas, inventario, finanzas, SaaS y apps empresariales.
```

---

# 1. Objetivo del proyecto

Crear un starter/paquete Laravel reutilizable para registrar acciones importantes dentro de una aplicación.

Debe permitir auditar eventos como:

```txt
- Usuario creado
- Usuario actualizado
- Producto creado
- Precio modificado
- Venta creada
- Venta anulada
- Pago registrado
- Pago eliminado
- Login exitoso
- Intento de acceso no autorizado
- Cambio de permisos
- Cambio de estado de una orden
```

El proyecto debe servir como:

```txt
- Paquete instalable para otros proyectos Laravel.
- Starter educativo para la comunidad.
- Base reutilizable para sistemas administrativos.
- Repositorio presentable en GitHub con README, ejemplos y demo visual.
```

---

# 2. Stack técnico

Usar:

```txt
- Laravel 13 o versión Laravel actual estable.
- PHP 8.3+.
- MySQL / MariaDB compatible.
- Eloquent Models.
- Service Provider.
- Migration publicable.
- Config publicable.
- Facade opcional.
- Helper opcional audit().
- Blade para demo/admin panel.
- CSS moderno sin dependencias pesadas.
- JavaScript vanilla para filtros e interacciones simples.
```

No usar frameworks pesados innecesarios.

Evitar:

```txt
- React.
- Vue.
- Inertia.
- Jetstream.
- Breeze obligatorio.
```

El paquete debe poder integrarse en cualquier Laravel existente.

---

# 3. Identidad visual del demo/admin

El panel visual debe sentirse moderno, limpio y profesional.

Inspiración visual:

```txt
- Dashboard SaaS moderno.
- Panel administrativo claro.
- Estética tipo security logs / activity center.
- Mucho espacio en blanco.
- Cards limpias.
- Bordes suaves.
- Sombras sutiles.
- Estados visuales por tipo de acción.
```

Paleta sugerida:

```txt
--audit-bg: #f8fafc;
--audit-surface: #ffffff;
--audit-dark: #0f172a;
--audit-muted: #64748b;
--audit-border: #e2e8f0;
--audit-primary: #2563eb;
--audit-success: #16a34a;
--audit-warning: #f59e0b;
--audit-danger: #dc2626;
--audit-info: #0891b2;
```

Tipografía:

```txt
font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
```

Estilo general:

```txt
- Fondo gris muy claro.
- Cards blancas.
- Header oscuro o blanco sticky.
- Badges de colores.
- Tabla desktop.
- Cards en mobile.
- Animaciones suaves.
- Transiciones de 180ms a 250ms.
```

---

# 4. Estructura del repositorio

Crear una estructura similar a esta:

```txt
laravel-audit-trail-starter/
│
├── src/
│   ├── AuditTrailServiceProvider.php
│   ├── Models/
│   │   └── AuditLog.php
│   ├── Facades/
│   │   └── AuditTrail.php
│   ├── Services/
│   │   └── AuditTrailService.php
│   ├── Contracts/
│   │   └── AuditableAction.php
│   ├── Traits/
│   │   └── Auditable.php
│   ├── Observers/
│   │   └── AuditObserver.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── AuditLogController.php
│   │   └── Middleware/
│   │       └── LogRouteActivity.php
│   ├── Console/
│   │   └── InstallAuditTrailCommand.php
│   └── helpers.php
│
├── database/
│   └── migrations/
│       └── create_audit_logs_table.php.stub
│
├── config/
│   └── audit-trail.php
│
├── resources/
│   ├── views/
│   │   └── audit-trail/
│   │       ├── index.blade.php
│   │       ├── show.blade.php
│   │       └── components/
│   │           ├── badge.blade.php
│   │           ├── stat-card.blade.php
│   │           └── filters.blade.php
│   ├── css/
│   │   └── audit-trail.css
│   └── js/
│       └── audit-trail.js
│
├── routes/
│   └── audit-trail.php
│
├── examples/
│   ├── basic-usage.md
│   ├── model-trait.md
│   ├── manual-log.md
│   ├── route-middleware.md
│   └── screenshots/
│
├── tests/
│   ├── Feature/
│   │   └── AuditTrailTest.php
│   └── Unit/
│       └── AuditTrailServiceTest.php
│
├── composer.json
├── README.md
├── CHANGELOG.md
├── LICENSE
└── design.md
```

---

# 5. Base de datos

Crear tabla:

```txt
audit_logs
```

Campos recomendados:

```txt
id
user_id nullable
user_type nullable
event string
action string
auditable_type nullable
auditable_id nullable
module nullable
description text nullable
old_values json nullable
new_values json nullable
metadata json nullable
ip_address nullable
user_agent nullable
url nullable
method nullable
created_at
```

No incluir `updated_at` porque el registro de auditoría no debería modificarse.

Ejemplo de eventos:

```txt
created
updated
deleted
restored
login
logout
failed_login
permission_changed
status_changed
manual
```

Ejemplo de acciones:

```txt
product.created
product.price_updated
sale.created
sale.cancelled
payment.received
user.role_changed
auth.login
auth.failed
```

---

# 6. Reglas de auditoría

El historial debe ser tratado como inalterable.

Reglas:

```txt
- No debe existir edición desde el panel.
- No debe existir eliminación desde el panel.
- El modelo AuditLog no debe exponer métodos públicos para borrar registros.
- La tabla no debe tener updated_at.
- El panel solo permite visualizar, buscar y filtrar.
- Las acciones destructivas deben quedar registradas antes de ejecutarse.
```

Agregar protección a nivel de modelo:

```php
public $timestamps = false;
```

O usar solo:

```php
const UPDATED_AT = null;
```

Evitar rutas para update/delete.

---

# 7. API de uso simple

El paquete debe permitir registrar acciones de forma sencilla.

Ejemplo:

```php
audit()->log(
    action: 'product.price_updated',
    description: 'El precio del producto fue modificado.',
    auditable: $product,
    oldValues: ['price' => 1200],
    newValues: ['price' => 1500],
    module: 'inventory'
);
```

También debe permitir:

```php
AuditTrail::log('sale.created', $sale);
```

Y desde el trait:

```php
use Auditable;
```

Ejemplo en modelo:

```php
class Product extends Model
{
    use Auditable;
}
```

El trait debe capturar:

```txt
created
updated
deleted
restored
```

---

# 8. Configuración

Archivo:

```txt
config/audit-trail.php
```

Debe incluir:

```php
return [
    'enabled' => env('AUDIT_TRAIL_ENABLED', true),

    'route_prefix' => 'audit-trail',

    'middleware' => ['web', 'auth'],

    'table_name' => 'audit_logs',

    'user_model' => App\Models\User::class,

    'track_ip' => true,

    'track_user_agent' => true,

    'track_url' => true,

    'track_auth_events' => true,

    'track_model_events' => true,

    'ignored_fields' => [
        'password',
        'remember_token',
        'updated_at',
    ],

    'ignored_models' => [
        //
    ],

    'modules' => [
        'auth',
        'users',
        'sales',
        'inventory',
        'finance',
        'settings',
    ],
];
```

---

# 9. Comando de instalación

Crear comando:

```bash
php artisan audit-trail:install
```

Debe ejecutar o indicar:

```txt
- Publicar configuración.
- Publicar migración.
- Publicar assets.
- Mostrar instrucciones finales.
```

También permitir:

```bash
php artisan vendor:publish --tag=audit-trail-config
php artisan vendor:publish --tag=audit-trail-migrations
php artisan vendor:publish --tag=audit-trail-assets
```

---

# 10. Rutas del panel

Crear rutas protegidas por middleware configurable.

Rutas:

```txt
GET /audit-trail
GET /audit-trail/{auditLog}
```

Opcional:

```txt
GET /audit-trail/export/csv
```

No crear rutas para:

```txt
POST update
DELETE destroy
```

---

# 11. Diseño de la pantalla principal

## Vista `/audit-trail`

Debe tener:

```txt
- Header superior.
- Título: Audit Trail.
- Subtítulo: Registro inalterable de acciones importantes del sistema.
- Cards de resumen.
- Filtros.
- Tabla de registros.
- Vista mobile en cards.
```

Cards superiores:

```txt
- Total logs
- Logs de hoy
- Acciones críticas
- Usuarios activos
```

Cada card debe tener:

```txt
- Ícono simple
- Número grande
- Label
- Pequeña descripción
```

Filtros:

```txt
- Buscar texto
- Usuario
- Acción
- Módulo
- Evento
- Fecha desde
- Fecha hasta
```

Tabla desktop:

```txt
Columnas:
- Fecha
- Usuario
- Acción
- Módulo
- Evento
- IP
- Ver
```

Card mobile:

```txt
- Acción como título
- Descripción
- Usuario
- Módulo
- Fecha
- Badge de evento
- Botón ver detalle
```

---

# 12. Diseño del detalle

## Vista `/audit-trail/{auditLog}`

Debe mostrar:

```txt
- Acción
- Descripción
- Usuario
- Modelo afectado
- ID del registro afectado
- Fecha
- IP
- User Agent
- URL
- Método HTTP
- Old values
- New values
- Metadata
```

Los JSON deben mostrarse en bloques visuales tipo código:

```txt
- Fondo #0f172a
- Texto claro
- Bordes suaves
- Scroll horizontal si es necesario
```

Debe tener botón:

```txt
Volver al historial
```

No debe tener botón de editar ni eliminar.

---

# 13. Badges visuales

Crear badges según tipo de acción.

```txt
created  => verde
updated  => azul
deleted  => rojo
login    => cyan
logout   => gris
failed   => naranja
manual   => violeta
critical => rojo fuerte
```

Ejemplo visual:

```html
<span class="audit-badge audit-badge-success">created</span>
```

---

# 14. CSS responsive

Desktop:

```txt
- Layout con max-width 1200px.
- Tabla limpia.
- Cards en grid.
```

Mobile:

```txt
- Ocultar tabla.
- Mostrar cards.
- Filtros en una columna.
- Botones full width.
- Padding cómodo.
```

Breakpoints:

```css
@media (max-width: 768px) {
  /* mobile cards */
}
```

---

# 15. Animaciones

Usar animaciones sutiles:

```txt
- Fade in al cargar cards.
- Hover suave en filas.
- Hover en botones.
- Transición en badges.
```

No usar animaciones exageradas.

Ejemplo:

```css
.audit-card {
  transition: transform .2s ease, box-shadow .2s ease;
}

.audit-card:hover {
  transform: translateY(-2px);
}
```

---

# 16. Middleware opcional

Crear middleware:

```txt
LogRouteActivity
```

Objetivo:

```txt
Registrar visitas o acciones importantes en rutas específicas.
```

Ejemplo:

```php
Route::post('/sales', [SaleController::class, 'store'])
    ->middleware('audit.route:sale.created');
```

---

# 17. Trait Auditable

Crear trait:

```txt
Auditable
```

Debe permitir que cualquier modelo registre:

```txt
created
updated
deleted
restored
```

Debe comparar valores anteriores y nuevos.

Ignorar campos configurados en:

```txt
config('audit-trail.ignored_fields')
```

Ejemplo:

```php
class Product extends Model
{
    use Auditable;
}
```

---

# 18. Helper global

Crear helper:

```php
audit()
```

Uso:

```php
audit()->log(
    action: 'payment.received',
    description: 'Se registró un pago.',
    module: 'finance',
);
```

---

# 19. Facade

Crear facade:

```php
AuditTrail::log(...)
```

Debe resolver:

```txt
AuditTrailService
```

---

# 20. Seguridad

El panel debe estar protegido por middleware.

Por defecto:

```php
['web', 'auth']
```

Permitir configurar middleware extra:

```php
'audit',
'can:viewAuditTrail'
```

No exponer logs a usuarios no autenticados.

---

# 21. Tests mínimos

Crear tests para:

```txt
- Puede registrar un log manual.
- Puede registrar old_values y new_values.
- Ignora campos sensibles.
- No tiene ruta delete.
- El helper audit() funciona.
- La facade funciona.
- El trait registra created.
- El trait registra updated.
```

---

# 22. README.md

El README debe incluir:

```txt
- Título
- Descripción
- Badges simples
- Instalación
- Publicar config
- Ejecutar migración
- Uso básico
- Uso con trait
- Uso con facade
- Uso con helper
- Capturas o espacio para capturas
- Roadmap
- Contribuir
- Licencia MIT
```

Ejemplo de intro:

```md
# Laravel Audit Trail Starter

A lightweight and modern audit trail starter for Laravel applications.

Record important user actions, model changes, authentication events and critical business operations in an immutable activity history.
```

---

# 23. Roadmap

Agregar al README:

```txt
- Export CSV
- Export PDF
- Pruning opcional con backup
- Dashboard con gráficos
- Soporte multi-tenant
- Integración con Spatie Permissions
- Notificaciones para acciones críticas
- Webhook para enviar logs externos
```

---

# 24. Criterios de aceptación

El proyecto se considera terminado cuando:

```txt
- Se puede instalar en un proyecto Laravel.
- Se puede publicar config, migraciones y assets.
- Se puede ejecutar la migración.
- Se puede registrar un log manual.
- Se puede registrar un log usando facade.
- Se puede registrar un log usando helper.
- Se puede auditar un modelo con trait.
- Existe panel visual en /audit-trail.
- El panel es responsive.
- En mobile la tabla se convierte en cards.
- No existen acciones de editar ni eliminar logs.
- README está completo.
- Tests básicos pasan.
```

---

# 25. Resultado esperado

Crear un repo limpio, público y profesional que se vea como un aporte real para la comunidad Laravel.

Debe sentirse:

```txt
- Simple
- Útil
- Seguro
- Profesional
- Fácil de instalar
- Fácil de entender
- Visualmente moderno
```

El objetivo no es crear un paquete gigante, sino un starter claro, terminado y bien documentado.
