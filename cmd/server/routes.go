package main

import (
	"github.com/belovai/gixer-app/handler"
	"github.com/belovai/gixer-app/middleware"
	"github.com/gin-gonic/gin"
)

func (app *application) routes() *gin.Engine {
	authMiddleware := middleware.AuthMiddleware(*app.repositories.UserRepository)

	userHandler := handler.NewUserHandler(app.services.UserService)

	router := gin.Default()

	router.POST("/api/register", userHandler.Register)
	router.POST("/api/login", userHandler.CreateUser)

	authorized := router.Group("/")
	authorized.Use(authMiddleware)
	{
		authorized.GET("/api/users", userHandler.ListUsers)
	}

	return router
}
