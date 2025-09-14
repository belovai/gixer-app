package main

import (
	"log"
	"os"

	"github.com/belovai/gixer-app/config"
	"github.com/belovai/gixer-app/database"
	"github.com/belovai/gixer-app/repository"
)

type application struct {
	errorLog     *log.Logger
	infoLog      *log.Logger
	debugLog     *log.Logger
	config       config.Config
	repositories *repository.Repositories
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

	//usr := repository.CreateUserParams{
	//	Email:    "a@example.com",
	//	Password: "password",
	//	Timezone: "UTC",
	//	Locale:   "en",
	//	Enabled:  false,
	//}
	//
	//user, err := app.repositories.UserRepository.CreateUser(context.Background(), usr)
	//if err != nil {
	//	panic(err)
	//}
	//
	//app.infoLog.Printf("User: %+v", user)

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

func (app *application) migrate() {
	//dsn := "postgres://postgres:password@localhost:5432/gixer?sslmode=disable"

	//conn, err := pgx.Connect(context.Background(), dsn)
	//if err != nil {
	//	app.errorLog.Fatalf("Unable to connect to database: %v\\n", err)
	//}
	//
	//defer conn.Close(context.Background())

}
