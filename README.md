# Gixer

This is a tinkering project. Not for production yet.

## Development

## Migrations

### Run migrations in local
```bash
docker run --rm -it -v $(pwd)/database/migrations:/migrations --network host migrate/migrate \
    -path=/migrations -database "postgres://postgres:password@localhost:5432/gixer?sslmode=disable" up
```

### Rollback migrations in local
```bash
docker run --rm -it -v $(pwd)/database/migrations:/migrations --network host migrate/migrate \
    -path=/migrations -database "postgres://postgres:password@localhost:5432/gixer?sslmode=disable" down 1
```

### Create a new migration
```bash
docker run --rm -it -v $(pwd)/database/migrations:/migrations --network host migrate/migrate \
    create -ext sql -dir /migrations -seq  
```