# Basic Usage

```php
audit()->log(
    action: 'order.status_changed',
    description: 'The order status changed from pending to paid.',
    oldValues: ['status' => 'pending'],
    newValues: ['status' => 'paid'],
    module: 'sales',
);
```
