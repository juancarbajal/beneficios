<?php

namespace Usuario\Controller;

use Usuario\Model\Filter\UsuarioFilter;
use Usuario\Model\Usuario;
use Usuario\Form\BuscarUsuarioForm;
use Usuario\Form\UsuarioForm;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class UsuarioController extends AbstractActionController
{
    const Tipo_DNI = 1;
    const Tipo_PASAPORTE = 2;
    const Tipo_OTROS = 3;

    #region ObjectTables
    public function getTipoUsuarioTable()
    {
        return $this->serviceLocator->get('Usuario\Model\TipoUsuarioTable');
    }

    public function getTipoDocumentoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\TipoDocumentoTable');
    }

    public function getUsuarioTable()
    {
        return $this->serviceLocator->get('Usuario\Model\Table\UsuarioTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    #endregion

    public function getTipoUsuario()
    {
        $tipo = array();
        $datos = $this->getTipoUsuarioTable()->fetchAll();
        foreach ($datos as $dato) {
            $tipo[$dato->id] = $dato->Nombre;
        }
        return $tipo;
    }

    public function getTipoDocumento()
    {
        $tipodoc[1] = '';
        $datosdoc = $this->getTipoDocumentoTable()->fetchAll();
        foreach ($datosdoc as $dato) {
            $tipodoc[$dato->id] = $dato->Nombre;
        }
        return $tipodoc;
    }

    public function validatedocumento($dato)
    {
        if (preg_match("/^\d{8}$/", $dato)) {
            return true;
        } else {
            return false;
        }
    }

    public function extraerEmpresaProv()
    {
        $cbxEmpresa = array();
        try {
            $datose = $this->getEmpresaTable()->getEmpresaProv();
            foreach ($datose as $dato) {
                $cbxEmpresa[$dato->id] = $dato->NombreComercial . ' - ' .
                    $dato->RazonSocial . ' - ' . $dato->Ruc;
            }
        } catch (\Exception $ex) {
            return $cbxEmpresa;
        }
        return $cbxEmpresa;
    }

    public function extraerEmpresaCli()
    {
        $cbxEmpresa = array();
        try {
            $datose = $this->getEmpresaTable()->getEmpresaCli();
            foreach ($datose as $dato) {
                $cbxEmpresa[$dato->id] = $dato->NombreComercial . ' - ' .
                    $dato->RazonSocial . ' - ' . $dato->Ruc;
            }
        } catch (\Exception $ex) {
            return $cbxEmpresa;
        }
        return $cbxEmpresa;
    }

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $busqueda = array(
            'TipoUsuario' => 'NombreTipoUsuario',
            'Nombres' => 'Nombres',
            'Apellidos' => 'Apellidos',
            'Usuario' => 'NombreUsuario',
            'Correo' => 'Correo',
            'Documento' => 'NumeroDocumento',
            'Activo' => 'Eliminado',
        );

        $form = new BuscarUsuarioForm();

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : null;
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : 'desc';
        $page = $this->params()->fromRoute('page') ? (int)$this->params()->fromRoute('page') : 1;
        $itemsPerPage = 10;

        if (array_key_exists($order_by, $busqueda)) {
            $order_by_o = $order_by;
            $order_by = $busqueda[$order_by];
        } else {
            $order_by_o = 'id';
            $order_by = 'FechaCreacion';
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $usuario = new Usuario();
            $usuario->Nombres = $request->getPost()->Nombres;
            $nombre = (!empty($request->getPost()->Nombres))
                ? $request->getPost()->Nombres : null;
            $form->bind($usuario);
            $usuario = $this->getUsuarioTable()->getUsuarioDetail($nombre, $order_by, $order);
        } else {
            $nombre = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $form->setData(array("Nombres" => $nombre));
            $usuario = $this->getUsuarioTable()->getUsuarioDetail($nombre, $order_by, $order);
        }

        $paginator = new Paginator(new paginatorIterator($usuario, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        return new ViewModel(
            array(
                'usuario' => 'active',
                'ulistar' => 'active',
                'form' => $form,
                'datos' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'q1' => $nombre,
                'p' => $page,
            )
        );
    }

    public function addAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $alert = 'danger';
        $msg = null;
        $errors = null;

        $form = new UsuarioForm($this->getTipoUsuario(), $this->getTipoDocumento());
        $request = $this->getRequest();

        if ($request->isPost()) {
            /////validacion documento y email unicos
            $correo = $this->getUsuarioTable()->getCorreo($request->getPost()->Correo);
            $doc = $this->getUsuarioTable()->getDocumento($request->getPost()->NumeroDocumento);
            if ($correo == 1) {
                $errors['correom'] = 'El Correo esta en uso';
                $errors['correoc'] = 'has-error';
            }
            if ($doc == 1) {
                $errors['ndocm'] = 'El Número de Documento esta en uso';
                $errors['ndocc'] = 'has-error';
            }
            /////validacion del limite de caracteres segun el tipo de documento
            $limit = 8;
            $tipodoc = $request->getPost()->BNF_TipoDocumento_id;
            if ($tipodoc == $this::Tipo_DNI) {
                $limit = 8;
            } elseif ($tipodoc == $this::Tipo_PASAPORTE) {
                $limit = 15;
            } elseif ($tipodoc == $this::Tipo_OTROS) {
                $limit = 5;
            }

            //Agregando filtro
            $tipousu = $request->getPost()->BNF_TipoUsuario_id;
            $empresasFilter = null;
            if ($tipousu == 6) {
                $empresasFilter = $this->extraerEmpresaProv();
            } elseif ($tipousu == 7 || $tipousu == 8) {
                $empresasFilter = $this->extraerEmpresaCli();
            }

            $filter = new UsuarioFilter();
            $form->setInputFilter(
                $filter->getInputFilter(
                    $tipousu,
                    $tipodoc,
                    $limit,
                    $this->getTipoUsuario(),
                    $this->getTipoDocumento(),
                    $empresasFilter
                )
            );

            $form->setData($request->getPost());
            if ($form->isValid() && $errors == null) {
                $usuario = new Usuario();
                $usuario->exchangeArray($form->getData());
                $usuario->Eliminado = 0;
                $this->getUsuarioTable()->saveUsuario($usuario);

                $alert = 'success';
                $msg[] = 'Usuario Registrado Correctamente';
                $form = new UsuarioForm(
                    $this->getTipoUsuario(),
                    $this->getTipoDocumento(),
                    $this->extraerEmpresaProv()
                );
            }
        }

        return new ViewModel(
            array(
                'usuario' => 'active',
                'uadd' => 'active',
                'alert' => $alert,
                'msg' => $msg,
                'form' => $form,
                'errors' => $errors,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $msg = null;
        $errors = null;
        ////////recepciona id del usuario enviado por post
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('usuario', array('action' => 'add'));
        }
        try {
            $usuario = $this->getUsuarioTable()->getUsuario($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('usuario', array('action' => 'index'));
        }

        $form = new UsuarioForm($this->getTipoUsuario(), $this->getTipoDocumento());
        $form->get('submit')->setAttribute('value', 'Editar');
        $form->bind($usuario);

        $request = $this->getRequest();
        if ($request->isPost()) {
            /////validacion del limite de caracteres segun el tipo de documento
            $limit = 8;
            $tipodoc = $request->getPost()->BNF_TipoDocumento_id;
            if ($tipodoc == $this::Tipo_DNI) {
                $limit = 8;
            } elseif ($tipodoc == $this::Tipo_PASAPORTE) {
                $limit = 15;
            } elseif ($tipodoc == $this::Tipo_OTROS) {
                $limit = 5;
            }

            //Agregando filtro
            $tipousu = $request->getPost()->BNF_TipoUsuario_id;
            $empresasFilter = null;
            if ($tipousu == 6) {
                $empresasFilter = $this->extraerEmpresaProv();
            } elseif ($tipousu == 7 || $tipousu == 8) {
                $empresasFilter = $this->extraerEmpresaCli();
            }

            $filter = new UsuarioFilter();
            $form->setInputFilter(
                $filter->getInputFilterE(
                    $tipousu,
                    $tipodoc,
                    $limit,
                    $this->getTipoUsuario(),
                    $this->getTipoDocumento(),
                    $empresasFilter
                )
            );
            /////validacion documento y email unicos
            $correo = $this->getUsuarioTable()->getCorreoId($request->getPost()->Correo, $id);
            $doc = $this->getUsuarioTable()->getDocumentoId($request->getPost()->NumeroDocumento, $id);
            if ($correo == 1) {
                $errors['correom'] = 'El Correo esta en uso';
                $errors['correoc'] = 'has-error';
            }
            if ($doc == 1) {
                $errors['ndocm'] = 'El Número de Documento esta en uso';
                $errors['ndocc'] = 'has-error';
            }

            $form->setData($request->getPost());
            if ($form->isValid() && $errors == null) {
                $this->getUsuarioTable()->saveUsuario($usuario);
                $this->flashMessenger()->addMessage('Usuario Modificado Correctamente');
                return $this->redirect()->toRoute('usuario');
            }
        }

        return new ViewModel(
            array(
                'usuario' => 'active',
                'ulistar' => 'active',
                'msg' => $msg,
                'id' => $id,
                'form' => $form,
                'errors' => $errors,
            )
        );
    }

    public function deleteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $val = $this->getRequest()->getPost('val');
        $id = $this->getRequest()->getPost('id');
        $this->getUsuarioTable()->deleteUsuario($id, $val);
        return json_encode(array('status' => 200));
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $resultado = $this->getUsuarioTable()->getUsuarioDetail('null', '');
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();

        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Usuarios del Sistema")
                ->setSubject("Usuarios")
                ->setDescription("Documento listando las Usuarios")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Usuarios");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:M' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $styleArray2 = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:M' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Tipo Usuario')
                ->setCellValue('C1', 'Tipo Documento')
                ->setCellValue('D1', 'Nombres')
                ->setCellValue('E1', 'Apellidos')
                ->setCellValue('F1', 'Nombre Usuario')
                ->setCellValue('G1', 'Contraseña')
                ->setCellValue('H1', 'Numero Documento')
                ->setCellValue('I1', 'Correo')
                ->setCellValue('J1', 'Fecha Creacion')
                ->setCellValue('K1', 'Fecha Actualizacion')
                ->setCellValue('L1', 'Fecha Ultimo Acceso')
                ->setCellValue('M1', 'Eliminado');
            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->NombreTipoUsuario)
                    ->setCellValue('C' . $i, $registro->NombreTipoDocumento)
                    ->setCellValue('D' . $i, $registro->Nombres)
                    ->setCellValue('E' . $i, $registro->Apellidos)
                    ->setCellValue('F' . $i, $registro->NombreUsuario)
                    ->setCellValue('G' . $i, $registro->Contrasenia)
                    ->setCellValue('H' . $i, $registro->NumeroDocumento)
                    ->setCellValue('I' . $i, $registro->Correo)
                    ->setCellValue('J' . $i, $registro->FechaCreacion)
                    ->setCellValue('K' . $i, $registro->FechaActualizacion)
                    ->setCellValue('L' . $i, $registro->FechaUltimoAcceso)
                    ->setCellValue('M' . $i, $registro->Eliminado);
                $i++;

            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Usuarios.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function getEmpresasAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $tipo_usuario = (int)$this->getRequest()->getPost('value');
            $nombre_usuario = $this->getRequest()->getPost('text');

            if ($tipo_usuario == 6 and $nombre_usuario == "Proveedor") {
                $empresas = $this->extraerEmpresaProv();
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'empresas' => $empresas
                        )
                    )
                );
            } elseif (($tipo_usuario == 7 and $nombre_usuario == "Cliente") || ($tipo_usuario == 8 and $nombre_usuario == "Verisure")) {
                $empresas = $this->extraerEmpresaCli();
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'empresas' => $empresas
                        )
                    )
                );
            } else {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'empresas' => null
                        )
                    )
                );
            }
        }
        return $response;
    }
}
