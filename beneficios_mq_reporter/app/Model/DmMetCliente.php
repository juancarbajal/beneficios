<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DmMetCliente extends Model
{
    protected $table = 'BNF_DM_Met_Cliente';

    public function getDescargasOrRedimidos($empresa_id, $fechaInicio, $fechaFin)
    {
        $query = "SELECT SUM(IF(D.EstadoCupon = 'Caducado',1,0) +
                IF(D.EstadoCupon = 'Generado',1,0) +
                IF(D.TipoOferta = 'Lead',1,0)) AS Descargas,
            SUM(IF(D.EstadoCupon = 'Redimido',1,0)) AS Redimidos
            FROM BNF_DM_Met_Cliente AS D
            WHERE ";

        $query .= " (( D.FechaGenerado BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)) " .
            "OR (D.FechaRedimido BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)))";

        if ($empresa_id) {
            $query .= " AND  D.BNF_DM_Dim_Empresa_id = " . $empresa_id;
        }

        return \DB::select($query)[0];
    }

    public function getListClientesId($fechaInicio, $fechaFin)
    {
        $query = "SELECT D.BNF_Cliente_id  FROM BNF_DM_Met_Cliente AS D " .
            "WHERE D.BNF_Cliente_id IS NOT NULL AND D.FechaGenerado BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY) " .
            "GROUP BY D.BNF_Cliente_id";

        return \DB::select($query);
    }

    public function getDataCliente($array, $list_categorias_ids, $empresa_id, $fechaInicio, $fechaFin)
    {
        $query = "SELECT DiasUltimoLogin, " .
            "SUM(IF(EstadoCupon = 'Caducado',1,0) + " .
            "IF(EstadoCupon = 'Generado',1,0) + " .
            "IF(TipoOferta = 'Lead',1,0)) " .
            "AS Descargas," .
            "SUM(IF(EstadoCupon = 'Redimido',1,0))" .
            "AS Redimidos," .
            "SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Caducado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'bus' AND `TipoOferta` = 'Lead',1,0) + " .
            "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Caducado',1,0) + " .
            "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = '0' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatBus," .
            "SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Caducado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'com' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatCom," .
            "SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Caducado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'cam' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatCam," .
            "SUM(IF(`BNF_Categoria_id` = 'tei' AND `EstadoCupon` = 'Caducado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'tie' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatTie," .
            "SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Redimido',1,0) + " .
            "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatBus," .
            "SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatCom," .
            "SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatCam," .
            "SUM(IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatTie," .
            "D.Edad, D.Genero, D.nombres AS Nombre, D.apellidos as Apellido, D.distrito_vive, D.distrito_trabaja ";


        foreach ($list_categorias_ids as $data) {
            $query .= ", SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Caducado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$data->id' AND `TipoOferta` = 'Lead',1,0)) AS DesCat" . $data->id;

            $query .= ", SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Redimido',1,0)) " .
                "AS RedCat" . $data->id;
        }

        $query .= ",C.FechaCreacion, C.NumeroDocumento, E.estado, IF(H.hijos = -1, 'no-definido', H.hijos) as hijos ";

        $query .= "FROM BNF_DM_Met_Cliente AS D " .
            "INNER JOIN BNF_Cliente AS C ON D.BNF_Cliente_id = C.id " .
            "INNER JOIN BNF_DM_Dim_EstadoCivil AS E ON D.BNF_DM_Dim_EstadoCivil_id = E.id " .
            "INNER JOIN BNF_DM_Dim_Hijos AS H ON D.BNF_DM_Dim_Hijos_id = H.id ";

        $query .= "WHERE C.NumeroDocumento IN  ('" . implode("','", $array) . "') AND " .
            "(( D.FechaGenerado BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)) " .
            "OR (D.FechaRedimido BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)))";

        if ($empresa_id) {
            $query .= " AND D.BNF_DM_Dim_Empresa_id =" . $empresa_id;
        }

        $query .= " GROUP BY C.id";

        return \DB::select($query);
    }

    public function getDataDescargasRedimidos($empresa_id, $list_categorias_ids, $fechaInicio, $fechaFin)
    {
        $query = "SELECT SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Caducado',1,0) +" .
            "IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'bus' AND `TipoOferta` = 'Lead',1,0) + " .
            "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Caducado',1,0) +" .
            "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = '0' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatBus," .
            "SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Caducado',1,0) +" .
            "IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'com' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatCom," .
            "SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Caducado',1,0) +" .
            "IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'cam' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatCam," .
            "SUM(IF(`BNF_Categoria_id` = 'tei' AND `EstadoCupon` = 'Caducado',1,0) +" .
            "IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Generado',1,0) + " .
            "IF(`BNF_Categoria_id` = 'tie' AND `TipoOferta` = 'Lead',1,0)) " .
            "AS DesCatTie," .
            "SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Redimido',1,0) +" .
            "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatBus," .
            "SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatCom," .
            "SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatCam," .
            "SUM(IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Redimido',1,0)) " .
            "AS RedCatTie";

        foreach ($list_categorias_ids as $data) {
            $query .= ", SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                "IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$data->id' AND `TipoOferta` = 'Lead',1,0)) AS DesCat" . $data->id;

            $query .= ",SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Redimido',1,0)) " .
                "AS RedCat" . $data->id;
        }

        $query .= " FROM BNF_DM_Met_Cliente AS D " .
            "WHERE (( D.FechaGenerado BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)) " .
            "OR (D.FechaRedimido BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)))";

        if ($empresa_id) {
            $query .= " AND D.BNF_DM_Dim_Empresa_id =" . $empresa_id;
        }

        $data = \DB::select($query);
        return isset($data[0]) ? $data[0] : [];
    }

    public function getDescargaRubros($lista_rubros, $empresa_id, $fechaInicio, $fechaFin)
    {
        $colums = "";
        for ($i = 0; $i < count($lista_rubros); $i++) {
            $colums .= "SUM(IF(`BNF_Rubro_id` = '" . $lista_rubros[$i]->id . "',1,0)) AS Rubro" . $lista_rubros[$i]->id;
            if ($i < count($lista_rubros) - 1) $colums .= ",";
        }

        $query = "SELECT " . $colums . " FROM BNF_DM_Met_Cliente  WHERE " .
            "FechaRedimido IS NULL AND" .
            " (( FechaGenerado BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)) " .
            "OR (FechaRedimido BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)))";

        if ($empresa_id) {
            $query .= " AND BNF_DM_Dim_Empresa_id =" . $empresa_id;
        }

        return \DB::select($query);
    }

    public function getDescargasCategoria($empresa_id, $fechaInicio, $fechaFin, $categoria_id)
    {
        $query = "SELECT ";

        if ($categoria_id == 'bus') {
            $query .= "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                "IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$categoria_id' AND `TipoOferta` = 'Lead',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Caducado',1,0) +" .
                "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `TipoOferta` = 'Lead',1,0)) AS Descargas ";
        } else {
            $query .= "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                "IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$categoria_id' AND `TipoOferta` = 'Lead',1,0)) AS Descargas ";
        }

        $query .= "FROM BNF_DM_Met_Cliente WHERE ( FechaGenerado BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)) ";

        if ($empresa_id) {
            $query .= " AND BNF_DM_Dim_Empresa_id = " . $empresa_id;
        }

        return \DB::select($query)[0];
    }

    public function getDescargasRedimidos($empresa_id, $fechaInicio, $fechaFin, $categoria_id)
    {
        $categoria_id = ($categoria_id == 0) ? 'bus' : $categoria_id;

        $query = "SELECT ";

        if ($categoria_id == 'bus') {
            $query .= "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Redimido',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Redimido',1,0)) AS Redimidos ";
        } else {
            $query .= "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Redimido',1,0)) AS Redimidos ";
        }

        $query .= "FROM BNF_DM_Met_Cliente WHERE ( FechaRedimido BETWEEN '" . $fechaInicio .
            "' AND ADDDATE('" . $fechaFin . "', INTERVAL 1 DAY)) ";


        if ($empresa_id) {
            $query .= " AND BNF_DM_Dim_Empresa_id = " . $empresa_id;
        }

        return \DB::select($query)[0];
    }

    public function getClientes($fechaInicio, $fechaFin, $empresa, $rubros)
    {
        $query = "SELECT T.Nombre, C.NumeroDocumento, DC.celular, P.Pregunta03 AS FechaNacimiento, " .
            "DC.BNF_DM_Dim_EstadoCivil_id, DC.nivel_estudios, DC.Genero, DC.BNF_DM_Dim_Hijos_id, " .
            "DC.FechaGenerado, DC.nombres, DC.apellidos , DC.distrito_vive , DC.distrito_trabaja, " .
            "(SELECT DISTINCT GROUP_CONCAT(distinct TRIM(Correo) SEPARATOR ', ') FROM BNF_ClienteCorreo " .
            "WHERE Correo IS NOT NULL AND TRIM(Correo) != '' AND BNF_Cliente_id = C.id ) AS Correo";
        foreach ($rubros as $key => $value) {
            $query .= ", (SELECT COUNT(*) FROM BNF_DM_Met_Cliente " .
                "WHERE BNF_Cliente_id = C.id AND BNF_Rubro_id = $key " .
                "AND FechaGenerado BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY) ";
            if ((int)$empresa) {
                $query .= "AND BNF_DM_Dim_Empresa_id = $empresa";
            }
            $query .= ") AS '$value' ";
        }
        $query .= "FROM BNF_Cliente AS C " .
            "INNER JOIN BNF_DM_Met_Cliente AS DC ON C.id = DC.BNF_Cliente_id " .
            "INNER JOIN BNF_TipoDocumento AS T ON T.id = C.BNF_TipoDocumento_id " .
            "INNER JOIN BNF_Preguntas AS P ON P.BNF_Cliente_id = C.id " .
            "LEFT JOIN (SELECT BNF_Cliente_id, MAX(FechaGenerado) FechaGenerado " .
            "FROM BNF_DM_Met_Cliente GROUP BY BNF_Cliente_id) r ON DC.FechaGenerado = r.FechaGenerado " .
            "WHERE DC.FechaGenerado BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY)";

        if ((int)$empresa) {
            $query .= " AND BNF_DM_Dim_Empresa_id = $empresa";
        }

        $query .= " GROUP BY C.id";

        return \DB::select($query);
    }

    public function prueba($fechaInicio, $fechaFin)
    {
        $query = "SELECT C.NumeroDocumento, D.BNF_DM_Dim_Empresa_id AS Empresa, D.FechaGenerado, D.BNF_Categoria_id
                  FROM BNF_DM_Met_Cliente AS D 
                  INNER JOIN BNF_Cliente AS C ON D.BNF_Cliente_id = C.id 
                  WHERE D.FechaGenerado BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY) 
                  OR D.FechaRedimido BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY) 
                  GROUP BY C.id";

        return \DB::select($query);
    }

    public function getCorreos($fechaInicio, $fechaFin, $empresa, $rubros)
    {
        $query = "SELECT  " .
            "DC.FechaGenerado, " .
            "DC.Correo ";
        foreach ($rubros as $key => $value) {
            $query .= ", (SELECT COUNT(*) FROM BNF_DM_Met_Cliente " .
                "WHERE Correo = DC.Correo AND BNF_Rubro_id = $key " .
                "AND FechaGenerado BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY) ";
            if ((int)$empresa) {
                $query .= "AND BNF_DM_Dim_Empresa_id = $empresa";
            }
            $query .= ") AS '$value' ";
        }
        $query .= "FROM BNF_DM_Met_Cliente AS DC " .
            "WHERE DC.FechaGenerado BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY)";

        if ((int)$empresa) {
            $query .= " AND DC.BNF_DM_Dim_Empresa_id = $empresa";
        }

        $query .= " GROUP BY DC.Correo";

        return \DB::select($query);
    }
}
