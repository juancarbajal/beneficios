<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190759 extends AbstractMigration
{
    public static $description = "ADD fields Color_menu,Color_hover BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Empresa` ADD `Color_menu` VARCHAR(7) NULL AFTER `Color`;
                       ALTER TABLE `BNF_Empresa` ADD `Color_hover` VARCHAR(7) NULL AFTER `Color_menu`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `BNF_Empresa`  DROP `Color_menu`,  DROP `Color_hover`;");
    }
}
