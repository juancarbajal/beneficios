<?php

namespace Application\Controller;

use Application\Cache\CacheManager;
use Application\Model\DetalleOfertaFormulario;
use Application\Model\OfertaFormCliente;
use Application\Model\OfertaFormClienteLead;
use Application\Service\MobileDetect;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Debug\Debug;

class DeliveryController extends AbstractActionController
{
    const ROUTER = 'coupon-puntos';
    const PAIS_DEFAULT = 1;
    const CATEGORIA_DEFAULT = 9;

    #region ObjectTables
    public function getConfiguracionesTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\ConfiguracionesTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosTable');
    }

    public function getOfertaPuntosAtributosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaPuntosAtributosTable');
    }

    public function getUbigeoTable()
    {
        return $this->serviceLocator->get('Application\Model\UbigeoTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\AsignacionTable');
    }

    public function getDeliveryPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\DeliveryPuntosTable');
    }
    #endregion

    public function getUbigeo()
    {
        return $this->identity()['ubigeo'];
    }

    public function setUbigeo($ubigeo)
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['ubigeo'] = $ubigeo;
        $session->offsetSet('storage', $data_user);
    }

    public function __construct()
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');

        if (!isset($data_user['NumeroDocumento'])) {
            header('Location: /');
        }
    }

    public function indexAction()
    {
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
        $slug = $this->params()->fromRoute('slug', null);
        $atributo = $this->params()->fromRoute('id', null);

        $datosCupon = $this->getOfertaPuntosTable()->getCuponOfertaSlug($slug);
        if (!$datosCupon) {
            return $this->redirect()->toRoute('404', array('opt' => $slug));
        } else {
            $datosAtributo = $this->getOfertaPuntosAtributosTable()->getOfertaPuntosAtributos($atributo);
            if ($datosCupon->TipoPrecio == "Split" && !$datosAtributo) {
                return $this->redirect()->toRoute('404', array('opt' => $slug));
            } elseif ($datosCupon->TipoPrecio == "Unico" && $datosAtributo) {
                return $this->redirect()->toRoute('404', array('opt' => $slug));
            }
        }

        $datosOferta = $this->getOfertaPuntosTable()->getOfertaPuntos($datosCupon->idOferta);
        $condicionesDelivery = [
            'descripcion' => $datosOferta->CondicionesDelivery,
            'texto' =>$datosOferta->CondicionesDeliveryTexto,
            'estado' =>$datosOferta->CondicionesDeliveryEstado,
        ];

        $datosDelivery = $this->getDeliveryPuntosTable()->getFormulario($datosCupon->idOferta);
        $requeridos = [];
        foreach ($datosDelivery as $value){
            if($value->Requerido == '1'){
                $requeridos[] = $value->Nombre_Campo;
            }
        }
        $requeridos = $this->generarArreglosJS($requeridos);

        $dni = isset($this->identity()['NumeroDocumento']) ? $this->identity()['NumeroDocumento'] : null;
        $empresa = $this->identity()['Empresa'];
        $img = $this->identity()['logo'];
        $email = isset($this->identity()['email']) ? $this->identity()['email'] : null;
        $idCliente = isset($this->identity()['id']) ? $this->identity()['id'] : null;

        $ubigeo_id = $this->getUbigeo();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $this->setUbigeo($ubigeo_id);
        }

        $ubigeos = $this->getUbigeoTable()->getUbigeo($ubigeo_id);
        $ubigeo = $ubigeos->Nombre;

        $configuraciones = $this->getConfiguracionesTable()->fetchAll();
        $conf = array();
        foreach ($configuraciones as $dat) {
            $conf[$dat->Campo] = $dat->Atributo;
        }

        $config = $this->getServiceLocator()->get('Config');
        $categorias = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $categoriasfooter = $this->getCategoriaTable()->getBuscarCategoriaXPais($this::PAIS_DEFAULT);
        $catotros = $this->getCategoriaTable()->getBuscarCatOtros($this::PAIS_DEFAULT);

        $clienteData = $this->getAsignacionTable()->getPuntosAsignados($dni);

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'delivery/' . $slug,
                'router' => $this::ROUTER,
                'slug' => 'ptos-delivery',
                'category' => 'puntos',
                'meses' => $meses,
                'conf' => $conf,
                'ubigeo' => $ubigeo,
                'rlogos' => $config['images']["logos"],
                'rofertas' => $config['images']["ofertas-puntos"],
                'rgaleria' => $config['images']["galeria"],
                'rbanners' => $config['images']["banners"],
                'ubigeo_id' => $ubigeo_id,
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'categorias' => $categorias,
                'categoriasfooter' => $categoriasfooter,
                'email_user' => $email,
                'clienteID' => $idCliente,
                'empresaID' => $empresa,
                'imgemp' => $img,
                'cupon' => $datosCupon,
                'clienteData' => $clienteData,
                'atributo' => $datosAtributo,
                'delivery' => $datosDelivery,
                'requeridos' => $requeridos,
                'condicionesDelivery' => $condicionesDelivery,
                'catotros' => $catotros
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('application/delivery/index-mobile');
        }
        return $view;
    }

    public function generarArreglosJS($arreglo)
    {
        $temp = "[";
        $contador = 0;
        if (is_array($arreglo)) {
            foreach ($arreglo as $value) {
                $temp = $contador > 0 ? $temp . ", '" . addslashes($value) . "'" : $temp . "'" . addslashes($value) . "'";
                $contador++;
            }
        }
        $temp = $temp . "]";
        $arreglo = $temp;
        return $arreglo;
    }
}
