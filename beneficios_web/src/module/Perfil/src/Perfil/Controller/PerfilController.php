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

class PerfilController extends AbstractActionController
{
    const PAIS_DEFAULT = 1;
    const CATEGORIA_DEFAULT = 1;
    const ROUTER = 'perfil';

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

    public function indexAction()
    {
        $request = $this->getRequest();
        $message = null;
        $type = 'danger';
        $nombres = "";
        $apellidos = "";
        $telefono = "";
        $correo = "";

        //Categorias
        $dataCategoria = new MenuCategorias($this->serviceLocator);
        $dataCategoria = $dataCategoria->getDataCategorias($this::PAIS_DEFAULT);
        $config = $this->getServiceLocator()->get('Config');

        //Perfil
        $cliente = $this->preguntaTable()->getPerfil($this->identity()['id']);
        $form = new PerfilForm();
        if ($cliente) {
            $form->bind($cliente);
            $nombres = $cliente->Nombre;
            $apellidos = $cliente->Apellido;
            $telefono = $cliente->Telefono;
            $correo = $cliente->Correo;
        }

        //Ubigeo
        $ubigeo = new Ubigeo($this->serviceLocator);
        if ($request->isPost()) {
            if (isset($request->getPost()->ubigeo)) {
                $ubigeo_id = $request->getPost()->ubigeo;
                $ubigeo->setUbigeo($ubigeo_id);
            } else {
                $id = $this->identity()['id'];
                $data['Pregunta01'] = trim($request->getPost()->Nombre);
                $data['Pregunta02'] = trim($request->getPost()->Apellido);
                $data['Pregunta09'] = trim($request->getPost()->Telefono);
                $data['FechaPregunta01'] = date('Y-m-d H:i:s');
                $data['FechaPregunta02'] = date('Y-m-d H:i:s');
                $data['FechaPregunta09'] = date('Y-m-d H:i:s');

                if ($request->getPost()->Correo) {
                    $valid = new EmailAddress(array('domain' => false));
                    $valid->isValid($request->getPost()->Correo);
                    $message = ($valid->getMessages()) ? 'formato de correo invalido' : $message;
                }
                if ($request->getPost()->Telefono) {
                    $valid = new Regex(array('pattern' => '/^(\([0-9]+\))?[0-9]+-?[0-9]+$/'));
                    $valid->isValid($request->getPost()->Telefono);
                    $message = ($valid->getMessages()) ? 'formato de teléfono invalido' : $message;
                }

                if (!$data['Pregunta01']) {
                    unset($data['Pregunta01']);
                    unset($data['FechaPregunta01']);
                }
                if (!$data['Pregunta02']) {
                    unset($data['Pregunta02']);
                    unset($data['FechaPregunta02']);
                }
                if (!$data['Pregunta09']) {
                    unset($data['Pregunta09']);
                    unset($data['FechaPregunta09']);
                    if (!empty($telefono)) {
                        $message = $message . "<p>El campo teléfono no puede quedar vacío</p>";
                        $type = 'warning';
                    }
                }

                if (!$data && $request->getPost()->Correo == '') {
                    $message = 'No ha ingresado datos para actualizar';
                    $type = 'info';
                }

                if (!$message) {
                    if ($data) {
                        $this->preguntaTable()->saveRespuestas($id, $data);
                    }

                    if($request->getPost()->Correo != '') {
                        $correosTable = $this->serviceLocator->get('Auth\Model\Table\ClienteCorreoTable');
                        if (@$correosTable->getUltimoCorreo($id)->Correo != $request->getPost()->Correo &&
                            $request->getPost()->Correo
                        ) {
                            $correosTable->saveCorreo($id, $request->getPost()->Correo);
                        }
                    }

                    $session = new SessionContainer('auth');
                    $data_user = $session->offsetGet('storage');
                    if(@$data['Pregunta01'])
                        $data_user['Nombre'] = @$data['Pregunta01'];
                    if(@$data['Pregunta02'])
                        $data_user['Apellido'] = @$data['Pregunta02'];
                    $session->offsetSet('storage', $data_user);

                    $message = 'Datos Editados Exitosamente!';
                    $type = 'success';

                    $cliente = $this->preguntaTable()->getPerfil($this->identity()['id']);
                    $form->bind($cliente);
                }
            }
        }

        $nombreUbigeo = $ubigeo->getNombre($this->identity()['ubigeo']);

        $asignacion = $this->asignacionPuntosTable()
            ->getAsignacionForCliente($this->identity()['id'], $this->identity()['Empresa']);

        $totalPuntos = 0;
        foreach ($asignacion as $value) {
            $totalPuntos += $value->CantidadPuntosDisponibles;
        }

        if ((!$this->identity()["exist_puntos"] and !$this->identity()["have_asignacion"])
            && (!$this->identity()["exist_premios"] and !$this->identity()["have_asignacion_premios"])
        ) {
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
                'form' => $form,
                'afiliadas' => true, //elimina seccion de empresas afiliadas
                'active' => 'datos',
                'message' => $message,
                'type' => $type,
                'puntos' => $totalPuntos
            )
        );

        $mobile = new MobileDetect();
        if ($mobile->isMobile() == 1) {
            $view->setTemplate('perfil/perfil/index-mobile');
        }
        return $view;
    }
}
