<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 26/07/16
 * Time: 11:53 PM
 */

namespace Application\Service;

use Zend\Session\Container as SessionContainer;

class Ubigeo
{
    protected $serviceLocator;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function setUbigeo($ubigeo)
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['ubigeo'] = $ubigeo;
        $session->offsetSet('storage', $data_user);
    }

    public function getNombre($id)
    {
        $ubigeoTable = $this->serviceLocator->get('Application\Model\UbigeoTable');
        $ubigeos = $ubigeoTable->getUbigeo($id);
        return $ubigeos->Nombre;
    }

}