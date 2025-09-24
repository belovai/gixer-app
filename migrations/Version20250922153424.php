<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250922153424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE http_monitor (
              id BIGINT NOT NULL,
              uuid UUID NOT NULL,
              url VARCHAR(500) NOT NULL,
              http_method VARCHAR(255) NOT NULL,
              http_body TEXT DEFAULT NULL,
              http_headers JSON DEFAULT NULL,
              authentication JSON DEFAULT NULL,
              expected_status_codes JSON NOT NULL,
              expected_content TEXT DEFAULT NULL,
              timeout INT DEFAULT 10 NOT NULL,
              max_redirects SMALLINT DEFAULT 10 NOT NULL,
              upside_down BOOLEAN DEFAULT false NOT NULL,
              ignore_ssl_errors BOOLEAN DEFAULT false NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E40EBC4ED17F50A6 ON http_monitor (uuid)');
        $this->addSql('COMMENT ON COLUMN http_monitor.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE monitor (
              id BIGSERIAL NOT NULL,
              monitorable_id BIGINT NOT NULL,
              uuid UUID NOT NULL,
              name VARCHAR(255) NOT NULL,
              description TEXT NOT NULL,
              enabled BOOLEAN DEFAULT true NOT NULL,
              interval INT NOT NULL,
              retry_interval INT NOT NULL,
              retry_max INT NOT NULL,
              deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1159985D17F50A6 ON monitor (uuid)');
        $this->addSql('CREATE INDEX IDX_E11599851A2F0B50 ON monitor (monitorable_id)');
        $this->addSql('COMMENT ON COLUMN monitor.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE monitorable (id BIGSERIAL NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql(<<<'SQL'
            CREATE TABLE ping_monitor (
              id BIGINT NOT NULL,
              hostname VARCHAR(500) NOT NULL,
              packet_size SMALLINT NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              http_monitor
            ADD
              CONSTRAINT FK_E40EBC4EBF396750 FOREIGN KEY (id) REFERENCES monitorable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              monitor
            ADD
              CONSTRAINT FK_E11599851A2F0B50 FOREIGN KEY (monitorable_id) REFERENCES monitorable (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              ping_monitor
            ADD
              CONSTRAINT FK_73559019BF396750 FOREIGN KEY (id) REFERENCES monitorable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE http_monitor DROP CONSTRAINT FK_E40EBC4EBF396750');
        $this->addSql('ALTER TABLE monitor DROP CONSTRAINT FK_E11599851A2F0B50');
        $this->addSql('ALTER TABLE ping_monitor DROP CONSTRAINT FK_73559019BF396750');
        $this->addSql('DROP TABLE http_monitor');
        $this->addSql('DROP TABLE monitor');
        $this->addSql('DROP TABLE monitorable');
        $this->addSql('DROP TABLE ping_monitor');
    }
}
