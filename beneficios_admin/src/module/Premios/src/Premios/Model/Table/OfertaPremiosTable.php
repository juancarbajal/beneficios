<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 12:51 PM
 */

namespace Premios\Model\Table;

use Premios\Model\OfertaPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaPremiosTable
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

    public function getOfertaPremios($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getOfertaPremiosBySegmento($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('BNF3_Segmento_id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getAllOfertaPremiosBySegmento($id)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(array('*'));
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );

        $select->where->equalTo("BNF3_Segmentos.id", $id);
        $select->group("BNF3_Oferta_Premios.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getAllOfertaPremiosByCampania($id)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(array('id', 'Titulo'));
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );

        $select->where->equalTo("BNF3_Campanias.id", $id);
        $select->group("BNF3_Oferta_Premios.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getAllOfertaPremiosByCampaniaAndEmpresaProv($id, $empresa)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                'id' => new Expression('(IFNULL(BNF3_Oferta_Premios_Atributos.id,
                        BNF3_Oferta_Premios.id))'),
                'Titulo'
            )
        );
        $select->join(
            "BNF3_Oferta_Premios_Atributos",
            "BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id",
            array('Atributo' => 'NombreAtributo'),
            'left'
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );

        $select->where->equalTo("BNF3_Campanias.id", $id);
        $select->where->equalTo("BNF3_Oferta_Premios.BNF_Empresa_id", $empresa);
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

    public function getOfertaPremiosEmpresaCliente($empresa_id = null)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(array('id', 'Titulo'));
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
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

    public function getOfertaPremiosBySlug($slug)
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
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                'id',
                'Titulo',
                'TipoPrecio',
                'Estado',
                'Segmentos' => new Expression(
                    "(SELECT DISTINCT GROUP_CONCAT(DISTINCT TRIM(NombreSegmento) SEPARATOR ', ') FROM BNF3_Segmentos " .
                    "INNER JOIN BNF3_Oferta_Premios_Segmentos ON BNF3_Segmentos.id = BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id " .
                    "WHERE BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id " .
                    "AND BNF3_Oferta_Premios_Segmentos.Eliminado = 0)"
                )
            )
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($titulo)) {
            $select->where->equalTo("BNF3_Oferta_Premios.id", $titulo);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF3_Oferta_Premios.FechaCreacion DESC");
        }

        //$select->where->equalTo("BNF3_Oferta_Premios_Segmentos.Eliminado", 0);
        $select->group("BNF3_Oferta_Premios.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getEmpresaClientes()
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('id', "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        $select->where->equalTo("BNF3_Oferta_Premios_Segmentos.Eliminado", 0);
        $select->order("Empresa ASC");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getReporte($empresa = null, $oferta = null)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                'id',
                'Titulo',
                'TipoPrecio',
                'Estado',
                'Segmentos' => new Expression(
                    "(SELECT DISTINCT GROUP_CONCAT(DISTINCT TRIM(NombreSegmento) SEPARATOR ', ') FROM BNF3_Segmentos " .
                    "INNER JOIN BNF3_Oferta_Premios_Segmentos ON BNF3_Segmentos.id = BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id " .
                    "WHERE BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id " .
                    "AND BNF3_Oferta_Premios_Segmentos.Eliminado = 0)"
                )
            )
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array()
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($oferta)) {
            $select->where->equalTo("BNF3_Oferta_Premios.id", $oferta);
        }

        $select->where->equalTo("BNF3_Oferta_Premios_Segmentos.Eliminado", 0);
        $select->group("BNF3_Oferta_Premios.id");
        $select->order("BNF3_Oferta_Premios.id");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function saveOfertaPremios(OfertaPremios $OfertaPremios)
    {
        $data = array(
            'BNF_Empresa_id' => $OfertaPremios->BNF_Empresa_id,
            'Nombre' => $OfertaPremios->Nombre,
            'Titulo' => $OfertaPremios->Titulo,
            'TituloCorto' => $OfertaPremios->TituloCorto,
            'CondicionesUso' => $OfertaPremios->CondicionesUso,
            'Direccion' => $OfertaPremios->Direccion,
            'Telefono' => $OfertaPremios->Telefono,
            'Correo' => $OfertaPremios->Correo,
            'Premium' => $OfertaPremios->Premium,
            'TipoPrecio' => $OfertaPremios->TipoPrecio,
            'PrecioVentaPublico' => $OfertaPremios->PrecioVentaPublico,
            'PrecioBeneficio' => $OfertaPremios->PrecioBeneficio,
            'Distrito' => $OfertaPremios->Distrito,
            'FechaVigencia' => $OfertaPremios->FechaVigencia,
            'DescargaMaxima' => $OfertaPremios->DescargaMaxima,
            'Stock' => $OfertaPremios->Stock,
            'Slug' => $OfertaPremios->Slug,
            'Estado' => $OfertaPremios->Estado,
            'Eliminado' => $OfertaPremios->Eliminado,
        );

        $id = (int)$OfertaPremios->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getOfertaPremios($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('OfertaPremios id does not exist');
            }
        }
        return $id;
    }

    public function deleteOfertaPremios($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getOfertasPremiosCaducadas()
    {
        $fecha = date('Y-m-d');
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                '*',
                'FechaVigencia' => new Expression('(IFNULL(BNF3_Oferta_Premios.FechaVigencia,
                        BNF3_Oferta_Premios_Atributos.FechaVigencia))')
            )
        );
        $select->join(
            "BNF3_Oferta_Premios_Atributos",
            "BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->literal(
            "IFNULL(TIMESTAMPDIFF(DAY,
                '" . $fecha . "',
                (IFNULL(BNF3_Oferta_Premios.FechaVigencia,
                        BNF3_Oferta_Premios_Atributos.FechaVigencia))),
            1) < 1"
        );

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertasPremiosFinalizadas()
    {
        $fecha = date('Y-m-d');
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                '*',
                'FechaVigencia' => new Expression('(IFNULL(BNF3_Oferta_Premios.FechaVigencia,
                        BNF3_Oferta_Premios_Atributos.FechaVigencia))'),
                'Stock' => new Expression('(IFNULL(BNF3_Oferta_Premios.Stock,
                        BNF3_Oferta_Premios_Atributos.Stock))')
            )
        );
        $select->join(
            "BNF3_Oferta_Premios_Atributos",
            "BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->literal(
            "IFNULL(TIMESTAMPDIFF(DAY,
                '" . $fecha . "',
                (IFNULL(BNF3_Oferta_Premios.FechaVigencia,
                        BNF3_Oferta_Premios_Atributos.FechaVigencia))),
            1) < 1"
        )->and->equalTo('Estado', 'Publicado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function updateOfertasPremiosFinalizadas($id)
    {
        $data['Stock'] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function updateOfertasPremiosVencidas($id)
    {
        $data['Stock'] = 0;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function getByEmpresaOrCampaniaOrSegmento($empresa_id, $campania_id, $segmento_id, $estado)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                'Titulo' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.Titulo,
                        CONCAT(BNF3_Oferta_Premios.Titulo, ' - ', BNF3_Oferta_Premios_Atributos.NombreAtributo)
                    ))"
                ),
                'PrecioBeneficio' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico', 
                        BNF3_Oferta_Premios.PrecioBeneficio, 
                        BNF3_Oferta_Premios_Atributos.PrecioBeneficio
                    ))"
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF3_Oferta_Premios.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    "(IF(TipoPrecio = 'Unico', 
                    BNF3_Oferta_Premios.PrecioVentaPublico, 
                    BNF3_Oferta_Premios_Atributos.PrecioVentaPublico))"
                ),
                'Descargas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS CP
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaGenerado IS NOT NULL 
                        AND CP.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS CP 
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaGenerado IS NOT NULL AND CP.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id 
                        AND CP.BNF3_Oferta_Premios_Atributos_id = BNF3_Oferta_Premios_Atributos.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id)
                    ))"),
                'Redimidas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF3_Cupon_Premios` AS CP
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE FechaRedimido IS NOT NULL 
                        AND `BNF3_Oferta_Premios_id` = BNF3_Oferta_Premios.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS CP 
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaRedimido IS NOT NULL AND CP.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id 
                        AND CP.BNF3_Oferta_Premios_Atributos_id = BNF3_Oferta_Premios_Atributos.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id)
                ))"),
                'Stock' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.Stock,
                        BNF3_Oferta_Premios_Atributos.Stock
                    ))"
                )
            )
        );
        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id',
            array(),
            'left'
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array('Segmentos' => 'NombreSegmento')
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array('Campania' => 'NombreCampania', 'VigenciaCampania' => 'VigenciaFin')
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF3_Oferta_Premios.BNF_Empresa_id = EP.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );
        $select->join(
            'BNF3_Oferta_Premios_Rubro',
            'BNF3_Oferta_Premios_Rubro.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id',
            array()
        );
        $select->join(
            'BNF_Rubro',
            'BNF3_Oferta_Premios_Rubro.BNF_Rubro_id = BNF_Rubro.id',
            array('Rubro' => 'Nombre')
        );

        $select->where->equalTo('BNF3_Oferta_Premios_Rubro.Eliminado', 0);
        $select->where->equalTo('BNF_Rubro.Eliminado', 0);

        if ($estado != '') {
            $select->where->equalTo('BNF3_Oferta_Premios.Estado', $estado);
        }

        if ($empresa_id > 0) {
            $select->where->equalTo('BNF_Empresa.id', $empresa_id)
                ->and->equalTo('BNF_Empresa.Cliente', 1);
        }
        if ($campania_id > 0) {
            $select->where->equalTo('BNF3_Campanias.id', $campania_id);
        }
        if ($segmento_id > 0) {
            $select->where->equalTo('BNF3_Segmentos.id', $segmento_id);
        }

        $select->order('Descargas DESC');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getByEmpresaOrCampaniaOrRango($empresa_id, $campania_id, $inicio, $fin)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                'Titulo' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.Titulo,
                        CONCAT(BNF3_Oferta_Premios.Titulo, ' - ', BNF3_Oferta_Premios_Atributos.NombreAtributo)
                    ))"
                ),
                'PrecioBeneficio' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico', 
                        BNF3_Oferta_Premios.PrecioBeneficio, 
                        BNF3_Oferta_Premios_Atributos.PrecioBeneficio
                    ))"
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF3_Oferta_Premios.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.PrecioVentaPublico,
                        BNF3_Oferta_Premios_Atributos.PrecioVentaPublico)
                    )"
                ),
                'Redimidas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF3_Cupon_Premios` 
                        WHERE FechaRedimido IS NOT NULL AND `BNF3_Oferta_Premios_id` = BNF3_Oferta_Premios.id),
                        (SELECT COUNT(*) FROM `BNF3_Cupon_Premios` 
                        WHERE FechaRedimido IS NOT NULL AND `BNF3_Oferta_Premios_id` = BNF3_Oferta_Premios.id 
                        AND BNF3_Oferta_Premios_Atributos_id = BNF3_Oferta_Premios_Atributos.id)
                    ))")
            )
        );
        $select->join(
            'BNF3_Cupon_Premios',
            'BNF3_Cupon_Premios.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id',
            array()
        );
        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id',
            array(),
            'left'
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array('Segmentos' => 'NombreSegmento')
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF3_Oferta_Premios.BNF_Empresa_id = EP.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        $select->where->isNotNull('FechaRedimido');

        if ($inicio && $fin) {
            $select->where->between('BNF3_Cupon_Premios.FechaRedimido', $inicio, $fin);
        } elseif ($inicio && $fin == '') {
            $select->where->greaterThan('BNF3_Cupon_Premios.FechaRedimido', $inicio);
        } elseif ($inicio == '' && $fin) {
            $select->where->lessThan('BNF3_Cupon_Premios.FechaRedimido', $fin);
        }

        if ($empresa_id > 0) {
            $select->where->equalTo('BNF_Empresa.id', $empresa_id)
                ->and->equalTo('BNF_Empresa.Cliente', 1);
        }
        if ($campania_id > 0) {
            $select->where->equalTo('BNF3_Campanias.id', $campania_id);
        }

        $select->group('BNF3_Oferta_Premios.id');
        $select->group('BNF3_Oferta_Premios_Atributos.id');
        $select->group('BNF3_Campanias.id');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getByEmpresaOrCampania($empresa_id, $campania_id)
    {
        $select = new Select();
        $select->from('BNF3_Oferta_Premios');
        $select->columns(
            array(
                '*',
                'Titulo' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.Titulo,
                        CONCAT(BNF3_Oferta_Premios.Titulo, ' - ', BNF3_Oferta_Premios_Atributos.NombreAtributo)
                    ))"
                ),
                'PrecioBeneficio' => new Expression(
                    '(IF(
                        BNF3_Oferta_Premios.PrecioBeneficio > 0, 
                        BNF3_Oferta_Premios.PrecioBeneficio, 
                        BNF3_Oferta_Premios_Atributos.PrecioBeneficio
                    ))'
                ),
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF3_Oferta_Premios.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    '(IF(BNF3_Oferta_Premios.PrecioVentaPublico = 0,
                    BNF3_Oferta_Premios_Atributos.PrecioVentaPublico, 
                    BNF3_Oferta_Premios.PrecioVentaPublico))'
                ),
                'Redimidas' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF3_Cupon_Premios` AS CP
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE FechaRedimido IS NOT NULL 
                        AND `BNF3_Oferta_Premios_id` = BNF3_Oferta_Premios.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS CP 
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaRedimido IS NOT NULL AND CP.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id 
                        AND CP.BNF3_Oferta_Premios_Atributos_id = BNF3_Oferta_Premios_Atributos.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id)
                    ))"),
                'Pagados' => new Expression(
                    "(IF(
                        TipoPrecio = 'Unico',
                        (SELECT COUNT(*) FROM `BNF3_Cupon_Premios` AS CP
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE FechaPagado IS NOT NULL 
                        AND `BNF3_Oferta_Premios_id` = BNF3_Oferta_Premios.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id),
                        (SELECT COUNT(*) FROM BNF3_Cupon_Premios AS CP 
                        INNER JOIN BNF3_Asignacion_Premios AS AP ON AP.BNF_Cliente_id = CP.BNF_Cliente_id
                        WHERE CP.FechaPagado IS NOT NULL AND CP.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id 
                        AND CP.BNF3_Oferta_Premios_Atributos_id = BNF3_Oferta_Premios_Atributos.id 
                        AND AP.BNF3_Segmento_id = BNF3_Segmentos.id)
                    ))")
            )
        );
        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id',
            array(),
            'left'
        );

        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id = BNF3_Segmentos.id',
            array('Segmentos' => 'NombreSegmento')
        );
        $select->join(
            'BNF3_Campanias',
            'BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id',
            array('Campania' => 'NombreCampania')
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'BNF3_Oferta_Premios.BNF_Empresa_id = EP.id',
            array('BNF_Empresa_id' => 'NombreComercial')
        );

        if ($empresa_id > 0) {
            $select->where->equalTo('EP.id', $empresa_id)
                ->and->equalTo('EP.Proveedor', 1);
        }
        if ($campania_id > 0) {
            $select->where->equalTo('BNF3_Campanias.id', $campania_id);
        }

        $select->order('Redimidas DESC');

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }
}
