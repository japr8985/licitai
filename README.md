## Seed DB de un tenant
```bash
php artisan tenants:run db:seed --tenants=globalcorp --option="class=TenantDatabaseSeeder"
```