<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/01/16
 * Time: 03:01 PM
 */

namespace Cron\Table;

use Zend\Db\Adapter\Adapter;

class DimEmpresaTable
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function delete()
    {
        $sql_str = "DELETE FROM BNF_DM_Dim_Empresa";
        $statement = $this->adapter->createStatement($sql_str);
        return $statement->execute();
    }

    public function insert()
    {
        $sql_str = "INSERT INTO `BNF_DM_Dim_Empresa` SELECT e.id, e.NombreComercial FROM `BNF_Empresa` e
                  INNER JOIN `BNF_EmpresaTipoEmpresa` te ON e.id = te.BNF_Empresa_id AND te.BNF_TipoEmpresa_id = 2";
        $statement = $this->adapter->createStatement($sql_str);
        return  $statement->execute();
    }
}