<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525195029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD show_wind TINYINT(1) DEFAULT 0 NOT NULL, ADD show_uv TINYINT(1) DEFAULT 0 NOT NULL, ADD show_sun_cycle TINYINT(1) DEFAULT 0 NOT NULL, ADD show_visibility TINYINT(1) DEFAULT 0 NOT NULL, DROP temp_unit, DROP wind_unit, CHANGE position position INT NOT NULL, CHANGE show_humidity show_humidity TINYINT(1) DEFAULT 0 NOT NULL, CHANGE show_pressure show_pressure TINYINT(1) DEFAULT 0 NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD temp_unit VARCHAR(255) DEFAULT NULL, ADD wind_unit VARCHAR(255) DEFAULT NULL, DROP show_wind, DROP show_uv, DROP show_sun_cycle, DROP show_visibility, CHANGE position position VARCHAR(255) NOT NULL, CHANGE show_humidity show_humidity TINYINT(1) NOT NULL, CHANGE show_pressure show_pressure TINYINT(1) NOT NULL
        SQL);
    }
}
