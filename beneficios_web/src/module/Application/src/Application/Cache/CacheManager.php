<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 14/12/15
 * Time: 12:36 PM
 */

namespace Application\Cache;

use Zend\Cache\StorageFactory;

class CacheManager
{
    protected $config;

    public function __construct($options = null)
    {
        $this->setConfig($options);
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getCache($class, $function, $time = 3600)
    {
        $config = $this->getConfig();
        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'memcached',
                'options' => array(
                    'ttl' => $time,
                    'servers' => array(
                        array(
                            $config['server'], $config['port']
                        )
                    ),
                    'namespace' => $class,
                    'namespaceSeparator' => $function,
                    'lib_options' => array(
                        'COMPRESSION' => true,
                        'binary_protocol' => true,
                        'no_block' => true,
                        'connect_timeout' => 100
                    )
                )
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                )
            )
        ));

        return $cache;
    }
}