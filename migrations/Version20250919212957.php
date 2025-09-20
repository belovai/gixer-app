<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919212957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (
              id BIGSERIAL NOT NULL,
              uuid UUID NOT NULL,
              email VARCHAR(255) NOT NULL,
              email_verified_at TIMESTAMP(0)
              WITH
                TIME ZONE DEFAULT NULL,
                password VARCHAR(255) NOT NULL,
                timezone VARCHAR(64) DEFAULT 'UTC' NOT NULL,
                locale VARCHAR(10) DEFAULT 'en' NOT NULL,
                enabled BOOLEAN DEFAULT true NOT NULL,
                created_at TIMESTAMP(0)
              WITH
                TIME ZONE NOT NULL,
                updated_at TIMESTAMP(0)
              WITH
                TIME ZONE NOT NULL,
                PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "user"');
    }
}
