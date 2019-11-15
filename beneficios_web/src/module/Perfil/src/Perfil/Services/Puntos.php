<?php
/**
 * Created by PhpStorm.
 * User: liszy
 * Date: 01/08/16
 * Time: 04:39 PM
 */

namespace Perfil\Services;

use Zend\Session\Container as SessionContainer;

class Puntos
{
    protected $serviceLocator;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getTotalPuntos($cliente_id)
    {
        $asignacionPuntosTable = $this->serviceLocator->get('Application\Model\Table\AsignacionTable');
        $asignacion = $asignacionPuntosTable->getAsignacionForCliente($cliente_id);

        $totalPuntos = 0;
        foreach ($asignacion as $value) {
            if($value->EstadoPuntos == "Activado"){
                $totalPuntos += $value->CantidadPuntosDisponibles;
            }
        }

        return $totalPuntos;
    }

    public function updatePuntos($cliente_id)
    {
        $puntos = $this->getTotalPuntos($cliente_id);
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['puntos'] = $puntos;
        $session->offsetSet('storage', $data_user);
        return $puntos;
    }
}