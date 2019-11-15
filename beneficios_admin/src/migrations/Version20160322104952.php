<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160322104952 extends AbstractMigration
{
    public static $description = "add fields [Correo , BNF_Rubro_id] BNF_DM_Met_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_DM_Met_Cliente` ADD `BNF_Rubro_id` VARCHAR(255) NULL AFTER `BNF_Categoria_id`,
              ADD `Correo` VARCHAR(255) NULL AFTER `BNF_Rubro_id`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE `BNF_DM_Met_Cliente` DROP `BNF_Rubro_id`, DROP `Correo`;");
    }
}
