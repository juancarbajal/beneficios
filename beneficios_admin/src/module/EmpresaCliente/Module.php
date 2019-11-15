<?php
namespace EmpresaCliente;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handlerError'));

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function handlerError(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('Config');
        if ($config["debug_mode"] == false) {
            $viewModel = $app->getMvcEvent()->getViewModel();
            $viewModel->setTemplate('error/500');
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'configItem' => function ($helperPluginManager) {
                    $serviceLocator = $helperPluginManager->getServiceLocator();
                    $viewHelper = new View\Helper\ConfigItem();
                    $viewHelper->setServiceLocator($serviceLocator);

                    return $viewHelper;
                }
            ),
        );
    }
}
