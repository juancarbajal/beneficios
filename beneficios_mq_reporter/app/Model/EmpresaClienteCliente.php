<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmpresaClienteCliente extends Model
{
    protected $table = 'BNF_EmpresaClienteCliente';

    public function getClientesXEmpresa($id_empresa, $fecha_inicio, $fecha_fin)
    {
        $query = "SELECT ECC.* FROM BNF_EmpresaClienteCliente AS ECC " .
            "INNER JOIN BNF_Cliente AS C ON C.id = ECC.BNF_Cliente_id WHERE " .
            "ECC.Estado = 'Activo' AND " .
            "C.FechaCreacion  BETWEEN '$fecha_inicio' AND ADDDATE('$fecha_fin', INTERVAL 1 DAY)";

        if ($id_empresa) {
            $query .= " AND ECC.BNF_Empresa_id = " . $id_empresa;
        }

        $result = \DB::select($query);

        if (!$result) {
            return false;
        }
        return count($result);
    }

    public function getEmpresaName($id_empresa)
    {
        $query = "SELECT NombreComercial FROM BNF_Empresa WHERE id = " . $id_empresa;

        $result = \DB::select($query);

        if (!$result) {
            return false;
        }
        return $result;
    }
}
