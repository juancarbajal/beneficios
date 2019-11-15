<?php

namespace Perfil\Controller;

use Application\Service\MenuCategorias;
use Application\Service\Ubigeo;
use DOMPDFModule\View\Model\PdfModel;
use Perfil\Form\PerfilForm;
use Perfil\Model\CuponPuntos;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\EmailAddress;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

use Application\Service\MobileDetect;
use Zend\Session\Container as SessionContainer;

class PerfilPuntosController extends AbstractActionController
{
    const PAIS_DEFAULT = 1;
    const CATEGORIA_DEFAULT = 1;
    const ROUTER = 'perfil-puntos';

    #region ObjectTables
    public function preguntaTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\PreguntasTable');
    }

    public function asignacionPuntosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\AsignacionTable');
    }

    public function cuponPuntosTable()
    {
        return $this->serviceLocator->get('Perfil\Model\Table\CuponPuntosTable');
    }

    #endregion

    public function puntosAction()
    {
        $request = $this->getRequest();

        //Categorias
        $dataCategoria = new MenuCategorias($this->serviceLocator);
        $dataCategoria = $dataCategoria->getDataCategorias($this::PAIS_DEFAULT);
        $config = $this->getServiceLocator()->get('Config');

        //Ubigeo
        $ubigeo = new Ubigeo($this->serviceLocator);
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $ubigeo->setUbigeo($ubigeo_id);
        }
        $nombreUbigeo = $ubigeo->getNombre($this->identity()['ubigeo']);

        $asignacion = $this->asignacionPuntosTable()
            ->getAsignacionForCliente($this->identity()['id'], $this->identity()['Empresa']);

        $dataAsignacion = array();
        foreach ($asignacion as $value) {
            @$dataAsignacion[0] += $value->EstadoPuntos == "Activado" ? $value->CantidadPuntosDisponibles : 0;
            @$dataAsignacion[1] += $value->CantidadPuntos;
            @$dataAsignacion[2] += $value->CantidadPuntosUsados;
        }

        $dataHistoria = $this->cuponPuntosTable()->getHistorial($this->identity()['id'], $this->identity()['Empresa']);
        $arrayHistorial = array();

        foreach ($dataHistoria as $item) {
            $arrayHistorial[] = array(
                "FechaGenerado" => $item['FechaGenerado'],
                "PrecioVentaPublico" => $item['PrecioVentaPublico'],
                "TituloCorto" => $item['TituloCorto'],
                "Descarga" => $item['Descarga'],
                "CantidadPuntos" => $item['CantidadPuntos']
            );
        }

        /*$page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;
        $paginator = new Paginator(new paginatorIterator($dataHistoria));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);*/

        if (!$this->identity()["exist_puntos"]) {
            return $this->redirect()->toRoute('application');
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'perfil',
                'url_slug' => 'perfil',
                'category' => $dataCategoria[3],
                'router' => $this::ROUTER,
                'rlogos' => $config['images']["logos"],
                'imgemp' => $this->identity()['logo'],
                'ubigeo' => $nombreUbigeo,
                'categorias' => $dataCategoria[0],
                'categoriasfooter' => $dataCategoria[1],
                'ubigeo_id' => $this->identity()['ubigeo'],
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'catotros' => $dataCategoria[2],
                'afiliadas' => true, //elimina seccion de empresas afiliadas
                'active' => 'puntos',
                'dataAsignacion' => $dataAsignacion,
                'dataHistoria' => $arrayHistorial
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->mobile = 1;
            $view->setTemplate('perfil/perfil-puntos/puntos-mobile');
        }
        return $view;
    }

    public function descargadosAction()
    {
        $request = $this->getRequest();
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        //Categorias
        $dataCategoria = new MenuCategorias($this->serviceLocator);
        $dataCategoria = $dataCategoria->getDataCategorias($this::PAIS_DEFAULT);
        $config = $this->getServiceLocator()->get('Config');

        //Ubigeo
        $ubigeo = new Ubigeo($this->serviceLocator);
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $ubigeo->setUbigeo($ubigeo_id);
        }
        $nombreUbigeo = $ubigeo->getNombre($this->identity()['ubigeo']);

        $descargados = $this->cuponPuntosTable()->getDescargados($this->identity()['id'], $this->identity()['Empresa']);

        $paginator = new Paginator(new paginatorIterator($descargados, null));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        if (!$this->identity()["exist_puntos"]) {
            return $this->redirect()->toRoute('application');
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'perfil',
                'url_slug' => 'perfil',
                'category' => $dataCategoria[3],
                'router' => $this::ROUTER,
                'rlogos' => $config['images']["logos"],
                'imgemp' => $this->identity()['logo'],
                'ubigeo' => $nombreUbigeo,
                'categorias' => $dataCategoria[0],
                'categoriasfooter' => $dataCategoria[1],
                'ubigeo_id' => $this->identity()['ubigeo'],
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'catotros' => $dataCategoria[2],
                'afiliadas' => true, //elimina seccion de empresas afiliadas
                'active' => 'descargados',
                'descargados' => $paginator
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->mobile = 1;
            $view->setTemplate('perfil/perfil-puntos/descargados-mobile');
        }
        return $view;
    }

    public function vigentesAction()
    {
        $request = $this->getRequest();
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;
        //var_dump($message);exit;
        //Categorias
        $dataCategoria = new MenuCategorias($this->serviceLocator);
        $dataCategoria = $dataCategoria->getDataCategorias($this::PAIS_DEFAULT);
        $config = $this->getServiceLocator()->get('Config');

        //Ubigeo
        $ubigeo = new Ubigeo($this->serviceLocator);
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $ubigeo->setUbigeo($ubigeo_id);
        }
        $nombreUbigeo = $ubigeo->getNombre($this->identity()['ubigeo']);

        $vigentes = $this->cuponPuntosTable()->getVigentes($this->identity()['id'], $this->identity()['Empresa']);

        $paginator = new Paginator(new paginatorIterator($vigentes, null));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        if (!$this->identity()["exist_puntos"]) {
            return $this->redirect()->toRoute('application');
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'perfil',
                'url_slug' => 'perfil',
                'category' => $dataCategoria[3],
                'router' => $this::ROUTER,
                'rlogos' => $config['images']["logos"],
                'imgemp' => $this->identity()['logo'],
                'ubigeo' => $nombreUbigeo,
                'categorias' => $dataCategoria[0],
                'categoriasfooter' => $dataCategoria[1],
                'ubigeo_id' => $this->identity()['ubigeo'],
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'catotros' => $dataCategoria[2],
                'afiliadas' => true, //elimina seccion de empresas afiliadas
                'active' => 'vigentes',
                'vigentes' => $paginator
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->mobile = 1;
            $view->setTemplate('perfil/perfil-puntos/vigentes-mobile');
        }
        return $view;
    }

    public function utilizadosAction()
    {
        $request = $this->getRequest();
        $message = $this->params()->fromRoute('message', null);
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;
        //var_dump($message);exit;
        //Categorias
        $dataCategoria = new MenuCategorias($this->serviceLocator);
        $dataCategoria = $dataCategoria->getDataCategorias($this::PAIS_DEFAULT);
        $config = $this->getServiceLocator()->get('Config');

        //Ubigeo
        $ubigeo = new Ubigeo($this->serviceLocator);
        if ($request->isPost()) {
            $ubigeo_id = $request->getPost()->ubigeo;
            $ubigeo->setUbigeo($ubigeo_id);
        }
        $nombreUbigeo = $ubigeo->getNombre($this->identity()['ubigeo']);

        $utilizados = $this->cuponPuntosTable()->getUtilizados($this->identity()['id'], $this->identity()['Empresa']);

        $paginator = new Paginator(new paginatorIterator($utilizados, null));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        if (!$this->identity()["exist_puntos"]) {
            return $this->redirect()->toRoute('application');
        }

        $view = new ViewModel();
        $view->setVariables(
            array(
                'url' => 'perfil',
                'url_slug' => 'perfil',
                'category' => $dataCategoria[3],
                'router' => $this::ROUTER,
                'rlogos' => $config['images']["logos"],
                'imgemp' => $this->identity()['logo'],
                'ubigeo' => $nombreUbigeo,
                'categorias' => $dataCategoria[0],
                'categoriasfooter' => $dataCategoria[1],
                'ubigeo_id' => $this->identity()['ubigeo'],
                'categoria_id' => $this::CATEGORIA_DEFAULT,
                'catotros' => $dataCategoria[2],
                'afiliadas' => true, //elimina seccion de empresas afiliadas
                'active' => 'utilizados',
                'utilizados' => $paginator
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('perfil/perfil-puntos/utilizados-mobile');
        }
        return $view;
    }

    public function pdfPuntosAction()
    {
        $asigancion = $this->asignacionPuntosTable()
            ->getAsignacionForCliente($this->identity()['id'], $this->identity()['Empresa']);

        $dataAsignacion = array();
        foreach ($asigancion as $value) {
            @$dataAsignacion[0] += $value->CantidadPuntosDisponibles;
            @$dataAsignacion[1] += $value->CantidadPuntos;
            @$dataAsignacion[2] += $value->CantidadPuntosUsados;
        }
        $data = $this->cuponPuntosTable()->getHistorial($this->identity()['id'], $this->identity()['Empresa']);

        $pdf = new PdfModel();
        $pdf->setOption('filename', 'Puntos');
        $pdf->setOption('paperSize', 'a4');
        $pdf->setOption('paperOrientation', 'portrait');

        $pdf->setVariables(array(
            'data' => $data,
            'dataAsignacion' => $dataAsignacion
        ));

        return $pdf;
    }

    public function pdfDescargadosAction()
    {
        $data = $this->cuponPuntosTable()->getDescargados($this->identity()['id'], $this->identity()['Empresa']);

        $pdf = new PdfModel();
        $pdf->setOption('filename', 'Descargados');
        $pdf->setOption('paperSize', 'a4');
        $pdf->setOption('paperOrientation', 'portrait');

        $pdf->setVariables(array(
            'data' => $data
        ));

        return $pdf;
    }

    public function pdfVigentesAction()
    {
        $data = $this->cuponPuntosTable()->getVigentes($this->identity()['id'], $this->identity()['Empresa']);

        $pdf = new PdfModel();
        $pdf->setOption('filename', 'Vigentes');
        $pdf->setOption('paperSize', 'a4');
        $pdf->setOption('paperOrientation', 'portrait');

        $pdf->setVariables(array(
            'data' => $data
        ));

        return $pdf;
    }

    public function pdfUtilizadosAction()
    {
        $data = $this->cuponPuntosTable()->getUtilizados($this->identity()['id'], $this->identity()['Empresa']);

        $pdf = new PdfModel();
        $pdf->setOption('filename', 'Utilizados');
        $pdf->setOption('paperSize', 'a4');
        $pdf->setOption('paperOrientation', 'portrait');

        $pdf->setVariables(array(
            'data' => $data
        ));

        return $pdf;
    }
}
