<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 07:31 PM
 */

namespace Application\Model\Table;

use Zend\Db\TableGateway\TableGateway;

class FormularioLeadTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getFormulario($oferta)
    {
        $id = (int)$oferta;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id));
        return $rowset;
    }
}
