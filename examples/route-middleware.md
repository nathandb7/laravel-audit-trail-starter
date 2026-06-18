# Route Middleware

```php
Route::post('/sales', [SaleController::class, 'store'])
    ->middleware('audit.route:sale.created,sales');
```

The middleware writes an audit log after a successful response.
