package service

import "github.com/belovai/gixer-app/repository"

type Services struct {
	UserService *UserService
}

func InitServices(repositories *repository.Repositories) *Services {
	return &Services{
		UserService: NewUserService(repositories.UserRepository, repositories.ApiTokenRepository),
	}
}
