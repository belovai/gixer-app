package main

import (
	"github.com/belovai/gixer-app/handler"
	"github.com/gin-gonic/gin"
)

func (app *application) routes() *gin.Engine {
	r := gin.Default()

	userHandler := handler.NewUserHandler(app.services.UserService)

	r.GET("/users", userHandler.ListUsers)

	return r
}
