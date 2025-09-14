package repository

import (
	"context"
	"database/sql"
	"errors"

	"github.com/belovai/gixer-app/model"
	"github.com/jackc/pgx/v5"
)

type UserRepository struct {
	db DbConnection
}

func NewUserRepository(db DbConnection) *UserRepository {
	return &UserRepository{db: db}
}

type CreateUserParams struct {
	Email    string
	Password string
	Timezone string
	Locale   string
	Enabled  bool
}

func (repo *UserRepository) CreateUser(ctx context.Context, params CreateUserParams) (model.User, error) {
	const createUserQuery = `INSERT INTO users (email, password, timezone, locale, enabled) VALUES ($1, $2, $3, $4, $5) RETURNING *`
	row := repo.db.QueryRow(ctx, createUserQuery, params.Email, params.Password, params.Timezone, params.Locale, params.Enabled)

	user, err := repo.scanUser(row)
	if err != nil {
		return model.User{}, err
	}

	return user, nil
}

func (repo *UserRepository) GetUserByEmail(ctx context.Context, email string) (model.User, error) {
	const getUserByEmailQuery = `SELECT * FROM users WHERE email = $1 LIMIT 1`

	row := repo.db.QueryRow(ctx, getUserByEmailQuery, email)

	user, err := repo.scanUser(row)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return model.User{}, ErrNotFound
		}
		return model.User{}, err
	}

	return user, nil
}

func (repo *UserRepository) scanUser(row pgx.Row) (model.User, error) {
	var user model.User
	err := row.Scan(
		&user.ID,
		&user.Email,
		&user.EmailVerifiedAt,
		&user.Password,
		&user.Timezone,
		&user.Locale,
		&user.Enabled,
		&user.CreatedAt,
		&user.UpdatedAt,
	)

	return user, err
}
