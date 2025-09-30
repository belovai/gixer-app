<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924145223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE metric (
              uuid UUID NOT NULL,
              executed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              monitor_id BIGINT NOT NULL,
              probe_id BIGINT NOT NULL,
              status VARCHAR(255) NOT NULL,
              metrics_data JSONB DEFAULT NULL,
              scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              queued_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(uuid, executed_at)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_87D62EE34CE1C902 ON metric (monitor_id)');
        $this->addSql('CREATE INDEX IDX_87D62EE33D2D0D4A ON metric (probe_id)');
        $this->addSql('CREATE INDEX IDX_87D62EE37B00651C ON metric (status)');
        $this->addSql('CREATE INDEX IDX_87D62EE37B00651C9CC65F6 ON metric (status, scheduled_at)');
        $this->addSql('CREATE INDEX IDX_87D62EE360118335 ON metric (executed_at)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87D62EE3D17F50A660118335 ON metric (uuid, executed_at)');
        $this->addSql('COMMENT ON COLUMN metric.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql(<<<'SQL'
            ALTER TABLE
              metric
            ADD
              CONSTRAINT FK_87D62EE34CE1C902 FOREIGN KEY (monitor_id) REFERENCES monitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              metric
            ADD
              CONSTRAINT FK_87D62EE33D2D0D4A FOREIGN KEY (probe_id) REFERENCES probe (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);

        // timescaledb
        $this->addSql("SELECT create_hypertable('metric', 'executed_at')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE metric DROP CONSTRAINT FK_87D62EE34CE1C902');
        $this->addSql('ALTER TABLE metric DROP CONSTRAINT FK_87D62EE33D2D0D4A');
        $this->addSql('DROP TABLE metric');
    }
}
