# Model Trait

```php
use Illuminate\Database\Eloquent\Model;
use NathanDeBarros\AuditTrail\Traits\Auditable;

class Product extends Model
{
    use Auditable;
}
```

The trait records `created`, `updated`, `deleted`, and `restored` events when model event tracking is enabled.
