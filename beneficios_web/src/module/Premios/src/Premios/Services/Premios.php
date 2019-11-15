<?php
/**
 * Created by PhpStorm.
 * User: Marlo
 * Date: 01/08/16
 * Time: 04:39 PM
 */

namespace Premios\Services;

use Zend\Session\Container as SessionContainer;

class Premios
{
    protected $serviceLocator;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getTotalPremios($cliente_id)
    {
        $asignacionPremiosTable = $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
        $asignacion = $asignacionPremiosTable->getAsignacionForCliente($cliente_id);

        $totalPremios = 0;
        foreach ($asignacion as $value) {
            if($value->EstadoPremios == "Activado"){
                $totalPremios += $value->CantidadPremiosDisponibles;
            }
        }

        return $totalPremios;
    }

    public function updatePremios($cliente_id)
    {
        $Premios = $this->getTotalPremios($cliente_id);
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['premios'] = $Premios;
        $session->offsetSet('storage', $data_user);
        return $Premios;
    }
}