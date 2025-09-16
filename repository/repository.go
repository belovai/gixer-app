package repository

import (
	"context"
	"errors"

	"github.com/jackc/pgx/v5"
	"github.com/jackc/pgx/v5/pgconn"
)

var ErrNotFound = errors.New("record not found")

type DbConnection interface {
	Exec(context.Context, string, ...interface{}) (pgconn.CommandTag, error)
	Query(context.Context, string, ...interface{}) (pgx.Rows, error)
	QueryRow(context.Context, string, ...interface{}) pgx.Row
}

type Repositories struct {
	UserRepository     *UserRepository
	ApiTokenRepository *ApiTokenRepository
}

func InitRepositories(db DbConnection) *Repositories {
	return &Repositories{
		UserRepository:     NewUserRepository(db),
		ApiTokenRepository: NewApiTokenRepository(db),
	}
}
