<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DmMetClientePreguntas extends Model
{
    protected $table = 'BNF_DM_Met_Cliente_Preguntas';

    public function getEdades($empresa_id = '', $array = array())
    {
        $query = "SELECT COUNT(*) AS Cantidad, DDE.id
            FROM BNF_DM_Met_Cliente_Preguntas AS DMC
            INNER JOIN BNF_DM_Dim_Edad AS DDE ON DDE.id = DMC.BNF_DM_Dim_Edad_id ";

        if ($empresa_id OR count($array) > 0) {
            $query .= "WHERE ";

            if ($empresa_id) {
                $query .= "DMC.BNF_DM_Dim_Empresa_id = ".$empresa_id;
            }

            if ($array != array()) {

                if ($empresa_id != '') {
                    $query .= " AND ";
                }
                $query .= "DMC.BNF_Cliente_id IN (". implode( ',', $array ) .")";
            }
        }

        $query .= " GROUP BY DDE.id";

        return \DB::select($query);
    }

    public function getHijos($empresa_id = '', $array = array())
    {
        $query = "SELECT
            SUM(IF(BNF_DM_Dim_Hijos_id = 1,1,0)) AS NoDef,
            SUM(IF(BNF_DM_Dim_Hijos_id = 2,1,0)) AS NoHijos,
            SUM(IF(BNF_DM_Dim_Hijos_id > 2,1,0)) AS SiHijos
            FROM BNF_DM_Met_Cliente_Preguntas AS DMC ";

        if ($empresa_id OR count($array) > 0) {
            $query .= "WHERE ";

            if ($empresa_id ) {
                $query .= "DMC.BNF_DM_Dim_Empresa_id = ".$empresa_id;
            }

            if ($array != array()) {
                if ($empresa_id != '') {
                    $query .= " AND ";
                }
                $query .= "DMC.BNF_Cliente_id IN (". implode( ',', $array ) .")";
            }
        }

        return \DB::select($query)[0];
    }

    public function getEstadoCivil($empresa_id = '', $array = array())
    {
        $query = "SELECT DDE.id,
            COUNT(*) AS Cantidad
            FROM BNF_DM_Met_Cliente_Preguntas AS DMC
            INNER JOIN BNF_DM_Dim_EstadoCivil AS DDE ON DMC.BNF_DM_Dim_EstadoCivil_id = DDE.id ";

        if ($empresa_id OR count($array) > 0) {
            $query .= "WHERE ";
            if ($empresa_id != '') {
                $query .= "DMC.BNF_DM_Dim_Empresa_id = ".$empresa_id;
            }

            if ($array != array()) {
                if ($empresa_id) {
                    $query .= " AND ";
                }

                $query .= "DMC.BNF_Cliente_id IN (". implode( ',', $array ) .")";
            }
        }

        $query .= " GROUP BY DDE.id";

        return \DB::select($query);
    }

    public function getPreguntaCampo($empresa_id, $campo, $array = array())
    {
        $query1 = "SELECT * FROM BNF_DM_Met_Cliente_Preguntas WHERE ".$campo ." IS NOT NULL ";

        if ( $empresa_id ) {
            $query1 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }
        if ($array != array()) {
            if ($empresa_id) {
                $query1 .= " ";
            }

            $query1 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $result1 = \DB::select($query1);


        $query2 = "SELECT * FROM BNF_DM_Met_Cliente_Preguntas WHERE ".$campo ." IS NULL ";

        if ( $empresa_id ) {
            $query2 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }
        if ($array != array()) {
            if ($empresa_id) {
                $query2 .= " ";
            }

            $query2 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $result2 = \DB::select($query2);

        return array(count($result1), count($result2));
    }

    public function getPreguntaGenero($empresa_id, $array = array())
    {
        $query1 = "SELECT * FROM BNF_DM_Met_Cliente_Preguntas WHERE Genero = 'H' ";

        if ( $empresa_id ) {
            $query1 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }
        if ($array != array()) {
            if ($empresa_id) {
                $query1 .= " ";
            }

            $query1 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $result1 = \DB::select($query1);


        $query2 = "SELECT * FROM BNF_DM_Met_Cliente_Preguntas WHERE Genero = 'M' ";

        if ( $empresa_id ) {
            $query2 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }
        if ($array != array()) {
            if ($empresa_id) {
                $query2 .= " ";
            }

            $query2 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $result2 = \DB::select($query2);


        $query3 = "SELECT * FROM BNF_DM_Met_Cliente_Preguntas WHERE Genero is NULL ";

        if ( $empresa_id ) {
            $query3 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }
        if ($array != array()) {
            if ($empresa_id) {
                $query3 .= " ";
            }

            $query3 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $result3 = \DB::select($query3);

        return array(count($result1), count($result2), count($result3));
    }

    public function getPreguntaDistrito($empresa_id, $campo, $array = array())
    {
        $query1 = "SELECT ".$campo. ", COUNT(*) AS Cantidad FROM BNF_DM_Met_Cliente_Preguntas WHERE ".
            $campo." IS NOT NULL ";

        if ( $empresa_id ) {
            $query1 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }
        if ($array != array()) {
            if ($empresa_id) {
                $query1 .= " ";
            }

            $query1 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $query1 .= " GROUP BY ".$campo;

        $result1 =  \DB::select($query1);


        $query2 = "SELECT * FROM BNF_DM_Met_Cliente_Preguntas WHERE ".$campo ." IS NULL ";

        if ( $empresa_id ) {
            $query2 .= "AND BNF_DM_Dim_Empresa_id = ". $empresa_id;
        }

        if ($array != array()) {
            if ($empresa_id) {
                $query2 .= " ";
            }

            $query2 .= "AND BNF_Cliente_id IN (". implode( ',', $array ) .")";
        }

        $result2 =  \DB::select($query2);

        return array($result1, count($result2));
    }

}
