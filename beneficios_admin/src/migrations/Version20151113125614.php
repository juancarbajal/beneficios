<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151113125614 extends AbstractMigration
{
    public static $description = "Alter BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cupon`
              ADD COLUMN `CodigoCupon` VARCHAR(45) NULL COMMENT '' AFTER `BNF_Cliente_id`,
              ADD UNIQUE INDEX `CodigoCupon_UNIQUE` (`CodigoCupon` ASC)  COMMENT '';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
