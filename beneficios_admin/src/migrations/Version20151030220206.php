<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151030220206 extends AbstractMigration
{
    public static $description = "Alter BNF_Configuraciones table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Configuraciones`
             CHANGE COLUMN `Atributo` `Atributo` LONGTEXT NOT NULL COMMENT '' ;

             INSERT INTO `BNF_Configuraciones`
	VALUES (NULL,'termcondiciones','Los datos ingresados son legítimos y correctos. Conozco el uso que se le dará a los datos que he proporcionado a Beneficios.pe. Entiendo que los Proveedores son los únicos responsables por cualquier inconveniente con los bienes y servicios ofrecidos a través de la Página Web. Sé cual es la información y datos personales requeridos para poder acceder a los bienes y servicios ofrecidos a través de la Página Web. He autorizado a mi Empleador a brindar o he brindado directamente a Belia mis datos personalespara poder ingresar a la Pagina Web.\r\n',NULL,NULL),
    (NULL,'mensajeproceso','Con estos datos trabajaremos para que disfrutes de una mejor experiencia.',NULL,NULL),
    (NULL,'mensajeerror','Supero la descargas permitidas del día',NULL,NULL),
    (NULL,'parf_contact_pdf','En caso de consulta o inconvenientes comunicarse con:',NULL,NULL);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
