<?php

namespace Ordenamiento\Controller;

use Ordenamiento\Form\AssignForm;
use Ordenamiento\Form\OrdenamientoForm;
use Ordenamiento\Model\Filter\AssignFilter;
use Ordenamiento\Model\Filter\OrdenamientoFilter;
use Ordenamiento\Model\LayoutCampania;
use Ordenamiento\Model\LayoutCampaniaPosicion;
use Ordenamiento\Model\LayoutCategoria;
use Ordenamiento\Model\LayoutCategoriaPosicion;
use Ordenamiento\Model\LayoutPremios;
use Ordenamiento\Model\LayoutPremiosPosicion;
use Ordenamiento\Model\LayoutPuntos;
use Ordenamiento\Model\LayoutPuntosPosicion;
use Ordenamiento\Model\LayoutTienda;
use Ordenamiento\Model\LayoutTiendaPosicion;
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

class OrdenamientoController extends AbstractActionController
{
    const USUARIO_CLIENTE = 7;
    const TYPE_CATEGORIA = 'categoria';
    const TYPE_CAMPANIA = 'campania';
    const TYPE_TIENDA = 'tienda';
    const TYPE_PUNTOS = 'puntos';
    const TYPE_PREMIOS = 'premios';

    #region ObjectTables
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    public function getLayoutCampaniaPosicionTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutCampaniaPosicionTable');
    }

    public function getLayoutCategoriaPosicionTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutCategoriaPosicionTable');
    }

    public function getLayoutTiendaPosicionTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutTiendaPosicionTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getLayoutPuntosPosicionTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutPuntosPosicionTable');
    }

    public function getOfertaPuntosTable()
    {
        return $this->serviceLocator->get('Puntos\Model\Table\OfertaPuntosTable');
    }

    public function getLayoutPremiosPosicionTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutPremiosPosicionTable');
    }

    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getGaleriasTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\GaleriaTable');
    }

    public function getBannersTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannerTable');
    }

    public function getBannersCategoriaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannersCategoriaTable');
    }

    public function getBannersCampaniaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannersCampaniasTable');
    }

    public function getBannersTiendaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\BannersTiendaTable');
    }

    public function getOrdenamientoTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\OrdenamientoTable');
    }

    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaTable');
    }

    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaTable');
    }

    public function getLayoutCategoriaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutCategoriaTable');
    }

    public function getLayoutCampaniaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutCampaniaTable');
    }

    public function getLayoutTiendaTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutTiendaTable');
    }

    public function getLayoutPuntosTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutPuntosTable');
    }

    public function getLayoutPremiosTable()
    {
        return $this->serviceLocator->get('Ordenamiento\Model\Table\LayoutPremiosTable');
    }

    #endregion

    #region Inicializacion
    public function extraerCategoria()
    {
        $cboCategoria = array();
        try {
            $datos = $this->getCategoriaTable()->fetchAll();
            foreach ($datos as $dato) {
                $cboCategoria[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cboCategoria;
        }
        return $cboCategoria;
    }

    public function extraerCampania()
    {
        $cboCampania = array();
        try {
            $datos = $this->getCampaniaTable()->fetchAll();
            foreach ($datos as $dato) {
                $cboCampania[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cboCampania;
        }
        return $cboCampania;
    }

    public function extraerOrdenamiento()
    {
        $cboOrdenamiento = array();
        try {
            $datos = $this->getOrdenamientoTable()->fetchAll();
            foreach ($datos as $dato) {
                $cboOrdenamiento[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cboOrdenamiento;
        }
        return $cboOrdenamiento;
    }

    public function extraerOfertas()
    {
        $cbxOfertas = array();
        try {
            $datos = $this->getOfertaTable()->fetchAll();
            foreach ($datos as $dato) {
                $cbxOfertas[$dato->id] = $dato->Nombre;
            }
        } catch (\Exception $ex) {
            return $cbxOfertas;
        }
        return $cbxOfertas;
    }

    public function extraerEmpresa()
    {
        $empresas = array();
        try {
            $dataEmpresas = $this->getEmpresaTable()->getEmpresaCli();
            $empresas["all"] = "Listar Todos";
            foreach ($dataEmpresas as $e) {
                $empresas[$e->id] = $e->NombreComercial . " (" . $e->RazonSocial . ") - " . $e->Ruc;
            }
        } catch (\Exception $ex) {
            $empresas = array();
        }
        return $empresas;
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($this->identity()->BNF_TipoUsuario_id == 4) {
            return $this->redirect()->toRoute('ordenamiento', array('action' => 'assign'));
        }

        $busqueda = array(
            'NombreLayout' => 'NombreLayout',
            'Tipo' => 'Tipo',
            'NombreTipo' => 'NombreTipo',
            'Fila' => 'Index',
            'Activo' => 'Eliminado',
        );

        $nombre = null;

        $form = new OrdenamientoForm('ordenamiento', 'Buscar');
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

        if (strcasecmp($order, "desc") == 0) {
            $order = "asc";
        } else {
            $order = "desc";
        }

        $request = $this->getRequest();

        if ($request->isPost()) {

            $post = $this->getRequest()->getPost()->toArray();
            $validate = new OrdenamientoFilter($post);
            $form->setInputFilter(
                $validate->getInputFilter($post)
            );

            $form->setData($post);

            if ($form->isValid()) {
                $nombre = (!empty($request->getPost()->Nombre)) ? $request->getPost()->Nombre : null;
            }
        } else {
            $nombre = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : null;
            $form->setData(array("Nombre" => str_replace('-', ' ', $nombre)));
        }

        $ordenamientolist = $this->getOrdenamientoTable()
            ->getOrdenamientoDetails(str_replace('-', ' ', $nombre), $order_by, $order);

        if (count($ordenamientolist) >= 1 && $nombre != null) {
            $nombre = str_replace(' ', '-', $nombre);
        } else {
            $nombre = null;
        }

        $paginator = new Paginator(new paginatorIterator($ordenamientolist, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        $pagesInRange = array();

        $pageCount = (int)$paginator->count();
        for ($i = 1; $i <= $pageCount; $i++) {
            $pagesInRange[$i] = $i;
        }
        return new ViewModel(
            array(
                'ordenamiento' => 'active',
                'orlistar' => 'active',
                'form' => $form,
                'datos' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'q1' => $nombre,
                'p' => $page,
                'pageCount' => $pageCount,
                'pagesInRange' => $pagesInRange
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($this->identity()->BNF_TipoUsuario_id == 4) {
            return $this->redirect()->toRoute('ordenamiento', array('action' => 'assign'));
        }

        $type = null;
        $index = array();
        $id = (int)$this->params()->fromRoute('id', 0);
        $val = $this->params()->fromRoute('val', 0);
        $form = new AssignForm($this->extraerCategoria(), $this->extraerCampania(), $this->extraerOrdenamiento());
        if (!$id) {
            return $this->redirect()->toRoute('ordenamiento', array('action' => 'add'));
        }
        try {
            if ($val == 'Categoría') {
                $layoutCategoria = $this->getLayoutCategoriaTable()->getLayoutCategoria($id);
                $form->bind($layoutCategoria);
                $form->get('type')->setAttribute('value', 'categoria');
                $indexs = $this->getLayoutCategoriaTable()->fetchAllIndex($layoutCategoria->BNF_Categoria_id);
                foreach ($indexs as $dato) {
                    $index[$dato->Index] = $dato->BNF_Layout_id;
                }
                $form->get('Fila_1')->setAttribute('value', $index[1]);
                $form->get('Fila_2')->setAttribute('value', $index[2]);
                $form->get('Fila_3')->setAttribute('value', $index[3]);
                $type = 'categoria';
            } elseif ($val == 'Campaña') {
                $layoutCampania = $this->getLayoutCampaniaTable()->getLayoutCampania($id);
                $form->bind($layoutCampania);
                $form->get('type')->setAttribute('value', 'campania');
                $indexs = $this->getLayoutCampaniaTable()->fetchAllIndex($layoutCampania->BNF_Campanias_id);
                foreach ($indexs as $dato) {
                    $index[$dato->Index] = $dato->BNF_Layout_id;
                }
                $form->get('Fila_1')->setAttribute('value', $index[1]);
                $form->get('Fila_2')->setAttribute('value', $index[2]);
                $form->get('Fila_3')->setAttribute('value', $index[3]);
                $type = 'campania';
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('ordenamiento', array('action' => 'index'));
        }

        $form->get('submit')->setAttribute('value', 'Editar');

        return new ViewModel(
            array(
                'ordenamiento' => 'active',
                'orassign' => 'active',
                'form' => $form,
                'type' => $type
            )
        );
    }

    public function deleteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($this->identity()->BNF_TipoUsuario_id == 4) {
            return $this->redirect()->toRoute('ordenamiento', array('action' => 'assign'));
        }

        $type = $this->getRequest()->getPost('type');
        $val = $this->getRequest()->getPost('val');
        $id = $this->getRequest()->getPost('id');

        if ($type == 'Categoría') {
            $this->getLayoutCategoriaTable()->deleteLayoutCategoria($id, $val);

        } elseif ($type == 'Campaña') {
            $this->getLayoutCampaniaTable()->deleteLayoutCampania($id, $val);

        }
        return json_encode(array('status' => 500));
    }

    public function exportAction()
    {
        $identity = $this->identity();

        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($this->identity()->BNF_TipoUsuario_id == 4) {
            return $this->redirect()->toRoute('ordenamiento', array('action' => 'assign'));
        }

        $resultado = $this->getOrdenamientoTable()->getReport();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Ordenamientos del Sistema")
                ->setSubject("Ordenamientos")
                ->setDescription("Documento listando las Ordenamientos")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Ordenamientos");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:H' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:H' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre del Ordenamiento')
                ->setCellValue('C1', 'Tipo')
                ->setCellValue('D1', 'Nombre del Tipo')
                ->setCellValue('E1', 'Fila')
                ->setCellValue('F1', 'Fecha Creación')
                ->setCellValue('G1', 'Fecha Actualización')
                ->setCellValue('H1', 'Eliminado');
            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->NombreLayout)
                    ->setCellValue('C' . $i, $registro->Tipo)
                    ->setCellValue('D' . $i, $registro->NombreTipo)
                    ->setCellValue('E' . $i, $registro->Index)
                    ->setCellValue('F' . $i, $registro->FechaCreacion)
                    ->setCellValue('G' . $i, $registro->FechaActualizacion)
                    ->setCellValue('H' . $i, ((int)$registro->Eliminado == 0) ? 'Activo' : 'Inactivo');
                $i++;

            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Ordenamientos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function assignAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $type = null;
        $nombre_empresa = null;
        $lista_oertas = array();
        $opcion = null;

        $tipo_usuario = $this->identity()->BNF_TipoUsuario_id;
        $empresa_value = $this->identity()->BNF_Empresa_id;
        if ($tipo_usuario == $this::USUARIO_CLIENTE) {
            $nombre_empresa = $this->extraerEmpresa()[$empresa_value];
            $form = new AssignForm(
                $this->extraerCategoria(),
                $this->extraerCampania(),
                $this->extraerOrdenamiento(),
                $empresa_value,
                $tipo_usuario
            );
        } else {
            $form = new AssignForm(
                $this->extraerCategoria(),
                $this->extraerCampania(),
                $this->extraerOrdenamiento(),
                $this->extraerEmpresa()
            );
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $layoutCampania = new LayoutCampania();
            $layoutCategoria = new LayoutCategoria();
            $layoutTienda = new LayoutTienda();
            $layoutPuntos = new LayoutPuntos();
            $layoutPremios = new LayoutPremios();
            $filter = new AssignFilter();
            $type = $request->getPost()->type;
            $form->setInputFilter(
                $filter->getInputFilter(
                    $this->extraerCategoria(),
                    $this->extraerCampania(),
                    $this->extraerOrdenamiento(),
                    $this->extraerOfertas(),
                    $this->extraerEmpresa(),
                    $type
                )
            );

            $form->setData($request->getPost());

            if ($form->isValid()) {
                if ($request->getPost()->type == $this::TYPE_CAMPANIA) {
                    for ($i = 0; $i < 3; $i++) {
                        $layoutCampania->exchangeArray($form->getData());
                        $layoutCampania->Index = $i + 1;
                        $ofertas = array();
                        if ($i == 0) {
                            $layoutCampania->BNF_Layout_id = $request->getPost()->Fila_1;
                            ($request->getPost()->Fila_1_1 != '') ? $ofertas[1] = $request->getPost()->Fila_1_1 : null;
                            ($request->getPost()->Fila_1_2 != '') ? $ofertas[2] = $request->getPost()->Fila_1_2 : null;
                            ($request->getPost()->Fila_1_3 != '') ? $ofertas[3] = $request->getPost()->Fila_1_3 : null;
                        } elseif ($i == 1) {
                            $layoutCampania->BNF_Layout_id = $request->getPost()->Fila_2;
                            ($request->getPost()->Fila_2_1 != '') ? $ofertas[1] = $request->getPost()->Fila_2_1 : null;
                            ($request->getPost()->Fila_2_2 != '') ? $ofertas[2] = $request->getPost()->Fila_2_2 : null;
                            ($request->getPost()->Fila_2_3 != '') ? $ofertas[3] = $request->getPost()->Fila_2_3 : null;
                        } elseif ($i == 2) {
                            $layoutCampania->BNF_Layout_id = $request->getPost()->Fila_3;
                            ($request->getPost()->Fila_3_1 != '') ? $ofertas[1] = $request->getPost()->Fila_3_1 : null;
                            ($request->getPost()->Fila_3_2 != '') ? $ofertas[2] = $request->getPost()->Fila_3_2 : null;
                            ($request->getPost()->Fila_3_3 != '') ? $ofertas[3] = $request->getPost()->Fila_3_3 : null;
                        }

                        $empresa_id = (int)$request->getPost()->empresa;
                        $data = $this->getLayoutCampaniaTable()
                            ->getLayoutCampaniaDetails(
                                $request->getPost()->BNF_Campanias_id,
                                $i + 1,
                                $empresa_id
                            );

                        if (is_object($data)) {
                            $layoutCampania->id = $data->id;
                        } else {
                            $layoutCampania->id = null;
                        }

                        if ($empresa_id > 0) {
                            $layoutCampania->BNF_Empresa_id = $empresa_id;
                        }

                        $layout_id = $this->getLayoutCampaniaTable()->saveLayoutCampania($layoutCampania);

                        $this->getLayoutCampaniaPosicionTable()->desactivarOfertas((int)$layout_id);
                        foreach ($ofertas as $key => $data) {
                            $layoutCampaniaP = new LayoutCampaniaPosicion();
                            $layoutCampaniaP->BNF_LayoutCampania_id = $layout_id;
                            $layoutCampaniaP->BNF_Oferta_id = $data;
                            $layoutCampaniaP->Index = $key;
                            $dato = $this->getLayoutCampaniaPosicionTable()->getLayoutCampaniaPosicionDetails(
                                (int)$layout_id,
                                (int)$data
                            );
                            if ($dato !== false) {
                                $layoutCampaniaP->id = $dato->id;
                            }
                            $this->getLayoutCampaniaPosicionTable()->saveLayoutCampaniaPosicion($layoutCampaniaP);
                        }
                    }
                } elseif ($request->getPost()->type == $this::TYPE_CATEGORIA) {
                    for ($i = 0; $i < 3; $i++) {
                        $layoutCategoria->exchangeArray($form->getData());
                        $layoutCategoria->Index = $i + 1;
                        $ofertas = array();
                        if ($i == 0) {
                            $layoutCategoria->BNF_Layout_id = $request->getPost()->Fila_1;
                            ($request->getPost()->Fila_1_1 != '') ? $ofertas[1] = $request->getPost()->Fila_1_1 : null;
                            ($request->getPost()->Fila_1_2 != '') ? $ofertas[2] = $request->getPost()->Fila_1_2 : null;
                            ($request->getPost()->Fila_1_3 != '') ? $ofertas[3] = $request->getPost()->Fila_1_3 : null;
                        } elseif ($i == 1) {
                            $layoutCategoria->BNF_Layout_id = $request->getPost()->Fila_2;
                            ($request->getPost()->Fila_2_1 != '') ? $ofertas[1] = $request->getPost()->Fila_2_1 : null;
                            ($request->getPost()->Fila_2_2 != '') ? $ofertas[2] = $request->getPost()->Fila_2_2 : null;
                            ($request->getPost()->Fila_2_3 != '') ? $ofertas[3] = $request->getPost()->Fila_2_3 : null;
                        } elseif ($i == 2) {
                            $layoutCategoria->BNF_Layout_id = $request->getPost()->Fila_3;
                            ($request->getPost()->Fila_3_1 != '') ? $ofertas[1] = $request->getPost()->Fila_3_1 : null;
                            ($request->getPost()->Fila_3_2 != '') ? $ofertas[2] = $request->getPost()->Fila_3_2 : null;
                            ($request->getPost()->Fila_3_3 != '') ? $ofertas[3] = $request->getPost()->Fila_3_3 : null;
                        }
                        $empresa_id = (int)$request->getPost()->empresa;
                        $data = $this->getLayoutCategoriaTable()
                            ->getLayoutCategoriaDetails(
                                $request->getPost()->BNF_Categoria_id,
                                $i + 1,
                                $empresa_id
                            );

                        if (is_object($data)) {
                            $layoutCategoria->id = $data->id;
                        } else {
                            $layoutCategoria->id = null;
                        }

                        if ($empresa_id > 0) {
                            $layoutCategoria->BNF_Empresa_id = $empresa_id;
                        }
                        $layout_id = $this->getLayoutCategoriaTable()->saveLayoutCategoria($layoutCategoria);

                        $this->getLayoutCategoriaPosicionTable()->desactivarOfertas((int)$layout_id);
                        foreach ($ofertas as $key => $data) {
                            $layoutCategoriaP = new LayoutCategoriaPosicion();
                            $layoutCategoriaP->BNF_LayoutCategoria_id = $layout_id;
                            $layoutCategoriaP->BNF_Oferta_id = $data;
                            $layoutCategoriaP->Index = $key;
                            $dato = $this->getLayoutCategoriaPosicionTable()->getLayoutCategoriaPosicionDetails(
                                (int)$layout_id,
                                (int)$data
                            );
                            if ($dato !== false) {
                                $layoutCategoriaP->id = $dato->id;
                            }
                            $this->getLayoutCategoriaPosicionTable()->saveLayoutCategoriaPosicion($layoutCategoriaP);
                        }
                    }
                } elseif ($request->getPost()->type == $this::TYPE_TIENDA) {
                    for ($i = 0; $i < 3; $i++) {
                        $layoutTienda->exchangeArray($form->getData());
                        $layoutTienda->Index = $i + 1;
                        $ofertas = array();
                        if ($i == 0) {
                            $layoutTienda->BNF_Layout_id = $request->getPost()->Fila_1;
                            ($request->getPost()->Fila_1_1 != '') ? $ofertas[1] = $request->getPost()->Fila_1_1 : null;
                            ($request->getPost()->Fila_1_2 != '') ? $ofertas[2] = $request->getPost()->Fila_1_2 : null;
                            ($request->getPost()->Fila_1_3 != '') ? $ofertas[3] = $request->getPost()->Fila_1_3 : null;
                        } elseif ($i == 1) {
                            $layoutTienda->BNF_Layout_id = $request->getPost()->Fila_2;
                            ($request->getPost()->Fila_2_1 != '') ? $ofertas[1] = $request->getPost()->Fila_2_1 : null;
                            ($request->getPost()->Fila_2_2 != '') ? $ofertas[2] = $request->getPost()->Fila_2_2 : null;
                            ($request->getPost()->Fila_2_3 != '') ? $ofertas[3] = $request->getPost()->Fila_2_3 : null;
                        } elseif ($i == 2) {
                            $layoutTienda->BNF_Layout_id = $request->getPost()->Fila_3;
                            ($request->getPost()->Fila_3_1 != '') ? $ofertas[1] = $request->getPost()->Fila_3_1 : null;
                            ($request->getPost()->Fila_3_2 != '') ? $ofertas[2] = $request->getPost()->Fila_3_2 : null;
                            ($request->getPost()->Fila_3_3 != '') ? $ofertas[3] = $request->getPost()->Fila_3_3 : null;
                        }
                        $empresa_id = (int)$request->getPost()->empresa;
                        $data = $this->getLayoutTiendaTable()->getLayoutTiendaDetails($i + 1, $empresa_id);

                        if (is_object($data)) {
                            $layoutTienda->id = $data->id;
                        } else {
                            $layoutTienda->id = null;
                        }

                        if ($empresa_id > 0) {
                            $layoutTienda->BNF_Empresa_id = $empresa_id;
                        }
                        $layout_id = $this->getLayoutTiendaTable()->saveLayoutTienda($layoutTienda);

                        $this->getLayoutTiendaPosicionTable()->desactivarOfertas((int)$layout_id);
                        foreach ($ofertas as $key => $data) {
                            $layoutTiendaP = new LayoutTiendaPosicion();
                            $layoutTiendaP->BNF_LayoutTienda_id = $layout_id;
                            $layoutTiendaP->BNF_Oferta_id = $data;
                            $layoutTiendaP->Index = $key;
                            $dato = $this->getLayoutTiendaPosicionTable()->getLayoutTiendaPosicionDetails(
                                (int)$layout_id,
                                (int)$data
                            );
                            if ($dato !== false) {
                                $layoutTiendaP->id = $dato->id;
                            }
                            $this->getLayoutTiendaPosicionTable()->saveLayoutTienda($layoutTiendaP);
                        }
                    }
                } elseif ($request->getPost()->type == $this::TYPE_PUNTOS) {
                    for ($i = 0; $i < 3; $i++) {
                        $layoutPuntos->exchangeArray($form->getData());
                        $layoutPuntos->Index = $i + 1;
                        $ofertas = array();
                        if ($i == 0) {
                            $layoutPuntos->BNF_Layout_id = $request->getPost()->Fila_1;
                            ($request->getPost()->Fila_1_1 != '') ? $ofertas[1] = $request->getPost()->Fila_1_1 : null;
                            ($request->getPost()->Fila_1_2 != '') ? $ofertas[2] = $request->getPost()->Fila_1_2 : null;
                            ($request->getPost()->Fila_1_3 != '') ? $ofertas[3] = $request->getPost()->Fila_1_3 : null;
                        } elseif ($i == 1) {
                            $layoutPuntos->BNF_Layout_id = $request->getPost()->Fila_2;
                            ($request->getPost()->Fila_2_1 != '') ? $ofertas[1] = $request->getPost()->Fila_2_1 : null;
                            ($request->getPost()->Fila_2_2 != '') ? $ofertas[2] = $request->getPost()->Fila_2_2 : null;
                            ($request->getPost()->Fila_2_3 != '') ? $ofertas[3] = $request->getPost()->Fila_2_3 : null;
                        } elseif ($i == 2) {
                            $layoutPuntos->BNF_Layout_id = $request->getPost()->Fila_3;
                            ($request->getPost()->Fila_3_1 != '') ? $ofertas[1] = $request->getPost()->Fila_3_1 : null;
                            ($request->getPost()->Fila_3_2 != '') ? $ofertas[2] = $request->getPost()->Fila_3_2 : null;
                            ($request->getPost()->Fila_3_3 != '') ? $ofertas[3] = $request->getPost()->Fila_3_3 : null;
                        }
                        $empresa_id = (int)$request->getPost()->empresa;
                        $data = $this->getLayoutPuntosTable()->getLayoutPuntosDetails($i + 1, $empresa_id);

                        if (is_object($data)) {
                            $layoutPuntos->id = $data->id;
                        } else {
                            $layoutPuntos->id = null;
                        }

                        if ($empresa_id > 0) {
                            $layoutPuntos->BNF_Empresa_id = $empresa_id;
                        }
                        $layout_id = $this->getLayoutPuntosTable()->saveLayoutPuntos($layoutPuntos);

                        $this->getLayoutPuntosPosicionTable()->desactivarOfertas((int)$layout_id);
                        foreach ($ofertas as $key => $data) {
                            $layoutPuntosP = new LayoutPuntosPosicion();
                            $layoutPuntosP->BNF_LayoutPuntos_id = $layout_id;
                            $layoutPuntosP->BNF2_Oferta_Puntos_id = $data;
                            $layoutPuntosP->Index = $key;
                            $dato = $this->getLayoutPuntosPosicionTable()->getLayoutPuntosPosicionDetails(
                                (int)$layout_id,
                                (int)$data
                            );
                            if ($dato !== false) {
                                $layoutPuntosP->id = $dato->id;
                            }
                            $this->getLayoutPuntosPosicionTable()->saveLayoutPuntos($layoutPuntosP);
                        }
                    }
                } elseif ($request->getPost()->type == $this::TYPE_PREMIOS) {
                    for ($i = 0; $i < 3; $i++) {
                        $layoutPremios->exchangeArray($form->getData());
                        $layoutPremios->Index = $i + 1;
                        $ofertas = array();
                        if ($i == 0) {
                            $layoutPremios->BNF_Layout_id = $request->getPost()->Fila_1;
                            ($request->getPost()->Fila_1_1 != '') ? $ofertas[1] = $request->getPost()->Fila_1_1 : null;
                            ($request->getPost()->Fila_1_2 != '') ? $ofertas[2] = $request->getPost()->Fila_1_2 : null;
                            ($request->getPost()->Fila_1_3 != '') ? $ofertas[3] = $request->getPost()->Fila_1_3 : null;
                        } elseif ($i == 1) {
                            $layoutPremios->BNF_Layout_id = $request->getPost()->Fila_2;
                            ($request->getPost()->Fila_2_1 != '') ? $ofertas[1] = $request->getPost()->Fila_2_1 : null;
                            ($request->getPost()->Fila_2_2 != '') ? $ofertas[2] = $request->getPost()->Fila_2_2 : null;
                            ($request->getPost()->Fila_2_3 != '') ? $ofertas[3] = $request->getPost()->Fila_2_3 : null;
                        } elseif ($i == 2) {
                            $layoutPremios->BNF_Layout_id = $request->getPost()->Fila_3;
                            ($request->getPost()->Fila_3_1 != '') ? $ofertas[1] = $request->getPost()->Fila_3_1 : null;
                            ($request->getPost()->Fila_3_2 != '') ? $ofertas[2] = $request->getPost()->Fila_3_2 : null;
                            ($request->getPost()->Fila_3_3 != '') ? $ofertas[3] = $request->getPost()->Fila_3_3 : null;
                        }
                        $empresa_id = (int)$request->getPost()->empresa;
                        $data = $this->getLayoutPremiosTable()->getLayoutPremiosDetails($i + 1, $empresa_id);

                        if (is_object($data)) {
                            $layoutPremios->id = $data->id;
                        } else {
                            $layoutPremios->id = null;
                        }

                        if ($empresa_id > 0) {
                            $layoutPremios->BNF_Empresa_id = $empresa_id;
                        }
                        $layout_id = $this->getLayoutPremiosTable()->saveLayoutPremios($layoutPremios);

                        $this->getLayoutPremiosPosicionTable()->desactivarOfertas((int)$layout_id);
                        foreach ($ofertas as $key => $data) {
                            $layoutPremiosP = new LayoutPremiosPosicion();
                            $layoutPremiosP->BNF_LayoutPremios_id = $layout_id;
                            $layoutPremiosP->BNF3_Oferta_Premios_id = $data;
                            $layoutPremiosP->Index = $key;
                            $dato = $this->getLayoutPremiosPosicionTable()->getLayoutPremiosPosicionDetails(
                                (int)$layout_id,
                                (int)$data
                            );
                            if ($dato !== false) {
                                $layoutPremiosP->id = $dato->id;
                            }
                            $this->getLayoutPremiosPosicionTable()->saveLayoutPremios($layoutPremiosP);
                        }
                    }
                }

                $alert = 'success';
                $msg[] = 'Ordenamiento Asignado Correctamente';
                if ($tipo_usuario == $this::USUARIO_CLIENTE) {
                    $nombre_empresa = $this->extraerEmpresa()[$empresa_value];
                    $form = new AssignForm(
                        $this->extraerCategoria(),
                        $this->extraerCampania(),
                        $this->extraerOrdenamiento(),
                        $empresa_value,
                        $tipo_usuario
                    );
                } else {
                    $form = new AssignForm(
                        $this->extraerCategoria(),
                        $this->extraerCampania(),
                        $this->extraerOrdenamiento(),
                        $this->extraerEmpresa()
                    );
                }

                return new ViewModel(
                    array(
                        'ordenamiento' => 'active',
                        'orassign' => 'active',
                        'alert' => $alert,
                        'msg' => $msg,
                        'form' => $form,
                        'type' => $type,
                        'nombre_empresa' => $nombre_empresa
                    )
                );
            } else {
                $opcion = $request->getPost()->type;
                ($request->getPost()->Fila_1_1 != '') ? $lista_oertas[1][1] = $request->getPost()->Fila_1_1 : null;
                ($request->getPost()->Fila_1_2 != '') ? $lista_oertas[1][2] = $request->getPost()->Fila_1_2 : null;
                ($request->getPost()->Fila_1_3 != '') ? $lista_oertas[1][3] = $request->getPost()->Fila_1_3 : null;

                ($request->getPost()->Fila_2_1 != '') ? $lista_oertas[2][1] = $request->getPost()->Fila_2_1 : null;
                ($request->getPost()->Fila_2_2 != '') ? $lista_oertas[2][2] = $request->getPost()->Fila_2_2 : null;
                ($request->getPost()->Fila_2_3 != '') ? $lista_oertas[2][3] = $request->getPost()->Fila_2_3 : null;

                ($request->getPost()->Fila_3_1 != '') ? $lista_oertas[3][1] = $request->getPost()->Fila_3_1 : null;
                ($request->getPost()->Fila_3_2 != '') ? $lista_oertas[3][2] = $request->getPost()->Fila_3_2 : null;
                ($request->getPost()->Fila_3_3 != '') ? $lista_oertas[3][3] = $request->getPost()->Fila_3_3 : null;
            }
        }

        if (!$form->getMessages() == array()) {
            if (isset($form->getMessages()['BNF_Campanias_id'][0])) {
                $form->setMessages(
                    array(
                        'BNF_Campanias_id' => array(
                            '0' => 'Se requiere valor'
                        )
                    )
                );
            }
            if (isset($form->getMessages()['BNF_Categoria_id'][0])) {
                $form->setMessages(
                    array(
                        'BNF_Categoria_id' => array(
                            '0' => 'Se requiere valor'
                        ),
                    )
                );
            }
            if (isset($form->getMessages()['Fila_1'][0])) {
                $form->setMessages(
                    array(
                        'Fila_1' => array(
                            '0' => 'Se requiere valor'
                        ),
                    )
                );
            }
            if (isset($form->getMessages()['Fila_2'][0])) {
                $form->setMessages(
                    array(
                        'Fila_2' => array(
                            '0' => 'Se requiere valor'
                        ),
                    )
                );
            }
            if (isset($form->getMessages()['Fila_3'][0])) {
                $form->setMessages(
                    array(
                        'Fila_3' => array(
                            '0' => 'Se requiere valor'
                        ),
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'ordenamiento' => 'active',
                'orassign' => 'active',
                'form' => $form,
                'type' => $type,
                'nombre_empresa' => $nombre_empresa,
                'opcion' => $opcion,
                'ofertas' => $lista_oertas
            )
        );
    }

    public function extraerOfertaxCategoriaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $cbxOferta = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $categoria_id = (int)$post_data['id'];
            $empresa_id = (int)$post_data['emp'];
            try {
                $datos = $this->getOfertaTable()->getOfertarxCategoria($categoria_id, $empresa_id);
                foreach ($datos as $dato) {
                    $cbxOferta[$dato->id] = $dato->Titulo;
                }
            } catch (\Exception $ex) {
                return $response->setContent(Json::encode(array('response' => false, 'value' => array())));
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $cbxOferta
                    )
                )
            );
        }
        return $response;
    }

    public function extraerOfertaxCampaniaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $cbxOferta = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $campania_id = (int)$post_data['id'];
            $empresa_id = (int)$post_data['emp'];
            try {
                $datos = $this->getOfertaTable()->getOfertarxCampania($campania_id, $empresa_id);
                foreach ($datos as $dato) {
                    $cbxOferta[$dato->id] = $dato->Titulo;
                }
            } catch (\Exception $ex) {
                return $response->setContent(Json::encode(array('response' => false, 'value' => array())));
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $cbxOferta
                    )
                )
            );
        }
        return $response;
    }

    public function extraerOfertaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $cbxOferta = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $empresa_id = (int)$post_data['emp'];
            try {
                $datos = $this->getOfertaTable()->getOfertaEmpresaCliente($empresa_id);
                foreach ($datos as $dato) {
                    $cbxOferta[$dato->id] = $dato->Titulo;
                }
            } catch (\Exception $ex) {
                return $response->setContent(Json::encode(array('response' => false, 'value' => array())));
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $cbxOferta
                    )
                )
            );
        }
        return $response;
    }

    public function extraerOfertaPuntosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $cbxOferta = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $empresa_id = (int)$post_data['emp'];
            try {
                $datos = $this->getOfertaPuntosTable()->getOfertaPuntosEmpresaCliente($empresa_id);
                foreach ($datos as $dato) {
                    $cbxOferta[$dato->id] = $dato->Titulo;
                }
            } catch (\Exception $ex) {
                return $response->setContent(Json::encode(array('response' => false, 'value' => array())));
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $cbxOferta
                    )
                )
            );
        }
        return $response;
    }

    public function tiendaExistAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datos = array();
        $empresa_id = (int)$this->getRequest()->getPost('emp');
        $data = $this->getLayoutTiendaTable()->getLayoutTiendaExist($empresa_id);
        foreach ($data as $dato) {
            $datos[$dato->Index] = $dato->BNF_Layout_id;
        }

        $response = $this->getResponse();
        if (count($datos)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'data' => $datos
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function puntosExistAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datos = array();
        $empresa_id = (int)$this->getRequest()->getPost('emp');
        $data = $this->getLayoutPuntosTable()->getLayoutPuntosExist($empresa_id);
        foreach ($data as $dato) {
            $datos[$dato->Index] = $dato->BNF_Layout_id;
        }

        $response = $this->getResponse();
        if (count($datos)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'data' => $datos
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function getOfertasIdsPorCategoriaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $listaOfertas = array();
        $categoria_id = (int)$this->getRequest()->getPost('id_cat');
        $layout_id = (int)$this->getRequest()->getPost('id_layout');
        $empresa_id = (int)$this->getRequest()->getPost('id_empresa');
        $index = (int)$this->getRequest()->getPost('index');
        $id_layoutCategoriaPsition = $this->getLayoutCategoriaTable()
            ->getLayoutCategoriaId($categoria_id, $index, $layout_id, $empresa_id)->id;
        $data = $this->getLayoutCategoriaPosicionTable()->getOfertasIds($id_layoutCategoriaPsition);
        foreach ($data as $dato) {
            $listaOfertas[$dato->Index] = $dato->BNF_Oferta_id;
        }

        $response = $this->getResponse();
        if (count($listaOfertas)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $listaOfertas
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function getOfertasIdsPorCampaniaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $listaOfertas = array();
        $campania_id = (int)$this->getRequest()->getPost('id_cam');
        $layout_id = (int)$this->getRequest()->getPost('id_layout');
        $empresa_id = (int)$this->getRequest()->getPost('id_empresa');
        $index = (int)$this->getRequest()->getPost('index');
        $id_layoutCampaniaPosition = $this->getLayoutCampaniaTable()
            ->getLayoutCampaniaId($campania_id, $index, $layout_id, $empresa_id)->id;
        $data = $this->getLayoutCampaniaPosicionTable()->getOfertasIds($id_layoutCampaniaPosition);
        foreach ($data as $dato) {
            $listaOfertas[$dato->Index] = $dato->BNF_Oferta_id;
        }

        $response = $this->getResponse();
        if (count($listaOfertas)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $listaOfertas
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function getOfertasIdsTiendaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $listaOfertas = array();
        $layout_id = (int)$this->getRequest()->getPost('id_layout');
        $empresa_id = (int)$this->getRequest()->getPost('id_empresa');
        $index = (int)$this->getRequest()->getPost('index');
        $id_layoutTiendaPosition = $this->getLayoutTiendaTable()
            ->getLayoutTiendaId($index, $layout_id, $empresa_id)->id;
        $data = $this->getLayoutTiendaPosicionTable()->getOfertasIds($id_layoutTiendaPosition);
        foreach ($data as $dato) {
            $listaOfertas[$dato->Index] = $dato->BNF_Oferta_id;
        }

        $response = $this->getResponse();
        if (count($listaOfertas)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $listaOfertas
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function getOfertasIdsPuntosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $listaOfertas = array();
        $layout_id = (int)$this->getRequest()->getPost('id_layout');
        $empresa_id = (int)$this->getRequest()->getPost('id_empresa');
        $index = (int)$this->getRequest()->getPost('index');
        $id_layoutPuntosPosition = $this->getLayoutPuntosTable()
            ->getLayoutPuntosId($index, $layout_id, $empresa_id)->id;
        $data = $this->getLayoutPuntosPosicionTable()->getOfertasIds($id_layoutPuntosPosition);
        foreach ($data as $dato) {
            $listaOfertas[$dato->Index] = $dato->BNF2_Oferta_Puntos_id;
        }

        $response = $this->getResponse();
        if (count($listaOfertas)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $listaOfertas
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function categoriaExistAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datos = array();
        $id = (int)$this->getRequest()->getPost('id');
        $empresa_id = (int)$this->getRequest()->getPost('emp');
        $data = $this->getLayoutCategoriaTable()->getLayoutCategoriaExist($id, $empresa_id);
        foreach ($data as $dato) {
            $datos[$dato->Index] = $dato->BNF_Layout_id;
        }

        $response = $this->getResponse();
        if (count($datos)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'data' => $datos
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function campaniaExistAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datos = array();
        $id = (int)$this->getRequest()->getPost('id');
        $empresa_id = (int)$this->getRequest()->getPost('emp');
        $data = $this->getLayoutCampaniaTable()->getLayoutCampaniaExist($id, $empresa_id);
        foreach ($data as $dato) {
            $datos[$dato->Index] = $dato->BNF_Layout_id;
        }

        $response = $this->getResponse();
        if (count($datos)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'data' => $datos
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function extraerOfertaPremiosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $cbxOferta = array();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $empresa_id = (int)$post_data['emp'];
            try {
                $datos = $this->getOfertaPremiosTable()->getOfertaPremiosEmpresaCliente($empresa_id);
                foreach ($datos as $dato) {
                    $cbxOferta[$dato->id] = $dato->Titulo;
                }
            } catch (\Exception $ex) {
                return $response->setContent(Json::encode(array('response' => false, 'value' => array())));
            }

            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $cbxOferta
                    )
                )
            );
        }
        return $response;
    }

    public function premiosExistAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $datos = array();
        $empresa_id = (int)$this->getRequest()->getPost('emp');
        $data = $this->getLayoutPremiosTable()->getLayoutPremiosExist($empresa_id);
        foreach ($data as $dato) {
            $datos[$dato->Index] = $dato->BNF_Layout_id;
        }

        $response = $this->getResponse();
        if (count($datos)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'data' => $datos
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }

    public function getOfertasIdsPremiosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $listaOfertas = array();
        $layout_id = (int)$this->getRequest()->getPost('id_layout');
        $empresa_id = (int)$this->getRequest()->getPost('id_empresa');
        $index = (int)$this->getRequest()->getPost('index');
        $id_layoutPremiosPosition = $this->getLayoutPremiosTable()
            ->getLayoutPremiosId($index, $layout_id, $empresa_id)->id;
        $data = $this->getLayoutPremiosPosicionTable()->getOfertasIds($id_layoutPremiosPosition);
        foreach ($data as $dato) {
            $listaOfertas[$dato->Index] = $dato->BNF2_Oferta_Premios_id;
        }

        $response = $this->getResponse();
        if (count($listaOfertas)) {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => true,
                        'value' => $listaOfertas
                    )
                )
            );
        } else {
            $response->setContent(
                Json::encode(
                    array(
                        'response' => false,
                    )
                )
            );
        }
        return $response;
    }
}
