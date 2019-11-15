<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190805 extends AbstractMigration
{
    public static $description = "ADD field BNF_Empresa_id BNF_BannersCampanias tabñe";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_BannersCampanias`
                        ADD COLUMN `BNF_Empresa_id` INT NULL DEFAULT NULL AFTER `id`,
                        ADD INDEX `fk_BNF_Empresa_1_idx` (`BNF_Empresa_id` ASC);
                        ALTER TABLE `BNF_BannersCampanias`
                        ADD CONSTRAINT `fk_BNF_Empresa_1`
                          FOREIGN KEY (`BNF_Empresa_id`)
                          REFERENCES `BNF_Empresa` (`id`)
                          ON DELETE NO ACTION
                          ON UPDATE NO ACTION;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE BNF_BannersCampanias DROP FOREIGN KEY fk_BNF_Empresa_1;
                        ALTER TABLE `BNF_BannersCampanias` DROP `BNF_Empresa_id`;");
    }
}
