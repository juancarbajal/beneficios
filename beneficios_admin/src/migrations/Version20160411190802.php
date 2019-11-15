<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190802 extends AbstractMigration
{
    public static $description = "ADD field BNF_Empresa_id BNF_BannersTienda tabñe";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_BannersTienda`
                        ADD COLUMN `BNF_Empresa_id` INT NULL DEFAULT NULL AFTER `id`,
                        ADD INDEX `fk_BNF_BannersTienda_2_idx` (`BNF_Empresa_id` ASC);
                        ALTER TABLE `BNF_BannersTienda`
                        ADD CONSTRAINT `fk_BNF_BannersTienda_2`
                          FOREIGN KEY (`BNF_Empresa_id`)
                          REFERENCES `BNF_Empresa` (`id`)
                          ON DELETE NO ACTION
                          ON UPDATE NO ACTION;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE BNF_BannersTienda DROP FOREIGN KEY fk_BNF_BannersTienda_2;
                        ALTER TABLE `BNF_BannersTienda` DROP `BNF_Empresa_id`;");
    }
}
