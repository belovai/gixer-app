<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924101623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE probe (
              id BIGSERIAL NOT NULL,
              uuid UUID NOT NULL,
              name VARCHAR(255) NOT NULL,
              token VARCHAR(64) NOT NULL,
              last_seen_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D75E6F2AD17F50A6 ON probe (uuid)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D75E6F2A5F37A13B ON probe (token)');
        $this->addSql('COMMENT ON COLUMN probe.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE probe');
    }
}
