package service

import (
	"context"
	"errors"
	"fmt"

	"github.com/belovai/gixer-app/model"
	"github.com/belovai/gixer-app/repository"
	"golang.org/x/crypto/bcrypt"
)

var ErrEmailTaken = errors.New("email address is already in use")

type UserService struct {
	userRepository *repository.UserRepository
}

func NewUserService(userRepository *repository.UserRepository) *UserService {
	return &UserService{userRepository: userRepository}
}

func (s *UserService) CreateUser(ctx context.Context, params repository.CreateUserParams) (model.User, error) {
	_, err := s.userRepository.GetUserByEmail(ctx, params.Email)
	if err == nil {
		return model.User{}, ErrEmailTaken
	}

	hashedPassword, err := bcrypt.GenerateFromPassword([]byte(params.Password), bcrypt.DefaultCost)
	if err != nil {
		return model.User{}, fmt.Errorf("failed to hash password: %w", err)
	}

	params.Password = string(hashedPassword)

	return s.userRepository.CreateUser(ctx, params)
}

func (s *UserService) GetUsers(ctx context.Context, page, pageSize int) ([]model.User, int, error) {
	if page < 1 {
		page = 1
	}
	if pageSize < 1 {
		pageSize = 10
	}
	if pageSize > 100 {
		pageSize = 100
	}

	offset := (page - 1) * pageSize

	return s.userRepository.GetUsers(ctx, pageSize, offset)
}
