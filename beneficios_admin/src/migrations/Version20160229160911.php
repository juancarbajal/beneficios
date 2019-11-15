<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160229160911 extends AbstractMigration
{
    public static $description = "Update BNF_Cupon BNF_OfertaFormCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 1;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 253;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 5 WHERE `id` = 755;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 756;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 2 WHERE `id` = 1539;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 2 WHERE `id` = 1777;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 2 WHERE `id` = 1780;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 2280;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 2 WHERE `id` = 3030;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 4 WHERE `id` = 3767;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 3771;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 3772;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 4 WHERE `id` = 3796;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 5524;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 5525;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 5621;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 5623;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 5820;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 8 WHERE `id` = 6062;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 6066;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 6277;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 8 WHERE `id` = 6278;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 4 WHERE `id` = 7566;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 11592;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 4 WHERE `id` = 11829;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 2 WHERE `id` = 13305;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 4 WHERE `id` = 17070;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 24600;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 24601;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 2 WHERE `id` = 26652;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 27686;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 27687;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 27688;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 27692;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 27694;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 28256;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 28257;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 28275;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 28276;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 3 WHERE `id` = 28279;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 6 WHERE `id` = 32878;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 4 WHERE `id` = 44506;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 6 WHERE `id` = 45264;
            UPDATE `BNF_Cupon` SET `BNF_Categoria_id` = 1 WHERE `id` = 45719;

            UPDATE `BNF_OfertaFormCliente` SET `BNF_Categoria_id` = 6 WHERE `id` = 56;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
