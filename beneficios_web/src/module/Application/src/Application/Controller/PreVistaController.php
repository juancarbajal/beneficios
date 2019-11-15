<?php

namespace Application\Controller;

use Application\Model\AsignacionEstadoLog;
use Application\Model\OfertaPuntos;
use Perfil\Services\Puntos;
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
    const CATEGORIA_DEFAULT = 9;
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
    const TIPO_MENSAJE_ERROR = 'danger';

    #region ObjectTables
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
    }

    public function getCuponPuntosTable()
    {
        return $this->serviceLocator->get('Perfil\Model\Table\CuponPuntosTable');
    }

    public function getOfertaPuntosAtributosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosAtributosTable');
    }

    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\ConfiguracionesTable');
    }

    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\BannersCategoriaTable');
    }

    public function getOfertaEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaEmpresaClienteTable');
    }

    public function getTarjetasOfertaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\TarjetasOfertaTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\UbigeoTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
    }

    public function getOfertaFormularioTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaFormularioTable');
    }

    public function getDetalleOfertaFormularioTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\DetalleOfertaFormularioTable');
    }

    public function getOfertaFormularioClienteTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaFormClienteTable');
    }

    public function getClienteTable()
    {
        return $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
    }

    public function getFormularioLeadTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\FormularioLeadTable');
    }

    public function getOfertaFormularioLeadTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaFormClienteLeadTable');
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
        $cupon = $this->getOfertaPuntosTable()->getCuponOfertaSlug($coupon);

        if ($cupon == false) {
            return $this->redirect()->toRoute('404', array('opt' => $coupon));
        }

        //imagenes de cupon
        $imgCupon = $this->getOfertaPuntosTable()->getImagenCupon($cupon->idOferta);

        //datos Atributos
        $atributosData = $this->getOfertaPuntosAtributosTable()->getAllOfertaPuntosAtributos($cupon->idOferta);

        $config = $this->getServiceLocator()->get('Config');

        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'coupon-puntos/' . $coupon,
                'router' => 'coupon-puntos',
                'slug' => 'ptos',
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas-puntos"],
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

        return $view;
    }

    public function normalAction()
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
        $cupon = $this->getOfertaEmpresaClienteTable()->getCuponOfertaSlug($coupon);

        if ($cupon == false) {
            return $this->redirect()->toRoute('404', array('opt' => $coupon));
        }

        //imagenes de cupon
        $imgCupon = $this->getOfertaTable()->getImagenCupon($cupon->idOferta);

        $config = $this->getServiceLocator()->get('Config');

        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $tarjetasData = $this->getTarjetasOfertaTable()->getAllTarjetasOferta($cupon->idOferta);

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'home/' . $coupon,
                'router' => 'destacados',
                'slug' => 'ptos',
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'cupon' => $cupon,
                'imgCupon' => $imgCupon,
                'url_slug' => $coupon,
                'conf' => $conf,
                'tarjetas' => $tarjetasData,
                'meses' => $meses
            )
        );

        return $view;
    }

    public function leadAction()
    {
        $nombre = null;
        $ofertas = null;
        $estado = null;
        $condicionesTexto = null;
        $condicionesEstado = null;
        $nombreempresa = null;
        $id = 0;
        $select = -1;
        $pais = 1;
        $envio = null;
        $message = array();
        $datos = array();
        $otros = array();
        $type = $this::TIPO_MENSAJE_ERROR;
        $active = true;
        $data_recovered = array();

        $slug = $this->params()->fromRoute('coupon', 0);

        $catotros = $this->getCategoriaTable()->getBuscarCatOtros($pais);

        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $subgrupo = $this->identity()['subgrupo'];
        $segmento = $this->identity()['segmento'];

        //Configuraciones
        $terminoscondiciones = $this->getConfiguracionesTable()->getConfig('terminoscondicioneslead');
        $mensaje_confirmacion = $this->getConfiguracionesTable()->getConfig('mensaje_confirmacion_lead');
        $textobanner = $this->getConfiguracionesTable()->getConfig('textobannerlead');


        //categorias
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($pais);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($pais);

        //varibles globales
        $config = $this->getServiceLocator()->get('Config');
        $oferta_lead = $this->getOfertaFormularioTable()->getFormularios($slug);

        foreach ($oferta_lead as $dato) {
            $nombreempresa = $dato->NombreComercial;
            $id = $dato->oferta_id;
            $estado = $dato->Estado;
        }

        if ($id == 0) {
            return $this->redirect()->toRoute('404', array('opt' => $slug));
        }

        //Datos Condiciones Delivery
        $oferta = $this->getOfertaTable()->getOferta($id);
        $condicionesTexto = $oferta->CondicionesDeliveryTexto;
        $condicionesEstado = $oferta->CondicionesDeliveryEstado;

        //Datos del Formulario Lead
        $form_lead = $this->getFormularioLeadTable()->getFormulario($id);
        $form_config = $this->getOfertaFormularioTable()->getFormularios($slug);

        $departamentos = $this->getUbigeoTable()->fetchAllDepartamentXPais($pais);
        $provincias = $this->getUbigeoTable()->fetchAllProvince();

        $view = new ViewModel();
        $view->setVariables(
            array(
                //'url' => $this::URL_RESULTADO,
                'router' => 'lead',
                'slug' => $slug,
                'estado' => $estado,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'terminos' => $terminoscondiciones->Atributo,
                'textobanner' => $textobanner->Atributo,
                'nombre' => $nombre,
                'imgemp' => $img,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                //'typebusqueda' => $this::TIPO_BUSQUEDA,
                'ofertas' => $ofertas,
                'select' => $select,
                'campania_id' => null,
                'nombreempresa' => $nombreempresa,
                'form_config' => $form_config,
                'id' => $id,
                'message' => $message,
                'confir' => $envio,
                'catotros' => $catotros,
                'type' => $type,
                'departamentos' => $departamentos,
                'provincias' => $provincias,
                'active' => $active,
                'afiliadas' => true,
                'form_lead' => $form_lead,
                'data_recovered' => $data_recovered,
                'condicionesTexto' => $condicionesTexto,
                'condicionesEstado' => $condicionesEstado,
            )
        );

        return $view;
    }

}
