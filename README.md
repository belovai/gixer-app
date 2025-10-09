# gixer

This is a tinkering project. Not for production yet.

## Coding Stile Fixes

```bash
./vendor/bin/php-cs-fixer fix src
```

## Testing

First, create the database and the schema:
```bash
docker compose exec app php bin/console --env=test doctrine:database:drop --force
docker compose exec app php bin/console --env=test doctrine:database:create
docker compose exec app php bin/console --env=test doctrine:schema:create
```

Then you can run the test:

```bash
docker compose exec app php bin/phpunit
```
