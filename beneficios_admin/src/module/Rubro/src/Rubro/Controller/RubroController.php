<?php

namespace Rubro\Controller;

use Rubro\Model\Filter\RubroFilter;
use Rubro\Model\Rubro;
use Rubro\Form\RubroForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class RubroController extends AbstractActionController
{
    #region ObjectTables
    public function getRubroTable()
    {
        return $this->serviceLocator->get('Rubro\Model\Table\RubroTable');
    }

    public function getOfertaRubroTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaRubroTable');
    }

    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $busqueda = array(
            'Nombre' => 'Nombre',
            'Descripcion' => 'Descripcion',
            'Activo' => 'Eliminado',
        );
        $nombre = null;

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

        $form = new RubroForm('rubro', 'Buscar');

        if ($request->isPost()) {

            $post = $this->getRequest()->getPost()->toArray();
            $validate = new RubroFilter();
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

        $rubro = $this->getRubroTable()->getRubroDetails(str_replace('-', ' ', $nombre), $order_by, $order);

        if (count($rubro) >= 1 && $nombre != null) {
            $nombre = str_replace(' ', '-', $nombre);
        } else {
            $nombre = null;
        }
        $paginator = new Paginator(new paginatorIterator($rubro, $order_by));
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($itemsPerPage)
            ->setPageRange(7);

        return new ViewModel(
            array(
                'rubro' => 'active',
                'rlistar' => 'active',
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

        $form = new RubroForm();
        $alert = 'danger';
        $msg = null;
        $errors = null;
        $request = $this->getRequest();
        $rubro = new Rubro();
        if ($request->isPost()) {

            $post = $this->getRequest()->getPost()->toArray();
            $validate = new RubroFilter();
            $form->setInputFilter(
                $validate->getInputFilter($post, true)
            );

            $form->setData($post);
            if ($form->isValid()) {
                $nombre = ucwords(strtolower(rtrim(ltrim($request->getPost()->Nombre))));
                if ($this->getRubroTable()->getRubrobyName($nombre)) {
                    $errors['repeat'] = 'El Nombre ya existe.';
                    $errors['repeatc'] = 'has-error';
                    $request->getPost()->Nombre = $nombre;
                    $form->setData($request->getPost());
                } else {
                    $rubro->exchangeArray($form->getData());
                    $rubro->Eliminado = '0';
                    $this->getRubroTable()->saveRubro($rubro);

                    $alert = 'success';
                    $msg[] = 'Rubro Registrado Correctamente';
                    $form = new RubroForm();
                }
            }
        }

        return new ViewModel(
            array(
                'rubro' => 'active',
                'radd' => 'active',
                'form' => $form,
                'errors' => $errors,
                'alert' => $alert,
                'msg' => $msg
            )
        );
    }

    public function editAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        ////////recepciona id del usuario enviado por post
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('rubro', array('action' => 'add'));
        }
        try {
            $rubro = $this->getRubroTable()->getRubro($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('rubro', array('action' => 'index'));
        }
        ///////

        $form = new RubroForm('rubro', 'Editar');
        $form->bind($rubro);
        $alert = 'danger';
        $msg = null;
        $errors = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $this->getRequest()->getPost()->toArray();
            $validate = new RubroFilter();

            $form->setInputFilter(
                $validate->getInputFilter($post)
            );
            $form->setData($post);

            if ($form->isValid()) {
                $nombre = ucwords(strtolower(rtrim(ltrim($request->getPost()->Nombre))));
                if ($this->getRubroTable()->getRubrobyName($nombre, $id)) {
                    $errors['repeat'] = 'El Nombre ya existe.';
                    $errors['repeatc'] = 'has-error';
                    $request->getPost()->Nombre = $nombre;
                    $form->setData($request->getPost());
                } else {
                    $this->getRubroTable()->saveRubro($rubro);
                    $this->flashMessenger()->addMessage('Rubro Modificado Correctamente');
                    return $this->redirect()->toRoute('rubro');
                }
            }
        }

        return new ViewModel(
            array(
                'rubro' => 'active',
                'rlistar' => 'active',
                'id' => $id,
                'form' => $form,
                'errors' => $errors,
                'alert' => $alert,
                'msg' => $msg
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
        $this->getRubroTable()->deleteRubro($id, $val);
        return json_encode(array('status' => 200));

    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultado = $this->getRubroTable()->getReport();
        $registros = count($resultado);
        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Rubros del Sistema")
                ->setSubject("Rubros")
                ->setDescription("Documento listando las Rubros")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Rubros");

            $objPHPExcel->getActiveSheet()->setAutoFilter('A1:F' . $registros);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:F' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre')
                ->setCellValue('C1', 'Descripcion')
                ->setCellValue('D1', 'Fecha Creacion')
                ->setCellValue('E1', 'Fecha Actualizacion')
                ->setCellValue('F1', 'Eliminado');
            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Nombre)
                    ->setCellValue('C' . $i, $registro->Descripcion)
                    ->setCellValue('D' . $i, $registro->FechaCreacion)
                    ->setCellValue('E' . $i, $registro->FechaActualizacion)
                    ->setCellValue('F' . $i, ((int)$registro->Eliminado == 0) ? 'Activo' : 'Inactivo');
                $i++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Rubros.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
