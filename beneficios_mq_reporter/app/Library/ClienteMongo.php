<?php
namespace App\Library;


class ClienteMongo
{
    protected $analytics;
    protected $db;

    public function __construct()
    {
        $connection = new \MongoClient();
        $name_db_mongo = env('DATABASE_MONGO');
        $db = $connection->selectDB($name_db_mongo);
        $this->db = $db;
        $this->analytics = $db->selectCollection('analytics');
    }


    public function getAnalytics()
    {
        return $this->analytics;
    }


    public function get_session_category($fecha_inicio, $fecha_fin, $id_empresa)
    {
        $query = array();
        if ($id_empresa) {
            $query['id_empresa'] = (int)$id_empresa;
        }

        $query['fecha_registro'] = array(
            '$gte' => $fecha_inicio . ' 00:00:00',
            '$lte' => $fecha_fin . ' 23:59:59'
        );
        $result = $this->analytics->aggregate(
            array(
                '$match' => $query
            ),
            array(
                '$group' => array(
                    '_id' => '$slug',
                    'count_e_u' => array(
                        '$sum' => '$e_u'
                    ),
                    'count_e_n' => array(
                        '$sum' => '$e_n'
                    )
                )
            ),
            array('$sort' => array("_id" => 1))
        );

        if (isset($result['result'])) {
            $data = $result['result'];
        } else {
            $data = array();
        }
        return $data;
    }

    public function get_dnis($fecha_inicio, $fecha_fin, $id_empresa)
    {
        $query = array();
        if ($id_empresa) {
            $query['id_empresa'] = (int)$id_empresa;
        }

        $query['fecha_registro'] = array(
            '$gte' => $fecha_inicio . ' 00:00:00',
            '$lte' => $fecha_fin . ' 23:59:59'
        );

        $result = $this->analytics->aggregate(
            array(
                '$match' => $query
            ),
            array(
                '$group' => array(
                    '_id' => '$dni',
                    'count_e_u' => array(
                        '$sum' => '$e_u'
                    ),
                    'count_e_n' => array(
                        '$sum' => '$e_n'
                    )
                )
            ),
            array('$sort' => array("_id" => 1))
        );

        if (isset($result['result'])) {
            $data = $result['result'];
        } else {
            $data = array();
        }
        return $data;
    }

    public function setAnalytics($datos)
    {
        return array(
            'id_cookie' => '',
            'id_empresa' => (int)$datos[3],
            'dni' => $datos[4],
            'slug' => $datos[5],
            'sub_dominio' => '',
            'ip' => '',
            'e_n' => (int)$datos[6],
            'e_u' => (int)$datos[7],
            'fecha_registro' => $datos[9],
            'navegador' => $datos[2],
            's_o' => $datos[1],
            'dispositivo' => $datos[0]
        );
    }

    public function getDnisUnicos($fecha_inicio, $fecha_fin, $id_empresa)
    {
        $query = array();
        if ($id_empresa) {
            $query['id_empresa'] = (int)$id_empresa;
        }

        $query['fecha_registro'] = array(
            '$gte' => $fecha_inicio . ' 00:00:00',
            '$lte' => $fecha_fin . ' 23:59:59'
        );

        $result = $this->analytics->aggregate(
            array(
                '$match' => $query
            ),
            array(
                '$group' => array(
                    '_id' => '$dni',
                )
            )
        );

        if (isset($result['result'])) {
            $data = $result['result'];
        } else {
            $data = array();
        }
        return count($data);
    }

    public function getdnis($fecha_inicio, $fecha_fin)
    {
        $query = array();

        $query['fecha_registro'] = array(
            '$gte' => $fecha_inicio . ' 00:00:00',
            '$lte' => $fecha_fin . ' 23:59:59'
        );

        $result = $this->analytics->distinct('dni');

        return $result;
    }

    public function get_collections($fecha, $subDominio, $limit)
    {
        $query = array();
        $query['sub_dominio'] = $subDominio;

        $query['fecha_registro'] = array(
            '$gte' => $fecha . ' 00:00:00',
            '$lte' => $fecha . ' 23:59:59'
        );

        $result = $this->analytics->find($query)->limit((int)$limit);

        return $result;
    }

    public function set_fechaRegistro($id, $fecha)
    {
        $result = $this->analytics->update(
            array('_id' => $id),
            array('$set' => array('fecha_registro' => $fecha . ' 00:00:00'))
        );
        
        return $result;
    }

    public function getRegistrosUnicos($fecha_inicio, $fecha_fin, $id_empresa)
    {
        $query = array();
        if ($id_empresa) {
            $query['id_empresa'] = (int)$id_empresa;
        }

        $query['fecha_registro'] = array(
            '$gte' => $fecha_inicio . ' 00:00:00',
            '$lte' => $fecha_fin . ' 23:59:59'
        );

        $result = $this->analytics->distinct(
            "dni",
            $query
        );

        if (isset($result)) {
            $data = $result;
        } else {
            $data = array();
        }

        return count($data);
    }
}