package model

import (
	"database/sql"
	"time"
)

type User struct {
	ID              int64        `json:"id"`
	Email           string       `json:"email"`
	EmailVerifiedAt sql.NullTime `json:"email_verified_at"`
	Password        string       `json:"-"`
	Timezone        string       `json:"timezone"`
	Locale          string       `json:"locale"`
	Enabled         bool         `json:"enabled"`
	CreatedAt       time.Time    `json:"created_at"`
	UpdatedAt       time.Time    `json:"updated_at"`
}
