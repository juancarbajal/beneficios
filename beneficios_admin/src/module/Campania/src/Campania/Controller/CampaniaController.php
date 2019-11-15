<?php

namespace Campania\Controller;

use Campania\Form\BuscarForm;
use Campania\Form\CampaniaForm;
use Campania\Model\Campania;
use Campania\Model\Filter\CampaniaFilter;
use Campania\Model\CampaniaUbigeo;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class CampaniaController extends AbstractActionController
{
    #region TableObjects
    public function getCampaniaTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaTable');
    }

    public function getCampaniaUTable()
    {
        return $this->serviceLocator->get('Campania\Model\Table\CampaniaUbigeoTable');
    }

    public function getOfertaCampaniaUbigeoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaCampaniaUbigeoTable');
    }

    public function getPaisTable()
    {
        return $this->serviceLocator->get('Paquete\Model\PaisTable');
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
        $busqueda = array(
            'Pais' => 'NombrePais',
            'Nombre' => 'Nombre',
            'Descripcion' => 'Descripcion',
            'Activo' => 'Eliminado',
        );

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $form = new BuscarForm();
        $form->get('NombrePais')->setValueOptions($this->extraerPais());

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
            $campania = new Campania();
            $campania->Nombre = $request->getPost()->Nombre;
            $campania->NombrePais = $request->getPost()->NombrePais;
            $nombre = (!empty($request->getPost()->Nombre)) ? $request->getPost()->Nombre : null;
            $pais = (!empty($request->getPost()->NombrePais)) ? $request->getPost()->NombrePais : 0;
            $form->bind($campania);
            $campania = $this->getCampaniaTable()
                ->getCampaniaDetail($pais, str_replace('-', ' ', $nombre), $order_by, $order);
            if (count($campania) == 1 && $nombre != null) {
                foreach ($campania as $dato) {
                    $nombre = str_replace(' ', '-', $nombre);
                }
            } else {
                $nombre = null;
            }
        } else {
            $pais = $this->params()->fromRoute('q1') ? $this->params()->fromRoute('q1') : 0;
            $nombre = $this->params()->fromRoute('q2') ? $this->params()->fromRoute('q2') : null;
            $form->setData(array("NombrePais" => $pais, "Nombre" => str_replace('-', ' ', $nombre)));
            $campania = $this->getCampaniaTable()
                ->getCampaniaDetail($pais, str_replace('-', ' ', $nombre), $order_by, $order);
        }

        $paginator = new Paginator(new paginatorIterator($campania, $order_by));
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
                'beneficios' => 'active',
                'campania' => 'active',
                'calistar' => 'active',
                'form' => $form,
                'datos' => $paginator,
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
        $form = new CampaniaForm($this->extraerPais());
        $alert = 'danger';
        $msg = null;
        $errors = null;
        $request = $this->getRequest();

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($request->isPost()) {
            $campania = new Campania();
            $filter = new CampaniaFilter();

            $form->setInputFilter($filter->getInputFilter($this->extraerPais()));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $nombre = ucwords(strtolower(rtrim(ltrim($request->getPost()->Nombre))));
                if ($this->getCampaniaTable()->getCampaniabyName($nombre)) {
                    $errors['repeat'] = 'El Nombre ya existe.';
                    $errors['repeatc'] = 'has-error';
                    $request->getPost()->Nombre = $nombre;
                    $form->setData($request->getPost());
                } else {
                    $campania->exchangeArray($form->getData());
                    $campania->Slug = $this->getSlug($nombre);
                    $campania->Eliminado = '0';
                    $id = $this->getCampaniaTable()->saveCampania($campania);

                    //relacionar camáña y pais
                    $campaniaUbigeo = new CampaniaUbigeo();
                    $campaniaUbigeo->BNF_Pais_id = (int)$request->getPost()->NombrePais;
                    $campaniaUbigeo->BNF_Campanias_id = (int)$id;
                    $campaniaUbigeo->Eliminado = '0';

                    $this->getCampaniaUTable()->saveCampaniaUbigeo($campaniaUbigeo);

                    $alert = 'success';
                    $msg[] = 'Campaña Registrada Correctamente';
                    $form = new CampaniaForm();
                    $form->get('NombrePais')->setValueOptions($this->extraerPais());
                }
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'campania' => 'active',
                'caadd' => 'active',
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

        $id = (int)$this->params()->fromRoute('id', 0);
        $campania = new Campania();

        if (!$id) {
            return $this->redirect()->toRoute('campania', array('action' => 'add'));
        }

        try {
            $campanias = $this->getCampaniaTable()->getCampaniaEdit($id);
            foreach ($campanias as $dato) {
                $campania = $dato;
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('campania', array('action' => 'index'));
        }

        $config = $this->getServiceLocator()->get('Config');
        $link = $config['campania'] . $campania->Slug;

        $form = new CampaniaForm($this->extraerPais());
        $form->bind($campania);
        $form->get('submit')->setAttribute('value', 'Editar');
        $alert = 'danger';
        $msg = null;
        $errors = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filter = new CampaniaFilter();
            $form->setInputFilter($filter->getInputFilter($this->extraerPais()));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $nombre = ucwords(strtolower(rtrim(ltrim($request->getPost()->Nombre))));
                if ($this->getCampaniaTable()->getCampaniabyName($nombre, $id)) {
                    $errors['repeat'] = 'El Nombre ya existe.';
                    $errors['repeatc'] = 'has-error';
                    $request->getPost()->Nombre = $nombre;
                    $form->setData($request->getPost());
                } else {
                    $campania->Slug = $this->getSlug($nombre);
                    $id = $this->getCampaniaTable()->saveCampania($campania);

                    //relacionar camáña y pais
                    $campaniaUbigeo = $this->getCampaniaUTable()->getCampaniaUbigeobyCamp($id);
                    $campaniaUbigeo->Eliminado = '' . (int)$campaniaUbigeo->Eliminado . '';
                    $campaniaUbigeo->BNF_Pais_id = (int)$request->getPost()->NombrePais;
                    $this->getCampaniaUTable()->saveCampaniaUbigeo($campaniaUbigeo);

                    $this->flashMessenger()->addMessage('Campaña Modificada Correctamente');
                    return $this->redirect()->toRoute('campania');
                }
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'campania' => 'active',
                'caadd' => 'active',
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
        $this->getCampaniaTable()->deleteCampania($id, $val);
        $this->getCampaniaUTable()->deleteCampaniaUbigeo($id, $val);
        return json_encode(array('status' => 200));
    }

    public function exportAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $resultado = $this->getCampaniaTable()->getCampaniaDetail(null, null, 'id');
        $registros = count($resultado);

        $objPHPExcel = new PHPExcel();
        if ($registros > 0) {
            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte Campañas del Sistema")
                ->setSubject("Campañas")
                ->setDescription("Documento listando las Campañas")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Campañas");

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($registros + 1))->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'id')
                ->setCellValue('B1', 'Nombre')
                ->setCellValue('C1', 'Descripcion')
                ->setCellValue('D1', 'Pais')
                ->setCellValue('E1', 'Fecha Creacion')
                ->setCellValue('F1', 'Fecha Actualizacion')
                ->setCellValue('G1', 'Eliminado');
            $i = 2;
            foreach ($resultado as $registro) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $registro->id)
                    ->setCellValue('B' . $i, $registro->Nombre)
                    ->setCellValue('C' . $i, $registro->Descripcion)
                    ->setCellValue('D' . $i, $registro->NombrePais)
                    ->setCellValue('E' . $i, $registro->FechaCreacion)
                    ->setCellValue('F' . $i, $registro->FechaActualizacion)
                    ->setCellValue('G' . $i, ((int)$registro->Eliminado == 0) ? 'Activo ' : 'Inactivo');
                $i++;

            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Campañas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
