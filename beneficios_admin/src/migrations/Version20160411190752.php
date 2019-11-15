<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190752 extends AbstractMigration
{
    public static $description = "ADD field SubDominio BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Empresa` ADD `SubDominio` VARCHAR(45) NULL AFTER `SitioWeb`,
                        ADD INDEX (`SubDominio`);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `BNF_Empresa` DROP `SubDominio`;");
    }
}
