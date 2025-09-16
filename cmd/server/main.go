package main

import (
	"fmt"
	"log"
	"os"

	"github.com/belovai/gixer-app/config"
	"github.com/belovai/gixer-app/database"
	"github.com/belovai/gixer-app/repository"
	"github.com/belovai/gixer-app/service"
)

type application struct {
	errorLog     *log.Logger
	infoLog      *log.Logger
	debugLog     *log.Logger
	config       config.Config
	repositories *repository.Repositories
	services     *service.Services
}

func main() {
	app := application{}
	err := app.init()
	if err != nil {
		panic(err)
	}

	dbConn, err := database.GetDatabase(app.config.Database)
	if err != nil {
		return
	}
	defer dbConn.Close()

	app.repositories = repository.InitRepositories(dbConn)
	app.services = service.InitServices(app.repositories)

	r := app.routes()

	if err = r.Run(fmt.Sprintf("%s:%d", app.config.Server.Host, app.config.Server.Port)); err != nil {
		app.errorLog.Fatal(err)
	}
}

func (app *application) init() (err error) {
	app.errorLog = log.New(os.Stderr, "ERROR: ", log.Lshortfile)
	app.infoLog = log.New(os.Stdout, "INFO: ", log.Lshortfile)
	app.debugLog = log.New(os.Stdout, "DEBUG: ", log.Lshortfile)

	app.config, err = config.LoadConfig()
	if err != nil {
		return
	}

	return nil
}
