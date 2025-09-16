package repository

type ApiTokenRepository struct {
	db DbConnection
}

func NewApiTokenRepository(db DbConnection) *ApiTokenRepository {
	return &ApiTokenRepository{db: db}
}
