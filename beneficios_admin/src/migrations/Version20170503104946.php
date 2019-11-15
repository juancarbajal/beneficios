<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170503104946 extends AbstractMigration
{
    public static $description = "Alter BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa` 
ADD COLUMN `checkboxLogo` TINYINT(1) NULL DEFAULT 1 AFTER `Color_hover`;
ALTER TABLE `BNF_Empresa` 
ADD COLUMN `checkboxLogoBeneficio` TINYINT(1) NULL DEFAULT 1 AFTER `checkboxLogo`;

ALTER TABLE `BNF_Empresa` 
ADD COLUMN `checkboxMoney` TINYINT(1) NULL DEFAULT 1 AFTER `checkboxLogoBeneficio`;

ALTER TABLE `BNF2_Cupon_Puntos_Log` 
ADD COLUMN `comentario_uno` VARCHAR(20) NULL DEFAULT NULL AFTER `FechaCreacion`,
ADD COLUMN `comentario_dos` VARCHAR(20) NULL DEFAULT NULL AFTER `comentario_uno`;



ALTER TABLE `BNF_Empresa` 
ADD COLUMN `checkboxTotalPuntos` TINYINT(1) NULL DEFAULT 1 AFTER `checkboxMoney`;

ALTER TABLE `BNF_EmpresaClienteCliente` 
ADD COLUMN `Beneficiario` VARCHAR(100) NULL DEFAULT NULL AFTER `Eliminado`;

INSERT INTO `BNF_Configuraciones_Referidos` (`Campo`, `Atributo`, `Tipo`, `FechaCreacion`, `Eliminado`)
 VALUES ('4', '400', 'puntos', '2017-02-27 13:55:54', '0');


"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("

ALTER TABLE `BNF_Empresa` 
DROP COLUMN `checkboxLogo`;

ALTER TABLE `BNF_Empresa` 
DROP COLUMN `checkboxLogoBeneficio`;


ALTER TABLE `BNF_Empresa` 
DROP COLUMN `checkboxMoney`;

ALTER TABLE `BNF_Empresa` 
DROP COLUMN `checkboxTotalPuntos`;


ALTER TABLE `BNF2_Cupon_Puntos_Log` 
DROP COLUMN `comentario_uno`;
ALTER TABLE `BNF2_Cupon_Puntos_Log` 
DROP COLUMN `comentario_dos`;

ALTER TABLE `BNF_EmpresaClienteCliente` 
DROP COLUMN `Beneficiario`;

DELETE FROM `BNF_Configuraciones_Referidos` WHERE `id`='8';

");
    }
}
