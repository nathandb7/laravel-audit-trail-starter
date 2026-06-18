# Manual Log

```php
use NathanDeBarros\AuditTrail\Facades\AuditTrail;

AuditTrail::log(
    action: 'sale.cancelled',
    auditable: $sale,
    description: 'The sale was cancelled by an administrator.',
    module: 'sales',
    event: 'manual',
);
```
