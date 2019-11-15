<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150155 extends AbstractMigration
{
    public static $description = "Seed BNF_OfertaRubro update";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "SET SQL_SAFE_UPDATES = 0;

            UPDATE BNF_OfertaRubro 
            SET 
                BNF_Rubro_id = 2
            WHERE
                id IN (SELECT 
                        id
                    FROM
                        (SELECT 
                            id
                        FROM
                            BNF_OfertaRubro
                        WHERE
                            BNF_Rubro_id = 7) AS RubroProducto);
                            
            SET SQL_SAFE_UPDATES = 1;
            "
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
