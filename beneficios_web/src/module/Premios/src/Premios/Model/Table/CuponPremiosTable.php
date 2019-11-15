<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 27/07/16
 * Time: 05:02 PM
 */

namespace Premios\Model\Table;

use Premios\Model\CuponPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponPremiosTable
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
        $rowset = $this->tableGateway->select(array('BNF3_Oferta_Premios_id' => $id, 'EstadoCupon' => 'Creado'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCupon(CuponPremios $cupon)
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
                    BNF3_Cupon_Premios.FechaGenerado AS FechaGenerado,
                    IFNULL(BNF3_Oferta_Premios_Atributos.NombreAtributo,
                      BNF3_Oferta_Premios.TituloCorto) AS TituloCorto,
                    IFNULL(BNF3_Oferta_Premios_Atributos.PrecioVentaPublico,
                      BNF3_Oferta_Premios.PrecioVentaPublico) AS PrecioVentaPublico,
                    BNF3_Cupon_Premios.PremiosUtilizados AS CantidadPremios,
                    1 AS Descarga
                FROM
                    BNF3_Cupon_Premios
                        INNER JOIN
                    BNF3_Oferta_Premios ON BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id
                        LEFT JOIN
                    BNF3_Oferta_Premios_Atributos ON BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id
                WHERE
                    BNF3_Cupon_Premios.BNF_Cliente_id = '$cliente_id'
                        AND BNF3_Cupon_Premios.BNF_Empresa_id = '$empresa_id'
                        AND BNF3_Cupon_Premios.EstadoCupon != 'Creado' 
                UNION (SELECT 
                    FechaCreacion,
                    CASE Operacion
                        WHEN 'Asignar' THEN 'Premios ganados'
                        WHEN 'Desactivar' THEN 'Premios inactivos'
                        WHEN 'Reactivar' THEN 'Premios reactivados'
                        WHEN 'Cancelar' THEN 'Premios eliminados'
                    END AS TituloCorto,
                    0 AS PrecioVentaPublico,
                    Premios,
                    0 AS 'Descarga'
                FROM
                    BNF3_Asignacion_Premios_Estado_Log
                WHERE
                    BNF_Cliente_id = '$cliente_id'
                        AND Operacion NOT IN ('Redimir' , 'Aplicar', 'Sumar', 'Restar')
                ORDER BY FechaCreacion DESC) 
                UNION SELECT 
                    FechaCreacion,
                    CASE Operacion
                        WHEN 'Sumar' THEN 'Premios sumados'
                        WHEN 'Restar' THEN 'Premios restados'
                    END AS TituloCorto,
                    0 AS PrecioVentaPublico,
                    Premios,
                    0 AS 'Descarga'
                FROM
                    BNF3_Asignacion_Premios_Estado_Log
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
        $select->from('BNF3_Cupon_Premios');
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id',
            array(
                'PrecioBeneficio',
                'TituloCorto',
                'FechaVigencia' => new Expression('IFNULL(BNF3_Oferta_Premios.FechaVigencia,' .
                    ' (SELECT BNF3_Oferta_Premios_Atributos.FechaVigencia FROM BNF3_Oferta_Premios_Atributos' .
                    ' WHERE BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id) )')
            )
        );
        $select->where->equalTo('BNF3_Cupon_Premios.BNF_Cliente_id', $cliente_id);
        $select->where->equalTo('BNF3_Cupon_Premios.BNF_Empresa_id', $empresa_id);
        $select->where->notEqualTo('BNF3_Cupon_Premios.EstadoCupon', 'Creado');
        $select->order('BNF3_Cupon_Premios.FechaGenerado DESC');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getVigentes($cliente_id, $empresa_id)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id',
            array(
                'PrecioBeneficio' => new Expression('IFNULL(BNF3_Oferta_Premios_Atributos.PrecioBeneficio, BNF3_Oferta_Premios.PrecioBeneficio)'),
                'TituloCorto' => new Expression('IFNULL(BNF3_Oferta_Premios_Atributos.NombreAtributo, BNF3_Oferta_Premios.TituloCorto)'),
                'PrecioVentaPublico' => new Expression('IFNULL(BNF3_Oferta_Premios_Atributos.PrecioVentaPublico, BNF3_Oferta_Premios.PrecioVentaPublico)'),
                'FechaVigencia' => new Expression('IFNULL(BNF3_Oferta_Premios.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia)')
            )
        );
        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id',
            array(),
            'left'
        );
        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF3_Cupon_Premios.BNF3_Oferta_Empresa_id',
            array('Empresa' => 'NombreComercial')
        );
        $select->where->equalTo('BNF3_Cupon_Premios.BNF_Cliente_id', $cliente_id)
            ->and->equalTo('BNF3_Cupon_Premios.BNF_Empresa_id', $empresa_id)
            ->and->equalTo('BNF3_Cupon_Premios.EstadoCupon', 'Generado')
            ->and->literal('IFNULL( Timestampdiff(day,CURDATE(),BNF3_Oferta_Premios.FechaVigencia) ,1) >= 0');
        $select->order('BNF3_Cupon_Premios.FechaGenerado DESC');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getUtilizados($cliente_id, $empresa_id)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id',
            array(
                'PrecioBeneficio' => new Expression(
                    'IFNULL(BNF3_Oferta_Premios_Atributos.PrecioBeneficio, BNF3_Oferta_Premios.PrecioBeneficio)'
                ),
                'TituloCorto' => new Expression(
                    'IFNULL(BNF3_Oferta_Premios_Atributos.NombreAtributo, BNF3_Oferta_Premios.TituloCorto)'
                ),
                'PrecioVentaPublico' => new Expression(
                    'IFNULL(BNF3_Oferta_Premios_Atributos.PrecioVentaPublico, BNF3_Oferta_Premios.PrecioVentaPublico)'
                )
            )
        );
        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id',
            array(),
            'left'
        );
        $select->where->equalTo('BNF3_Cupon_Premios.BNF_Cliente_id', $cliente_id);
        $select->where->equalTo('BNF3_Cupon_Premios.BNF_Empresa_id', $empresa_id);
        $select->where->isNotNull('FechaRedimido');
        $select->order('BNF3_Cupon_Premios.FechaRedimido DESC');

        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function verifyLimit($idOferta, $idCliente, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');

        $select->where->equalTo('BNF3_Oferta_Premios_id', $idOferta)
            ->and->equalTo('BNF_Cliente_id', $idCliente)
            ->and->literal('DATE(FechaGenerado) = CURDATE()');

        if ($atributo != null) {
            $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $atributo);
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
        $data['PremiosUtilizados'] = $ptosUsados;
        $data['PremiosUsuario'] = $ptosDisponibles;
        $data['FechaGenerado'] = date("Y-m-d H:i:s");
        $data['BNF_Categoria_id'] = $categoria;
        if ($idCorreo != null) {
            $data['BNF_ClienteCorreo_id'] = $idCorreo;
        }

        return $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function setCuponCode($codigo, $idCupon, $idAsignacion)
    {
        $data = array();
        $data['CodigoCupon'] = $codigo;
        $data['BNF3_Asignacion_Premios_id'] = $idAsignacion;
        return $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function getCuponValid($idOferta, $atributo)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $idOferta);
        if ($atributo != null) {
            $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $atributo);
        }
        $select->where->equalTo('EstadoCupon', 'Creado');
        $select->limit(1);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}