<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190753 extends AbstractMigration
{
    public static $description = "ADD field Logo_sitio, Color BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Empresa` ADD `Logo_sitio` VARCHAR(45) NULL AFTER `Cliente`;
                       ALTER TABLE `BNF_Empresa` ADD `Color` VARCHAR(7) NULL AFTER `Logo_sitio`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `BNF_Empresa`
                          DROP `Logo_sitio`,
                          DROP `Color`;");
    }
}
