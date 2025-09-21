<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920214018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "user_token" (
              id BIGSERIAL NOT NULL,
              user_id BIGINT NOT NULL,
              token VARCHAR(255) NOT NULL,
              user_agent TEXT DEFAULT NULL,
              ip_address VARCHAR(45) DEFAULT NULL,
              last_used_at TIMESTAMP(0)
              WITH
                TIME ZONE DEFAULT NULL,
                deleted_at TIMESTAMP(0)
              WITH
                TIME ZONE DEFAULT NULL,
                created_at TIMESTAMP(0)
              WITH
                TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0)
              WITH
                TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDF55A635F37A13B ON "user_token" (token)');
        $this->addSql('CREATE INDEX IDX_BDF55A63A76ED395 ON "user_token" (user_id)');
        $this->addSql(<<<'SQL'
            ALTER TABLE
              "user_token"
            ADD
              CONSTRAINT FK_BDF55A63A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user_token" DROP CONSTRAINT FK_BDF55A63A76ED395');
        $this->addSql('DROP TABLE "user_token"');
    }
}
