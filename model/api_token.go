package model

import "time"

type ApiToken struct {
	ID         int64     `json:"id"`
	UserId     int64     `json:"user_id"`
	Name       string    `json:"name"`
	TokenHash  string    `json:"token_hash"`
	Abilities  []string  `json:"abilities"`
	LastUsedAt time.Time `json:"last_used_at"`
	CreatedAt  time.Time `json:"created_at"`
	UpdatedAt  time.Time `json:"updated_at"`
}
