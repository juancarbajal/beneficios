<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 17/03/16
 * Time: 11:46 AM
 */

namespace Cron\Table;

use Zend\Db\Adapter\Adapter;

class CuponTable
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function updateClienteCorreo()
    {
        $sql = "UPDATE `BNF_ClienteCorreo` SET `FechaActualizacion`= `FechaCreacion` WHERE FechaActualizacion IS NULL";
        $statement = $this->adapter->createStatement($sql);
        return $statement->execute();
    }

    public function updateCupon($lista_clientes)
    {
        $result = '';
        foreach ($lista_clientes as $data) {
            $sql = "UPDATE `BNF_Cupon` AS c SET c.`BNF_ClienteCorreo_id` =
                      (SELECT `id` FROM BNF_ClienteCorreo as cc WHERE cc.`BNF_Cliente_id` = " . $data->BNF_Cliente_id
                            . " ORDER BY FechaActualizacion DESC LIMIT 1)
                WHERE c.`BNF_Cliente_id` = " . $data->BNF_Cliente_id;
            $statement = $this->adapter->createStatement($sql);
            $result += $statement->execute()->count();
        }
        return $result;
    }
}
