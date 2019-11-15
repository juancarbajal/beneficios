<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:51 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\OfertaPuntos;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPuntosTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getOfertaPuntos($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaExits($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->where->equalTo("id", $id);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaPuntosBySegmento($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF2_Segmento_id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getAllOfertaPuntosBySegmento($id)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(array('*'));
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );

        $select->where->equalTo("BNF2_Segmentos.id", $id);
        $select->group("BNF2_Oferta_Puntos.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getAllOfertaPuntosByCampania($id)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(array('id', 'Titulo'));
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );

        $select->where->equalTo("BNF2_Campanias.id", $id);
        $select->group("BNF2_Oferta_Puntos.id");

        //echo str_replace('"', ' ', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getAllOfertaPuntosByCampaniaAndEmpresaProv($id, $empresa)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                'id' => new Expression('(IFNULL(BNF2_Oferta_Puntos_Atributos.id,
                        BNF2_Oferta_Puntos.id))'),
                'Titulo'
            )
        );
        $select->join(
            "BNF2_Oferta_Puntos_Atributos",
            "BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id",
            array('Atributo' => 'NombreAtributo'),
            'left'
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );

        $select->where->equalTo("BNF2_Campanias.id", $id);
        $select->where->equalTo("BNF2_Oferta_Puntos.BNF_Empresa_id", $empresa);
        $select->group("id");

        //echo str_replace('"', ' ', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getIfExist($id)
    {
        $id = (int)$id;
        try {
            $rowSet = $this->tableGateway->select(array('id' => $id));
            $row = $rowSet->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            return $row;
        } catch (\Exception $ex) {
            return 0;
        }
    }

    public function getOfertaPuntosEmpresaCliente($empresa_id = null)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(array('id', 'Titulo'));
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        if ($empresa_id > 0) {
            $select->where->equalTo('BNF_Empresa.id', $empresa_id)
                ->and->equalTo('BNF_Empresa.Cliente', 1);
        }

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaPuntosBySlug($slug)
    {
        $rowSet = $this->tableGateway->select(array('Slug' => $slug));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getDetails($order_by, $order, $empresa = "", $titulo = null)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                'id',
                'Titulo',
                'TipoPrecio',
                'Estado',
                'Segmentos' => new Expression(
                    "(SELECT DISTINCT GROUP_CONCAT(DISTINCT TRIM(NombreSegmento) SEPARATOR ', ') FROM BNF2_Segmentos " .
                    "INNER JOIN BNF2_Oferta_Puntos_Segmentos ON BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id " .
                    "WHERE BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id " .
                    "AND BNF2_Oferta_Puntos_Segmentos.Eliminado = 0)"
                )
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($titulo)) {
            $select->where->equalTo("BNF2_Oferta_Puntos.id", $titulo);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Oferta_Puntos.FechaCreacion DESC");
        }

        //$select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        $select->group("BNF2_Oferta_Puntos.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getEmpresaClientes()
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('id', "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        $select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        $select->order("Empresa ASC");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getReporte($empresa = null, $oferta = null)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                'id',
                'Titulo',
                'TipoPrecio',
                'Estado',
                'Segmentos' => new Expression(
                    "(SELECT DISTINCT GROUP_CONCAT(DISTINCT TRIM(NombreSegmento) SEPARATOR ', ') FROM BNF2_Segmentos " .
                    "INNER JOIN BNF2_Oferta_Puntos_Segmentos ON BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id " .
                    "WHERE BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id " .
                    "AND BNF2_Oferta_Puntos_Segmentos.Eliminado = 0)"
                ),
                'PrecioVentaPublico' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioVentaPublico,
                        (SELECT GROUP_CONCAT(TRIM(PrecioVentaPublico) SEPARATOR ', ') 
                        FROM BNF2_Oferta_Puntos_Atributos 
                        WHERE BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id))
                    )"
                ),
                'PrecioBeneficio' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico', 
                        BNF2_Oferta_Puntos.PrecioBeneficio, 
                        (SELECT GROUP_CONCAT(TRIM(PrecioBeneficio) SEPARATOR ', ') 
                        FROM BNF2_Oferta_Puntos_Atributos 
                        WHERE BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id))
                    )"
                ),
                'FechaVigencia' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.FechaVigencia,
                        (SELECT GROUP_CONCAT(TRIM(FechaVigencia) SEPARATOR ', ') 
                        FROM BNF2_Oferta_Puntos_Atributos 
                        WHERE BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id))
                    )"
                ),
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array(),
            'left'
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array()
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($titulo)) {
            $select->where->equalTo("BNF2_Oferta_Puntos.id", $titulo);
        }

        //$select->where->equalTo("BNF2_Oferta_Puntos_Segmentos.Eliminado", 0);
        $select->group("BNF2_Oferta_Puntos.id");

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function saveOfertaPuntos(OfertaPuntos $ofertaPuntos)
    {
        $data = array(
            'BNF_Empresa_id' => $ofertaPuntos->BNF_Empresa_id,
            'Nombre' => $ofertaPuntos->Nombre,
            'Titulo' => $ofertaPuntos->Titulo,
            'TituloCorto' => $ofertaPuntos->TituloCorto,
            'CondicionesUso' => $ofertaPuntos->CondicionesUso,
            'Direccion' => $ofertaPuntos->Direccion,
            'Telefono' => $ofertaPuntos->Telefono,
            'Correo' => $ofertaPuntos->Correo,
            'Premium' => $ofertaPuntos->Premium,
            'TipoPrecio' => $ofertaPuntos->TipoPrecio,
            'PrecioVentaPublico' => $ofertaPuntos->PrecioVentaPublico,
            'PrecioBeneficio' => $ofertaPuntos->PrecioBeneficio,
            'Distrito' => $ofertaPuntos->Distrito,
            'FechaVigencia' => $ofertaPuntos->FechaVigencia,
            'DescargaMaxima' => $ofertaPuntos->DescargaMaxima,
            'Stock' => $ofertaPuntos->Stock,
            'Slug' => $ofertaPuntos->Slug,
            'Estado' => $ofertaPuntos->Estado,
            'Eliminado' => $ofertaPuntos->Eliminado,
        );

        $id = (int)$ofertaPuntos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPuntos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPuntos id does not exist');
            }
        }
        return $id;
    }

    public function deleteOfertaPuntos($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getOfertasPuntosCaducadas()
    {
        $fecha = date('Y-m-d');
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                '*',
                'FechaVigencia' => new Expression('(IFNULL(BNF2_Oferta_Puntos.FechaVigencia,
                        BNF2_Oferta_Puntos_Atributos.FechaVigencia))')
            )
        );
        $select->join(
            "BNF2_Oferta_Puntos_Atributos",
            "BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->literal(
            "IFNULL(TIMESTAMPDIFF(DAY,
                '" . $fecha . "',
                (IFNULL(BNF2_Oferta_Puntos.FechaVigencia,
                        BNF2_Oferta_Puntos_Atributos.FechaVigencia))),
            1) < 1"
        );

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertasPuntosFinalizadas()
    {
        $fecha = date('Y-m-d');
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                '*',
                'FechaVigencia' => new Expression('(IFNULL(BNF2_Oferta_Puntos.FechaVigencia,
                        BNF2_Oferta_Puntos_Atributos.FechaVigencia))'),
                'Stock' => new Expression('(IFNULL(BNF2_Oferta_Puntos.Stock,
                        BNF2_Oferta_Puntos_Atributos.Stock))')
            )
        );
        $select->join(
            "BNF2_Oferta_Puntos_Atributos",
            "BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->literal(
            "IFNULL(TIMESTAMPDIFF(DAY,
                '" . $fecha . "',
                (IFNULL(BNF2_Oferta_Puntos.FechaVigencia,
                        BNF2_Oferta_Puntos_Atributos.FechaVigencia))),
            1) < 1"
        )->and->equalTo('Estado', 'Publicado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function updateOfertasPuntosFinalizadas($id)
    {
        $data['Stock'] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function updateOfertasPuntosVencidas($id)
    {
        $data['Stock'] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function getByEmpresaOrCampaniaOrSegmento($empresa_id, $campania_id, $segmento_id, $estado)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                'Titulo' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.Titulo,
                        CONCAT(BNF2_Oferta_Puntos.Titulo, ' - ', BNF2_Oferta_Puntos_Atributos.NombreAtributo)
                    ))"
                ),
                'PrecioBeneficio' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico', 
                        BNF2_Oferta_Puntos.PrecioBeneficio, 
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio
                    ))"
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF2_Oferta_Puntos.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    "(IF(TipoPrecio = 'Unico', 
                    BNF2_Oferta_Puntos.PrecioVentaPublico, 
                    BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico))"
                ),
                'Descargas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS CP
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaGenerado IS NOT NULL 
                        AND CP.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS CP 
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaGenerado IS NOT NULL AND CP.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id 
                        AND CP.BNF2_Oferta_Puntos_Atributos_id = BNF2_Oferta_Puntos_Atributos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id)
                    ))"),
                'Redimidas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF2_Cupon_Puntos` AS CP
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE FechaRedimido IS NOT NULL 
                        AND `BNF2_Oferta_Puntos_id` = BNF2_Oferta_Puntos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS CP 
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaRedimido IS NOT NULL AND CP.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id 
                        AND CP.BNF2_Oferta_Puntos_Atributos_id = BNF2_Oferta_Puntos_Atributos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id)
                ))"),
                'Stock' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.Stock,
                        BNF2_Oferta_Puntos_Atributos.Stock
                    ))"
                )
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array(),
            'left'
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array('Segmentos' => 'NombreSegmento')
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array('Campania' => 'NombreCampania', 'VigenciaCampania' => 'VigenciaFin')
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF2_Oferta_Puntos.BNF_Empresa_id = EP.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );
        $select->join(
            'BNF2_Oferta_Puntos_Rubro',
            'BNF2_Oferta_Puntos_Rubro.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array()
        );
        $select->join(
            'BNF_Rubro',
            'BNF2_Oferta_Puntos_Rubro.BNF_Rubro_id = BNF_Rubro.id',
            array('Rubro' => 'Nombre')
        );

        $select->where->equalTo('BNF2_Oferta_Puntos_Rubro.Eliminado', 0);
        $select->where->equalTo('BNF2_Oferta_Puntos_Segmentos.Eliminado', 0);
        $select->where->equalTo('BNF_Rubro.Eliminado', 0);

        if ($estado != '') {
            $select->where->equalTo('BNF2_Oferta_Puntos.Estado', $estado);
        }

        if ($empresa_id > 0) {
            $select->where->equalTo('BNF_Empresa.id', $empresa_id)
                ->and->equalTo('BNF_Empresa.Cliente', 1);
        }
        if ($campania_id > 0) {
            $select->where->equalTo('BNF2_Campanias.id', $campania_id);
        }
        if ($segmento_id > 0) {
            $select->where->equalTo('BNF2_Segmentos.id', $segmento_id);
        }

        $select->order('Descargas DESC');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getByEmpresaOrCampaniaOrRango($empresa_id, $campania_id, $inicio, $fin)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                'Titulo' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.Titulo,
                        CONCAT(BNF2_Oferta_Puntos.Titulo, ' - ', BNF2_Oferta_Puntos_Atributos.NombreAtributo)
                    ))"
                ),
                'PrecioBeneficio' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico', 
                        BNF2_Oferta_Puntos.PrecioBeneficio, 
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio
                    ))"
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF2_Oferta_Puntos.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioVentaPublico,
                        BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico)
                    )"
                ),
                'Redimidas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF2_Cupon_Puntos` 
                        WHERE FechaRedimido IS NOT NULL AND `BNF2_Oferta_Puntos_id` = BNF2_Oferta_Puntos.id),
                        (SELECT COUNT(*) FROM `BNF2_Cupon_Puntos` 
                        WHERE FechaRedimido IS NOT NULL AND `BNF2_Oferta_Puntos_id` = BNF2_Oferta_Puntos.id 
                        AND BNF2_Oferta_Puntos_Atributos_id = BNF2_Oferta_Puntos_Atributos.id)
                    ))")
            )
        );
        $select->join(
            'BNF2_Cupon_Puntos',
            'BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
            array(),
            'left'
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array('Segmentos' => 'NombreSegmento')
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF2_Oferta_Puntos.BNF_Empresa_id = EP.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        $select->where->isNotNull('FechaRedimido');

        if ($inicio && $fin) {
            $select->where->between('BNF2_Cupon_Puntos.FechaRedimido', $inicio, $fin);
        } elseif ($inicio && $fin == '') {
            $select->where->greaterThan('BNF2_Cupon_Puntos.FechaRedimido', $inicio);
        } elseif ($inicio == '' && $fin) {
            $select->where->lessThan('BNF2_Cupon_Puntos.FechaRedimido', $fin);
        }

        if ($empresa_id > 0) {
            $select->where->equalTo('BNF_Empresa.id', $empresa_id)
                ->and->equalTo('BNF_Empresa.Cliente', 1);
        }
        if ($campania_id > 0) {
            $select->where->equalTo('BNF2_Campanias.id', $campania_id);
        }

        $select->group('BNF2_Oferta_Puntos.id');
        $select->group('BNF2_Oferta_Puntos_Atributos.id');
        $select->group('BNF2_Campanias.id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getByEmpresaOrCampania($empresa_id, $campania_id)
    {
        $select = new Select();
        $select->from('BNF2_Oferta_Puntos');
        $select->columns(
            array(
                '*',
                'Titulo' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.Titulo,
                        CONCAT(BNF2_Oferta_Puntos.Titulo, ' - ', BNF2_Oferta_Puntos_Atributos.NombreAtributo)
                    ))"
                ),
                'PrecioBeneficio' => new Expression(
                    '(IF(
                        BNF2_Oferta_Puntos.PrecioBeneficio > 0, 
                        BNF2_Oferta_Puntos.PrecioBeneficio, 
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio
                    ))'
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF2_Oferta_Puntos.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    '(IF(BNF2_Oferta_Puntos.PrecioVentaPublico = 0,
                    BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico, 
                    BNF2_Oferta_Puntos.PrecioVentaPublico))'
                ),
                'Redimidas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF2_Cupon_Puntos` AS CP
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE FechaRedimido IS NOT NULL 
                        AND `BNF2_Oferta_Puntos_id` = BNF2_Oferta_Puntos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS CP 
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaRedimido IS NOT NULL AND CP.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id 
                        AND CP.BNF2_Oferta_Puntos_Atributos_id = BNF2_Oferta_Puntos_Atributos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id)
                    ))"),
                'Pagados' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF2_Cupon_Puntos` AS CP
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE FechaPagado IS NOT NULL 
                        AND `BNF2_Oferta_Puntos_id` = BNF2_Oferta_Puntos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF2_Cupon_Puntos AS CP 
                        INNER JOIN BNF2_Asignacion_Puntos AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaPagado IS NOT NULL AND CP.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id 
                        AND CP.BNF2_Oferta_Puntos_Atributos_id = BNF2_Oferta_Puntos_Atributos.id 
                        AND AP.BNF2_Segmento_id = BNF2_Segmentos.id)
                    ))")
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array(),
            'left'
        );

        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id = BNF2_Segmentos.id',
            array('Segmentos' => 'NombreSegmento')
        );
        $select->join(
            'BNF2_Campanias',
            'BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF2_Oferta_Puntos.BNF_Empresa_id = EP.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        $select->where->equalTo('BNF2_Oferta_Puntos_Segmentos.Eliminado', 0);

        if ($empresa_id > 0) {
            $select->where->equalTo('EP.id', $empresa_id)
                ->and->equalTo('EP.Proveedor', 1);
        }
        if ($campania_id > 0) {
            $select->where->equalTo('BNF2_Campanias.id', $campania_id);
        }

        $select->order('Redimidas DESC');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function updateOferta($id, $data)
    {
        $this->tableGateway->update($data, array('id' => (int)$id));
    }
}
