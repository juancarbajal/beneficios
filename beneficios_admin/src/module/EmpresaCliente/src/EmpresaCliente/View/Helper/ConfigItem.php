<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/11/15
 * Time: 10:18 AM
 */

namespace EmpresaCliente\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

/**
 * Returns total value (with tax)
 *
 */
class ConfigItem extends AbstractHelper
{
    /**
     * Service Locator
     * @var ServiceManager
     */
    protected $serviceLocator;

    /**
     * __invoke
     *
     * @access public
     * @param  string
     * @return String
     */
    public function __invoke($value)
    {
        $config = $this->serviceLocator->get('config');
        if (isset($config[$value])) {
            return $config[$value];
        }

        return null;
        // we could return a default value, or throw exception etc here
    }

    /**
     * Setter for $serviceLocator
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}