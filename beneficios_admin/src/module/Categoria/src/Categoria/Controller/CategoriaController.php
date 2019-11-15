<?php

namespace Categoria\Controller;

use Categoria\Form\BuscarCategoriaForm;

use Categoria\Form\CategoriaForm;
use Categoria\Model\Categoria;
use Categoria\Model\CategoriaUbigeo;
use Categoria\Model\Data\BuscarCategoriaData;
use Categoria\Model\Filter\BuscarCategoriaFilter;
use Categoria\Model\Filter\CategoriaFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class CategoriaController extends AbstractActionController
{
    #region ObjectTables
    public function getCategoriaTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaTable');
    }

    public function getCategoriaUbigeoTable()
    {
        return $this->serviceLocator->get('Categoria\Model\Table\CategoriaUbigeoTable');
    }

    public function getPaisTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaisTable');
    }

    public function getOfertaCategoriaUbigeoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaCategoriaUbigeoTable');
    }

    #endregion

    public function extraerPais()
    {
        $cbxPais = array();
        try {
            $datosp = $this->getPaisTable()->fetchAll();
            foreach ($datosp as $dato) {
                $cbxPais[$dato->id] = $dato->NombrePais;
            }
        } catch (\Exception $ex) {
            return $cbxPais;
        }
        return $cbxPais;
    }

    public function getSlug($cadena)
    {
        $a = array(
            'S/.', '!', '¡', 'en vez de', ' por ', ' en ', ' el ', ' la ', ' + ', ' desde ', '®', '#', ':',
            ',', '/', ';', '*', '\\', '.', '$', '%', '@', '', '©', '£', '¥',
            '|', '°', '¬', '"', '&', '(', ')', '?', '¿', "'", '{', '}', '^', '~', '`', '<', '>',
            ' a ', ' e ', ' i ', ' o ', ' u ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ',
            ' ', '-.', '.-', '--',
        );

        $b = array(
            '', '', '', 'x', '-', '-', '-', '-', '-', '', '', '', '',
            '-', '-', '-', '-', '-', '-', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
            '-', '-', '-', '-', '-', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'ni',
            '-', '-', '-', '-',
        );

        return strtolower(str_ireplace($a, $b, strtolower($cadena)));
    }

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $busqueda = array(
            'Pais' => 'NombrePais',
            'Nombre' => 'Nombre',
            'Descripcion' => 'Descripcion',
            'Activo' => 'Eliminado',
        );

        $nombre = null;
        $pais = null;
        $data = new BuscarCategoriaData($this);

        $form = new BuscarCategoriaForm('buscar', $data->getFormData());
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

        //Obteniendo parametros de busqueda
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $validate = new BuscarCategoriaFilter();
            $form->setInputFilter(
                $validate->getInputFilter($data->getFilterData(), $post)
            );

            $form->setData($post);

            if ($form->isValid()) {
                $nombre = (!empty($request->getPost()->Nombre)) ? $request->getPost()->Nombre : null;
                $pais = (!empty($request->getPost()->Pais)) ? $request->getPost()->Pais : 0;
            }
        } else {
            $pais = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : 0;
            $nombre = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $form->setData(array("Pais" => $pais, "Nombre" => str_replace('-', ' ', $nombre)));
        }

        $categorias = $this->getCategoriaTable()
            ->getCategoriaDetails($pais, str_replace('-', ' ', $nombre), $order_by, $order);

        $paginator = new Paginator(new paginatorIterator($categorias, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        if (count($categorias) >= 1 && $nombre != null) {
            $nombre = str_replace(' ', '-', $nombre);
        } else {
            $nombre = null;
        }

        return new ViewModel(
            array(
                'categoria' => 'active',
                'clistar' => 'active',
                'form' => $form,
                'categorias' => $paginator,
                'order_by' => $order_by_o,
                'order' => $order,
                'q1' => $pais,
                'q2' => $nombre,
                'p' => $page,
            )
        );
    }

    public function addAction()
    {
        $form = new CategoriaForm('categoria', $this->extraerPais());
        $alert = 'danger';
        $msg = null;
        $errors = null;
        $request = $this->getRequest();

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        if ($request->isPost()) {
            $categoria = new Categoria();
            $filter = new CategoriaFilter();

            $form->setInputFilter($filter->getInputFilter($this->extraerPais()));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $nombre = ucwords(strtolower(rtrim(ltrim($request->getPost()->Nombre))));
                if ($this->getCategoriaTable()->getCategoriabyName($nombre)) {
                    $errors['repeat'] = 'El Nombre ya existe.';
                    $errors['repeatc'] = 'has-error';
                    $request->getPost()->Nombre = $nombre;
                    $form->setData($request->getPost());
                } else {
                    $categoria->exchangeArray($form->getData());
                    $categoria->Slug = $this->getSlug($nombre);
                    $categoria->Eliminado = '0';
                    $id = $this->getCategoriaTable()->saveCategoria($categoria);

                    //relacionar categoria y pais

                    $categoriaUbigeo = new CategoriaUbigeo();
                    $categoriaUbigeo->BNF_Pais_id = (int)$request->getPost()->NombrePais;
                    $categoriaUbigeo->BNF_Categoria_id = (int)$id;
                    $categoriaUbigeo->Eliminado = '0';

                    $this->getCategoriaUbigeoTable()->saveCategoriaUbigeo($categoriaUbigeo);

                    $alert = 'success';
                    $msg[] = 'Categoria Registrada Correctamente';
                    $form = new CategoriaForm('categoria', $this->extraerPais());

                    return new ViewModel(
                        array(
                            'alert' => $alert,
                            'msg' => $msg,
                            'form' => $form,
                            'cadd' => 'active',
                            'categoria' => 'active',
                        )
                    );
                }
            }
        }

        return new ViewModel(
            array(
                'categoria' => 'active',
                'cadd' => 'active',
                'alert' => $alert,
                'msg' => $msg,
                'errors' => $errors,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $id = (int)$this->params()->fromRoute('id', 0);
        $categoria = new Categoria();
        if (!$id) {
            return $this->redirect()->toRoute('categoria', array('action' => 'add'));
        }
        try {
            $categorias = $this->getCategoriaTable()->getCategoriaEdit($id);
            foreach ($categorias as $dato){
                $categoria = $dato;
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('categoria', array('action' => 'index'));
        }

        $config = $this->getServiceLocator()->get('Config');
        $link = $config['categoria'] . $categoria->Slug;

        $form = new CategoriaForm('categoria', $this->extraerPais());
        $form->bind($categoria);
        $form->get('submit')->setAttribute('value', 'Editar');
        $alert = 'danger';
        $msg = null;
        $errors = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = new CategoriaFilter();
            $form->setInputFilter($filter->getInputFilter($this->extraerPais()));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $nombre = ucwords(strtolower(rtrim(ltrim($request->getPost()->Nombre))));
                if ($this->getCategoriaTable()->getCategoriabyName($nombre, $id)) {
                    $errors['repeat'] = 'El Nombre ya existe.';
                    $errors['repeatc'] = 'has-error';
                    $request->getPost()->Nombre = $nombre;
                    $form->setData($request->getPost());
                } else {
                    $categoria->Slug = $this->getSlug($nombre);
                    $id = $this->getCategoriaTable()->saveCategoria($categoria);

                    //relacionar camáña y pais
                    $categoriaUbigeo = $this->getCategoriaUbigeoTable()->getCategoriaUbigeobyCat($id);
                    $categoriaUbigeo->Eliminado = '' . (int)$categoriaUbigeo->Eliminado . '';
                    $categoriaUbigeo->BNF_Pais_id = (int)$request->getPost()->NombrePais;
                    $this->getCategoriaUbigeoTable()->saveCategoriaUbigeo($categoriaUbigeo);

                    $this->flashMessenger()->addMessage('Categoria Modificada Correctamente');
                    return $this->redirect()->toRoute('categoria');
                }
            }
        }

        return new ViewModel(
            array(
                'categoria' => 'active',
                'clistar' => 'active',
                'id' => $id,
                'form' => $form,
                'errors' => $errors,
                'alert' => $alert,
                'msg' => $msg,
                'link' => $link
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
        $this->getCategoriaTable()->deleteCategoria($id, $val);
        $this->getCategoriaUbigeoTable()->deleteCategoriaUbigeo($id, $val);
        return json_encode(array('status' => 200));
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $resultado = $this->getCategoriaTable()->getCategoriaDetails();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {

            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte de Categorias")
                ->setSubject("Categoria")
                ->setDescription("Documento listando las Categorias")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Categoria");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:G' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

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

            //Crear Bordes del Documento
            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($registros + 1))->applyFromArray($styleArray2);

            //Filtrado
            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Pais')
                ->setCellValue('C1', 'Nombre')
                ->setCellValue('D1', 'Descripción')
                ->setCellValue('E1', 'Fecha de Creación')
                ->setCellValue('F1', 'Fecha de Actualización')
                ->setCellValue('G1', 'Eliminado');

            $i = 2;

            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->NombrePais)
                    ->setCellValue('C' . $i, $registro->Nombre)
                    ->setCellValue('D' . $i, $registro->Descripcion)
                    ->setCellValue('E' . $i, $registro->FechaCreacion)
                    ->setCellValue('F' . $i, $registro->FechaActualizacion)
                    ->setCellValue('G' . $i, ((int)$registro->Eliminado == 0) ? 'Activo' : 'Inactivo');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Categorias.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
