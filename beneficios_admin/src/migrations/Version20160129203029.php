<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160129203029 extends AbstractMigration
{
    public static $description = "add index BNF_Cliente_id BNF_DM_Met_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_DM_Met_Cliente`
          ADD INDEX `BNF_Cliente_id` (`BNF_Cliente_id` ASC);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
