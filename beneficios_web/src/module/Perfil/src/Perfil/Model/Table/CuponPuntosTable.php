<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 27/07/16
 * Time: 05:02 PM
 */

namespace Perfil\Model\Table;

use Perfil\Model\CuponPuntos;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponPuntosTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function get($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCupon($idOferta)
    {
        $id = (int)$idOferta;
        $rowset = $this->tableGateway->select(array('BNF2_Oferta_Puntos_id' => $id, 'EstadoCupon' => 'Creado'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCupon(CuponPuntos $cupon)
    {
        $data = $cupon->getArrayCopy();
        $data['FechaCreacion'] = date("Y-m-d H:i:s");
        $data['FechaGenerado'] = date("Y-m-d H:i:s");
        $this->tableGateway->insert($data);
        return $this->tableGateway->getLastInsertValue();
    }

    public function getHistorial($cliente_id, $empresa_id)
    {
        $sql = "SELECT 
                    BNF2_Cupon_Puntos.FechaGenerado AS FechaGenerado,
                    IFNULL(BNF2_Oferta_Puntos_Atributos.NombreAtributo,
                      BNF2_Oferta_Puntos.TituloCorto) AS TituloCorto,
                    IFNULL(BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico,
                      BNF2_Oferta_Puntos.PrecioVentaPublico) AS PrecioVentaPublico,
                    BNF2_Cupon_Puntos.PuntosUtilizados AS CantidadPuntos,
                    1 AS Descarga
                FROM
                    BNF2_Cupon_Puntos
                        INNER JOIN
                    BNF2_Oferta_Puntos ON BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id
                        LEFT JOIN
                    BNF2_Oferta_Puntos_Atributos ON BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id
                WHERE
                    BNF2_Cupon_Puntos.BNF_Cliente_id = '$cliente_id'
                        AND BNF2_Cupon_Puntos.BNF_Empresa_id = '$empresa_id'
                        AND BNF2_Cupon_Puntos.EstadoCupon != 'Creado' 
                UNION (SELECT 
                    FechaCreacion,
                    CASE Operacion
                        WHEN 'Asignar' THEN 'Puntos ganados'
                        WHEN 'Desactivar' THEN 'Puntos inactivos'
                        WHEN 'Reactivar' THEN 'Puntos reactivados'
                        WHEN 'Cancelar' THEN 'Puntos eliminados'
                    END AS TituloCorto,
                    0 AS PrecioVentaPublico,
                    Puntos,
                    0 AS 'Descarga'
                FROM
                    BNF2_Asignacion_Puntos_Estado_Log
                WHERE
                    BNF_Cliente_id = '$cliente_id'
                        AND Operacion NOT IN ('Redimir' , 'Aplicar', 'Sumar', 'Restar')
                ORDER BY FechaCreacion DESC) 
                UNION SELECT 
                    FechaCreacion,
                    CASE Operacion
                        WHEN 'Sumar' THEN 'Puntos sumados'
                        WHEN 'Restar' THEN 'Puntos restados'
                    END AS TituloCorto,
                    0 AS PrecioVentaPublico,
                    Puntos,
                    0 AS 'Descarga'
                FROM
                    BNF2_Asignacion_Puntos_Estado_Log
                WHERE
                    BNF_Cliente_id = '$cliente_id'
                        AND Operacion IN ('Sumar' , 'Restar')
                ORDER BY FechaGenerado DESC";
        $dbAdapter = $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($sql);
        $resultSet = $statement->execute();
        $resultSet->buffer();
        return $resultSet;
    }

    public function getDescargados($cliente_id, $empresa_id)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->join(
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
            array(
                'PrecioBeneficio',
                'TituloCorto',
                'FechaVigencia' => new Expression('IFNULL(BNF2_Oferta_Puntos.FechaVigencia,' .
                    ' (SELECT BNF2_Oferta_Puntos_Atributos.FechaVigencia FROM BNF2_Oferta_Puntos_Atributos' .
                    ' WHERE BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id) )')
            )
        );
        $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Cliente_id', $cliente_id);
        $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Empresa_id', $empresa_id);
        $select->where->notEqualTo('BNF2_Cupon_Puntos.EstadoCupon', 'Creado');
        $select->order('BNF2_Cupon_Puntos.FechaGenerado DESC');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getVigentes($cliente_id, $empresa_id)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->join(
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
            array(
                'PrecioBeneficio' => new Expression('IFNULL(BNF2_Oferta_Puntos_Atributos.PrecioBeneficio, BNF2_Oferta_Puntos.PrecioBeneficio)'),
                'TituloCorto' => new Expression('IFNULL(BNF2_Oferta_Puntos_Atributos.NombreAtributo, BNF2_Oferta_Puntos.TituloCorto)'),
                'PrecioVentaPublico' => new Expression('IFNULL(BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico, BNF2_Oferta_Puntos.PrecioVentaPublico)'),
                'FechaVigencia' => new Expression('IFNULL(BNF2_Oferta_Puntos.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia)')
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
            array(),
            'left'
        );
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF2_Cupon_Puntos.BNF2_Oferta_Empresa_id',
            array('Empresa' => 'NombreComercial')
        );
        $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Cliente_id', $cliente_id)
            ->and->equalTo('BNF2_Cupon_Puntos.BNF_Empresa_id', $empresa_id)
            ->and->equalTo('BNF2_Cupon_Puntos.EstadoCupon', 'Generado')
            ->and->literal('IFNULL( Timestampdiff(day,CURDATE(),BNF2_Oferta_Puntos.FechaVigencia) ,1) >= 0');
        $select->order('BNF2_Cupon_Puntos.FechaGenerado DESC');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getUtilizados($cliente_id, $empresa_id)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->join(
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
            array(
                'PrecioBeneficio' => new Expression(
                    'IFNULL(BNF2_Oferta_Puntos_Atributos.PrecioBeneficio, BNF2_Oferta_Puntos.PrecioBeneficio)'
                ),
                'TituloCorto' => new Expression(
                    'IFNULL(BNF2_Oferta_Puntos_Atributos.NombreAtributo, BNF2_Oferta_Puntos.TituloCorto)'
                ),
                'PrecioVentaPublico' => new Expression(
                    'IFNULL(BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico, BNF2_Oferta_Puntos.PrecioVentaPublico)'
                )
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
            array(),
            'left'
        );
        $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Cliente_id', $cliente_id);
        $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Empresa_id', $empresa_id);
        $select->where->isNotNull('FechaRedimido');
        $select->order('BNF2_Cupon_Puntos.FechaRedimido DESC');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function verifyLimit($idOferta, $idCliente, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');

        $select->where->equalTo('BNF2_Oferta_Puntos_id', $idOferta)
            ->and->equalTo('BNF_Cliente_id', $idCliente)
            ->and->literal('DATE(FechaGenerado) = CURDATE()');

        if ($atributo != null) {
            $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $atributo);
        }

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function updateCupon($empresa, $cliente, $idCupon, $categoria, $rubro, $idCorreo, $ptosUsados, $ptosDisponibles)
    {
        $data = array();
        $data['BNF_Empresa_id'] = $empresa;
        if ($cliente != '') {
            $data['BNF_Cliente_id'] = $cliente;
        }
        $data['EstadoCupon'] = 'Generado';
        $data['BNF_Rubro_id'] = $rubro;
        $data['PuntosUtilizados'] = $ptosUsados;
        $data['PuntosUsuario'] = $ptosDisponibles;
        $data['FechaGenerado'] = date("Y-m-d H:i:s");
        $data['BNF_Categoria_id'] = $categoria;
        if ($idCorreo != null) {
            $data['BNF_ClienteCorreo_id'] = $idCorreo;
        }

        return $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function setCuponCode($codigo, $idCupon)
    {
        $data = array();
        $data['CodigoCupon'] = $codigo;
        return $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function getCuponValid($idOferta, $atributo)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $idOferta);
        if ($atributo != null) {
            $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $atributo);
        }
        $select->where->equalTo('EstadoCupon', 'Creado');
        $select->limit(1);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}