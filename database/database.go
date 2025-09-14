package database

import (
	"context"
	"fmt"

	"github.com/belovai/gixer-app/config"
	"github.com/jackc/pgx/v5/pgxpool"
)

func GetDatabase(config config.DatabaseConfig) (*pgxpool.Pool, error) {
	dsn := fmt.Sprintf("postgres://%s:%s@%s:%d/%s?sslmode=disable",
		config.User,
		config.Password,
		config.Host,
		config.Port,
		config.DBName)

	pool, err := pgxpool.New(context.Background(), dsn)
	if err != nil {
		return nil, err
	}

	return pool, nil
}
