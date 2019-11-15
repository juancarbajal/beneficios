<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170220163232 extends AbstractMigration
{
    public static $description = "Migration description";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF4_LandingReferidos` 
                ADD CONSTRAINT `fk_BNF4_LandingReferidos_1`
                  FOREIGN KEY (`cliente_id`)
                  REFERENCES `BNF4_LandingClientesColaboradores` (`id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
