package repository

import (
	"context"

	"github.com/belovai/gixer-app/model"
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

func (repo *UserRepository) CreateUser(ctx context.Context, arg CreateUserParams) (model.User, error) {
	const createUserQuery = `INSERT INTO users (email, password, timezone, locale, enabled) VALUES ($1, $2, $3, $4, $5) RETURNING *`
	var user model.User
	err := repo.db.QueryRow(ctx, createUserQuery, arg.Email, arg.Password, arg.Timezone, arg.Locale, arg.Enabled).Scan(&user.ID, &user.Email, &user.EmailVerifiedAt, &user.Password, &user.Timezone, &user.Locale, &user.Enabled, &user.CreatedAt, &user.UpdatedAt)
	if err != nil {
		return model.User{}, err
	}

	return user, nil
}
