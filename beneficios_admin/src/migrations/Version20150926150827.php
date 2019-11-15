<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926150827 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoBeneficio";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_TipoBeneficio` (`id`, `NombreBeneficio`) VALUES
            (NULL, 'Descuento porcentual'),
            (NULL, 'Descuento en efectivo'),
            (NULL, 'NxN'),
            (NULL, 'Otros');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
