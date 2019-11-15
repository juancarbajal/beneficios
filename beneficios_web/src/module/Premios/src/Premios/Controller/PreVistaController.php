<?php

namespace Premios\Controller;

use Premios\Model\AsignacionPremiosEstadoLog;
use Premios\Model\OfertaPremios;
use Premios\Services\Premios;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Application\Cache\CacheManager;
use Application\Service\MobileDetect;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Mime\Mime;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class PreVistaController extends AbstractActionController
{
    const PAIS_DEFAULT = 1;
    const CATEGORIA_DEFAULT = 10;
    const OFETAS_PREMIUN = 1;
    const OFETAS_NOVADADES = 2;
    const OFETAS_DESTACADOS = 3;
    const OFETAS_RESTANTES = 0;
    const NOT_OFFSET = -1;
    const TIPO_CATEGORIA = 1;
    const TIPO_CAMPANIA = 2;
    const TIPO_TIENDA = 3;
    const OFERTA_TIPO_SPLIT = "Split";
    const OFERTA_TIPO_UNICO = "Unico";
    const OPERACION_APLICAR = "Aplicar";

    #region ObjectTables

    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaTable');
    }

    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
    }

    public function getCuponPremiosTable()
    {
        return $this->serviceLocator->get('Perfil\Model\Table\CuponPremiosTable');
    }

    public function getOfertaPremiosAtributosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosAtributosTable');
    }

    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\ConfiguracionesTable');
    }

    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersCategoriaTable');
    }

    #endregion

    public function couponAction()
    {
        $ofertas = null;
        $company = null;
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        );

        $coupon = $this->params()->fromRoute('coupon', 0);

        //buscar datos del Cupon
        $cupon = $this->getOfertaPremiosTable()->getCuponOfertaSlug($coupon);

        if ($cupon == false) {
            return $this->redirect()->toRoute('404', array('opt' => $coupon));
        }

        //imagenes de cupon
        $imgCupon = $this->getOfertaPremiosTable()->getImagenCupon($cupon->idOferta);

        //datos Atributos
        $atributosData = $this->getOfertaPremiosAtributosTable()->getAllOfertaPremiosAtributos($cupon->idOferta);

        $config = $this->getServiceLocator()->get('Config');

        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'coupon-premios/' . $coupon,
                'router' => 'coupon-premios',
                'slug' => 'ptos',
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas-premios"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'cupon' => $cupon,
                'imgCupon' => $imgCupon,
                'url_slug' => $coupon,
                'atributosData' => $atributosData,
                'conf' => $conf,
                'meses' => $meses
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('premios/premios/cupon-mobile');
        }
        return $view;
    }
}
