<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Wallabag\CoreBundle\Doctrine\WallabagMigration;

/**
 * Force utf8mb4 on craue_config_setting.
 */
final class Version20181029131313 extends WallabagMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ' . $this->getTable('craue_config_setting') . ' RENAME TO ' . $this->getTable('internal_setting') . ';');

        if ('mysql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql('ALTER TABLE ' . $this->getTable('internal_setting') . ' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');

            $this->addSql('ALTER TABLE ' . $this->getTable('internal_setting') . ' CHANGE `name` `name` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
            $this->addSql('ALTER TABLE ' . $this->getTable('internal_setting') . ' CHANGE `section` `section` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ' . $this->getTable('internal_setting') . ' RENAME TO ' . $this->getTable('craue_config_setting') . ';');

        if ('mysql' === $this->connection->getDatabasePlatform()->getName()) {
            $this->addSql('ALTER TABLE ' . $this->getTable('craue_config_setting') . ' CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;');

            $this->addSql('ALTER TABLE ' . $this->getTable('craue_config_setting') . ' CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
            $this->addSql('ALTER TABLE ' . $this->getTable('craue_config_setting') . ' CHANGE `section` `section` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
        }
    }
}
