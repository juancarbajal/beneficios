<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/09/15
 * Time: 07:06 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class OfertaUbigeoTable
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

    public function getOfertaUbigeo($pais, $empresa_id, $segmento, $subgrupo = 0)
    {
        $select = new Select();
        $select->from('BNF_Ubigeo');
        $select->columns(array('Nombre' => new Expression('DISTINCT(BNF_Ubigeo.Nombre)'), 'id'));
        $select->join('BNF_OfertaUbigeo', 'BNF_Ubigeo.id = BNF_OfertaUbigeo.BNF_Ubigeo_id', array());
        $select->join('BNF_Oferta', 'BNF_Oferta.id = BNF_OfertaUbigeo.BNF_Oferta_id', array());
        $select->join('BNF_OfertaEmpresaCliente', 'BNF_OfertaEmpresaCliente.BNF_Oferta_id = BNF_Oferta.id', array());
        $select->join('BNF_Empresa', 'BNF_Empresa.id = BNF_OfertaEmpresaCliente.BNF_Empresa_id', array());
        //Segmento
        $select->join(array('OS' => 'BNF_OfertaSegmento'), "OS.BNF_Oferta_id = BNF_Oferta.id", array());
        //Subgrupo
        if ($subgrupo > 0) {
            $select->join(array('OSG' => 'BNF_OfertaSubgrupo'), "OSG.BNF_Oferta_id = BNF_Oferta.id", array());
        }
        $select->where->greaterThan('BNF_Oferta.Stock', 0)
            ->and->equalTo('BNF_OfertaUbigeo.Eliminado', '0')
            ->and->equalTo('BNF_Ubigeo.BNF_Pais_id', $pais)
            ->and->equalTo('BNF_Empresa.id', $empresa_id)
            ->and->equalTo('BNF_Oferta.Estado', 'Publicado')
            ->and->isNull('id_padre');
        //Filtro Subgrupos
        if ($segmento > 0) {
            $select->where->equalTo('OS.Eliminado', 0)
                ->and->equalTo('OS.BNF_Segmento_id', $segmento);
        }
        //Filtro Subgrupos
        if ($subgrupo > 0) {
            $select->where->equalTo('OSG.Eliminado', 0)
                ->and->equalTo('OSG.BNF_Subgrupo_id', $subgrupo);
        }
        /*$query = $select->getSqlString();
        echo str_replace('"','', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
