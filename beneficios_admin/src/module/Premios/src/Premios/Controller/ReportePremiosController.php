<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/09/16
 * Time: 10:29
 */

namespace Premios\Controller;

use Auth\Form\BaseForm;
use Auth\Service\Csrf;
use Premios\Form\FormReportePremios;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class ReportePremiosController extends AbstractActionController
{
    const REPORTE_COMP = "Reporte de comportamiento por empresa";
    const REPORTE_COMP_X_EMP = "Reporte de comportamiento por empresa";
    const REPORTE_COMP_X_CAMP = "Reporte de comportamiento por campañas";
    const REPORTE_COMP_X_SEG = "Reporte de comportamiento por segmento";
    const REPORTE_COMP_X_USU = "Reporte de comportamiento por usuario";
    const REPORTE_DEMO_X_USU = "Reporte demográfico por usuario";
    const REPORTE_PREF = "Reporte de preferencia por empresa";
    const REPORTE_PREF_X_EMP = "Reporte de preferencia por empresa";
    const REPORTE_PREF_X_CAMP = "Reporte de preferencia por campañas";
    const REPORTE_PREF_X_SEG = "Reporte de preferencia por segmento";
    const REPORTE_PREF_X_USU = "Reporte de preferencia por usuario";
    const REPORTE_EC_COMP_PREF = "Reporte de Empresa Cliente con información de Comportamiento y Preferencia";
    const REPORTE_CAMP_COMP_PREF = "Reporte de Campañas con información de Comportamiento y Preferencia";
    const REPORTE_SEG_COMP_PREF = "Reporte de Segmentos con información de Comportamiento y Preferencia";
    const REPORTE_USU_COMP_PREF = "Reporte de usuarios con información de Comportamiento y Preferencias";
    const REPORTE_USU_DEMO_COMP = "Reporte de usuarios con información demográfica y comportamiento";
    const REPORTE_USU_DEMO_PREF = "Reporte de usuarios con información demográfica y preferencias";
    const REPORTE_USU_DEMO_COMP_PREF = "Reporte de usuarios con información demográfica, de comportamiento y de preferencias";

    #region ObjectTables
    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getCampaniaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\CampaniasPremiosTable');
    }

    public function getSegmentoPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\SegmentosPremiosTable');
    }

    public function getOfertaPremiosTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\OfertaPremiosTable');
    }

    public function getAsignacionTable()
    {
        return $this->serviceLocator->get('Premios\Model\Table\AsignacionPremiosTable');
    }

    #endregion

    public function inicializacionBusqueda()
    {
        $dataEmpCli = array();
        $filterEmpCli = array();

        try {
            foreach ($this->getCampaniaPremiosTable()->getEmpresasCliente() as $empresa) {
                $dataEmpCli[$empresa->id] = $empresa->Empresa;
                $filterEmpCli[$empresa->id] = [$empresa->id];
            }
        } catch (\Exception $ex) {
            $dataEmpCli = array();
        }

        $formulario['emp'] = $dataEmpCli;
        $filtro['emp'] = array_keys($filterEmpCli);
        return array($formulario, $filtro);
    }

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $data = $this->inicializacionBusqueda();

        $empresa = $this->identity()->BNF_Empresa_id;
        if ($this->identity()->TipoUsuario == "cliente") {
            $type = "cliente";
            $nombre = $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa)->NombreComercial;
        } else {
            $type = "admin";
            $nombre = "";
        }
        $form = new FormReportePremios('reporte', $data[0], $type);
        $form->get('empresa')->setAttribute('value', $empresa);
        return new ViewModel(
            array(
                'form' => $form,
                'type' => $type,
                'nombre' => $nombre,
                'reportepremios' => "active",
                'reportespremios' => "active"
            )
        );
    }

    public function reporteAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $checkbox_demo = ($request->getPost()->checkboxDemo);
            $checkbox_comp = ($request->getPost()->checkboxComp);
            $checkbox_pref = ($request->getPost()->checkboxPref);

            $checkbox_empresa = ($this->identity()->TipoUsuario == "cliente") ? true : $request->getPost()->checkboxEmpresa;
            $checkbox_campania = ($request->getPost()->checkboxCampania);
            $checkbox_segmento = ($request->getPost()->checkboxSegmento);
            $checkbox_usuario = ($request->getPost()->checkboxUsuario);

            $empresa = ($this->identity()->TipoUsuario == "cliente")
                ? $this->identity()->BNF_Empresa_id : $request->getPost()->empresa;
            $campania = ($request->getPost()->campania);
            $segmento = ($request->getPost()->segmento);
            $usuario = ($request->getPost()->usuario);

            $nombre_archivo = "ReportePremios";
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte de Comportamiento de Empresa")
                ->setSubject("Reporte Premios")
                ->setDescription("Documento Reporte de Comportamiento de Empresa")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte Premios");

            $tittleStyleArray = array(
                'font' => array(
                    'bold' => true,
                )
            );

            $cellArray = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                )
            );

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
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

            if ($checkbox_demo && !$checkbox_comp && !$checkbox_pref) {
                $resultado = $this->getAsignacionTable()
                    ->reporteDemografico($checkbox_empresa, $empresa, $checkbox_campania, $campania, $checkbox_segmento, $segmento, $checkbox_usuario, $usuario);
                $registros = count($resultado);
                $inicio = 7;

                #region General
                $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);

                if ($registros > 0) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);

                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->getActiveSheet()->setAutoFilter('A7:O' . ($registros + $inicio));
                        $objPHPExcel->getActiveSheet()->getStyle('A8:O' . ($inicio + $registros))->applyFromArray($styleArray2);
                        $objPHPExcel->getActiveSheet()->getStyle('A7:O7')->applyFromArray($styleArray);
                    } else {
                        $objPHPExcel->getActiveSheet()->setAutoFilter('B7:O' . ($registros + $inicio));
                        $objPHPExcel->getActiveSheet()->getStyle('B8:O' . ($inicio + $registros))->applyFromArray($styleArray2);
                        $objPHPExcel->getActiveSheet()->getStyle('B7:O7')->applyFromArray($styleArray);
                    }

                    $objPHPExcel->getActiveSheet()->getStyle('D8:D' . ($inicio + $registros))->applyFromArray($cellArray);

                    $nombre_archivo = $this::REPORTE_DEMO_X_USU;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $nombre_archivo)
                        ->mergeCells('A1:B1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                        ->setCellValue('B3', 'Todas')
                        ->setCellValue('B4', 'Todos');

                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Empresa');
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B7', 'Campaña')
                        ->setCellValue('C7', 'Segmento')
                        ->setCellValue('D7', 'Nro. Documento')
                        ->setCellValue('E7', 'Correos')
                        ->setCellValue('F7', 'Nombres')
                        ->setCellValue('G7', 'Apellidos')
                        ->setCellValue('H7', 'Celular')
                        ->setCellValue('I7', 'Año Nacimiento')
                        ->setCellValue('J7', 'Estado Civil')
                        ->setCellValue('K7', 'Nivel Educativo')
                        ->setCellValue('L7', 'Genero')
                        ->setCellValue('M7', 'Hijos')
                        ->setCellValue('N7', 'Distritos')
                        ->setCellValue('O7', 'Lugar de Trabajo');
                    $i = $inicio + 1;

                    foreach ($resultado as $registro) {
                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, $registro->Empresa);
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, $registro->Campania)
                            ->setCellValue('C' . $i, $registro->Segmento)
                            ->setCellValue('D' . $i, $registro->NumeroDocumento)
                            ->setCellValue('E' . $i, $registro->Correos)
                            ->setCellValue('F' . $i, $registro->Pregunta01)
                            ->setCellValue('G' . $i, $registro->Pregunta02)
                            ->setCellValue('H' . $i, $registro->Pregunta09)
                            ->setCellValue('I' . $i, $registro->Pregunta03)
                            ->setCellValue('J' . $i, $registro->Pregunta05)
                            ->setCellValue('K' . $i, $registro->Pregunta10)
                            ->setCellValue('L' . $i, $registro->Pregunta04)
                            ->setCellValue('M' . $i, $registro->Pregunta08)
                            ->setCellValue('N' . $i, $registro->Pregunta06)
                            ->setCellValue('O' . $i, $registro->Pregunta07);
                        $i++;
                    }
                }
                #endregion
            } elseif (!$checkbox_demo && $checkbox_comp && !$checkbox_pref) {
                $resultado = $this->getAsignacionTable()->reporteComportamiento($checkbox_empresa, $empresa);
                $registros = count($resultado);
                $inicio = 7;

                if ($checkbox_empresa || $checkbox_campania || $checkbox_segmento || $checkbox_usuario) {
                    #region Opciones
                    $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                    $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                    $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);

                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->getActiveSheet()->getStyle('A7:H7')->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('A8:H8')->applyFromArray($styleArray);
                    } else {
                        $objPHPExcel->getActiveSheet()->getStyle('B7:H7')->applyFromArray($styleArray);
                        $objPHPExcel->getActiveSheet()->getStyle('B8:H8')->applyFromArray($styleArray);
                    }

                    if ($checkbox_usuario) {
                        $nombre_archivo = $this::REPORTE_COMP_X_USU;
                        $titulo = $nombre_archivo;
                    } elseif ($checkbox_segmento) {
                        $nombre_archivo = $this::REPORTE_COMP_X_SEG;
                        $titulo = $nombre_archivo;
                    } elseif ($checkbox_campania) {
                        $nombre_archivo = $this::REPORTE_COMP_X_CAMP;
                        $titulo = $nombre_archivo;
                    } else {
                        $nombre_archivo = $this::REPORTE_COMP_X_EMP;
                        $titulo = $nombre_archivo;
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $titulo)
                        ->mergeCells('A1:B1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                        ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                        ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                    #endregion

                    #region Seccion Empresa
                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A7', 'Empresas')
                            ->mergeCells('A7:A8');
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B7', 'Usuarios')
                        ->setCellValue('E7', 'Premios')
                        ->mergeCells('B7:D7')
                        ->mergeCells('E7:H7')
                        ->setCellValue('B8', 'Asignados')
                        ->setCellValue('C8', 'Aplicados')
                        ->setCellValue('D8', 'Redimidos')
                        ->setCellValue('E8', 'Asignados')
                        ->setCellValue('F8', 'Aplicados')
                        ->setCellValue('G8', 'Redimidos')
                        ->setCellValue('H8', 'Saldo');

                    $i = $inicio + 2;

                    $sumUsuAsignados = 0;
                    $sumUsuAplicados = 0;
                    $sumUsuRedimidos = 0;
                    $sumTotalAsignados = 0;
                    $sumTotalAplicados = 0;
                    $sumRedimidos = 0;
                    $sumSaldo = 0;
                    $dataEmpresas = "";

                    foreach ($resultado as $registro) {
                        $dataEmpresas = $dataEmpresas . $registro->Empresa . "; ";
                        $sumUsuAsignados = $sumUsuAsignados + $registro->UsuAsignados;
                        $sumUsuAplicados = $sumUsuAplicados + $registro->UsuAplicados;
                        $sumUsuRedimidos = $sumUsuRedimidos + $registro->UsuRedimidos;
                        $sumTotalAsignados = $sumTotalAsignados + $registro->TotalAsignados;
                        $sumTotalAplicados = $sumTotalAplicados + $registro->TotalAplicados;
                        $sumRedimidos = $sumRedimidos + $registro->Redimidos;
                        $sumSaldo = $sumSaldo + $registro->TotalAsignados - $registro->TotalAplicados;
                    }

                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $dataEmpresas);
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $sumUsuAsignados)
                        ->setCellValue('C' . $i, $sumUsuAplicados)
                        ->setCellValue('D' . $i, $sumUsuRedimidos)
                        ->setCellValue('E' . $i, $sumTotalAsignados)
                        ->setCellValue('F' . $i, $sumTotalAplicados)
                        ->setCellValue('G' . $i, $sumRedimidos)
                        ->setCellValue('H' . $i, $sumSaldo);

                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->getActiveSheet()->getStyle('A9:H' . $i)->applyFromArray($styleArray2);
                        $objPHPExcel->getActiveSheet()->getStyle('A9')->getAlignment()->setWrapText(true);
                    } else {
                        $objPHPExcel->getActiveSheet()->getStyle('B9:H' . $i)->applyFromArray($styleArray2);
                    }

                    $i++;
                    #endregion

                    #region Seccion Campañas
                    if ($checkbox_campania or $checkbox_segmento or $checkbox_usuario) {
                        $resultadoCampania = $this->getAsignacionTable()
                            ->reporteComportamiento(true, $empresa, true, $campania);

                        $i = $i + 3;
                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->setCellValue('C' . $i, 'Usuarios')
                            ->setCellValue('D' . $i, 'Premios')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->mergeCells('C' . $i . ':E' . $i)
                            ->mergeCells('F' . $i . ':I' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
                        }

                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('C' . $i, 'Asignados')
                            ->setCellValue('D' . $i, 'Aplicados')
                            ->setCellValue('E' . $i, 'Redimidos')
                            ->setCellValue('F' . $i, 'Asignados')
                            ->setCellValue('G' . $i, 'Aplicados')
                            ->setCellValue('H' . $i, 'Redimidos')
                            ->setCellValue('I' . $i, 'Saldo');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . (count($resultadoCampania) + $i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . (count($resultadoCampania) + $i))->applyFromArray($styleArray2);
                        }
                        $i++;

                        foreach ($resultadoCampania as $registro) {
                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $registro->Empresa);
                            }

                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, $registro->Campania)
                                ->setCellValue('C' . $i, (int)$registro->UsuAsignados)
                                ->setCellValue('D' . $i, (int)$registro->UsuAplicados)
                                ->setCellValue('E' . $i, (int)$registro->UsuRedimidos)
                                ->setCellValue('F' . $i, (int)$registro->TotalAsignados)
                                ->setCellValue('G' . $i, (int)$registro->TotalAplicados)
                                ->setCellValue('H' . $i, (int)$registro->Redimidos)
                                ->setCellValue('I' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                            $i++;
                        }
                    }
                    #endregion

                    #region Seccion Segmentos
                    if ($checkbox_segmento or $checkbox_usuario) {
                        $resultadoSegmento = $this->getAsignacionTable()
                            ->reporteComportamiento(
                                true,
                                $empresa,
                                true,
                                $campania,
                                true,
                                $segmento
                            );

                        $i = $i + 3;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->setCellValue('C' . $i, 'Segmentos')
                            ->setCellValue('D' . $i, 'Usuarios')
                            ->setCellValue('G' . $i, 'Premios')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->mergeCells('C' . $i . ':C' . ($i + 1))
                            ->mergeCells('D' . $i . ':F' . $i)
                            ->mergeCells('G' . $i . ':J' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':J' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);
                        }
                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . $i, 'Asignados')
                            ->setCellValue('E' . $i, 'Aplicados')
                            ->setCellValue('F' . $i, 'Redimidos')
                            ->setCellValue('G' . $i, 'Asignados')
                            ->setCellValue('H' . $i, 'Aplicados')
                            ->setCellValue('I' . $i, 'Redimidos')
                            ->setCellValue('J' . $i, 'Saldo');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':J' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':J' . (count($resultadoSegmento) + $i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':J' . (count($resultadoSegmento) + $i))->applyFromArray($styleArray2);
                        }
                        $i++;

                        $campaniaTemp = "";
                        foreach ($resultadoSegmento as $registro) {
                            if ($campaniaTemp == "") {
                                $campaniaTemp = $registro->Campania;
                                $imprimir = true;
                            } else {
                                if ($campaniaTemp != $registro->Campania) {
                                    $campaniaTemp = $registro->Campania;
                                    $imprimir = true;
                                } else {
                                    $imprimir = false;
                                }
                            }

                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, $registro->Empresa);
                            }

                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, ($imprimir) ? $campaniaTemp : "")
                                ->setCellValue('C' . $i, $registro->Segmento)
                                ->setCellValue('D' . $i, (int)$registro->UsuAsignados)
                                ->setCellValue('E' . $i, (int)$registro->UsuAplicados)
                                ->setCellValue('F' . $i, (int)$registro->UsuRedimidos)
                                ->setCellValue('G' . $i, (int)$registro->TotalAsignados)
                                ->setCellValue('H' . $i, (int)$registro->TotalAplicados)
                                ->setCellValue('I' . $i, (int)$registro->Redimidos)
                                ->setCellValue('J' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                            $i++;
                        }
                    }
                    #endregion

                    #region Seccion Usuario
                    if ($checkbox_usuario) {
                        $resultadoUsuarios = $this->getAsignacionTable()
                            ->reporteComportamiento(
                                true,
                                $empresa,
                                true,
                                $campania,
                                true,
                                $segmento,
                                true,
                                $usuario
                            );

                        $i = $i + 3;
                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->setCellValue('C' . $i, 'Segmentos')
                            ->setCellValue('D' . $i, 'Usuarios')
                            ->setCellValue('E' . $i, 'Premios')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->mergeCells('C' . $i . ':C' . ($i + 1))
                            ->mergeCells('E' . $i . ':G' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':G' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':G' . $i)->applyFromArray($styleArray);
                        }
                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . $i, 'Asignados')
                            ->setCellValue('F' . $i, 'Aplicados')
                            ->setCellValue('G' . $i, 'Redimidos')
                            ->setCellValue('H' . $i, 'Saldo');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':H' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':H' . (count($resultadoUsuarios) + $i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':H' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':H' . (count($resultadoUsuarios) + $i))->applyFromArray($styleArray2);
                        }
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':D' . (count($resultadoUsuarios) + $i))->applyFromArray($cellArray);
                        $i++;

                        $campaniaTemp = "";
                        $segmentoTemp = "";
                        foreach ($resultadoUsuarios as $registro) {
                            if ($campaniaTemp == "") {
                                $campaniaTemp = $registro->Campania;
                                $segmentoTemp = $registro->Segmento;
                                $imprimirCam = true;
                                $imprimirSeg = true;
                            } else {
                                if ($campaniaTemp != $registro->Campania || $segmentoTemp != $registro->Segmento) {
                                    $campaniaTemp = $registro->Campania;
                                    $segmentoTemp = $registro->Segmento;
                                    $imprimirSeg = true;
                                    $imprimirCam = true;
                                } else {
                                    $imprimirCam = false;
                                    $imprimirSeg = false;
                                }
                            }

                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, $registro->Empresa);
                            }
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, ($imprimirCam) ? $campaniaTemp : "")
                                ->setCellValue('C' . $i, ($imprimirSeg) ? $segmentoTemp : "")
                                ->setCellValue('D' . $i, $registro->NumeroDocumento)
                                ->setCellValue('E' . $i, (int)$registro->TotalAsignados)
                                ->setCellValue('F' . $i, (int)$registro->TotalAplicados)
                                ->setCellValue('G' . $i, (int)$registro->Redimidos)
                                ->setCellValue('H' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                            $i++;
                        }
                    }
                    #endregion
                } else {
                    #region General
                    $objPHPExcel->getActiveSheet()->setAutoFilter('A8:H' . ($registros + $inicio));
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A8:H' . ($inicio + $registros + 1))->applyFromArray($styleArray2);
                    $objPHPExcel->getActiveSheet()->getStyle('A7:H7')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A8:H8')->applyFromArray($styleArray);

                    $nombre_archivo = $this::REPORTE_COMP;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $nombre_archivo)
                        ->mergeCells('A1:B1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', 'Por Empresa')
                        ->setCellValue('B3', 'Todas')
                        ->setCellValue('B4', 'Todos');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A7', 'Empresa')
                        ->setCellValue('B7', 'Usuarios')
                        ->setCellValue('E7', 'Premios')
                        ->setCellValue('B8', 'Asignados')
                        ->setCellValue('C8', 'Aplicados')
                        ->setCellValue('D8', 'Redimidos')
                        ->setCellValue('E8', 'Asignados')
                        ->setCellValue('F8', 'Aplicados')
                        ->setCellValue('G8', 'Redimidos')
                        ->setCellValue('H8', 'Saldo')
                        ->mergeCells('A7:A8')
                        ->mergeCells('B7:D7')
                        ->mergeCells('E7:H7');

                    $i = $inicio + 2;

                    foreach ($resultado as $registro) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $registro->Empresa)
                            ->setCellValue('B' . $i, (int)$registro->UsuAsignados)
                            ->setCellValue('C' . $i, (int)$registro->UsuAplicados)
                            ->setCellValue('D' . $i, (int)$registro->UsuRedimidos)
                            ->setCellValue('E' . $i, (int)$registro->TotalAsignados)
                            ->setCellValue('F' . $i, (int)$registro->TotalAplicados)
                            ->setCellValue('G' . $i, (int)$registro->Redimidos)
                            ->setCellValue('H' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                        $i++;
                    }
                    #endregion
                }
            } elseif (!$checkbox_demo && !$checkbox_comp && $checkbox_pref) {
                $resultado = $this->getAsignacionTable()->reportePreferencia($checkbox_empresa, $empresa);
                $registros = count($resultado);
                $inicio = 6;

                if ($checkbox_empresa || $checkbox_campania || $checkbox_segmento || $checkbox_usuario) {
                    #region Opciones
                    $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                    $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                    $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                    $objPHPExcel->getActiveSheet()->setAutoFilter('A7:R' . ($registros + $inicio));
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A7:R7')->applyFromArray($styleArray);

                    if ($checkbox_usuario) {
                        $nombre_archivo = $this::REPORTE_PREF_X_USU;
                        $titulo = $nombre_archivo;
                    } elseif ($checkbox_segmento) {
                        $nombre_archivo = $this::REPORTE_PREF_X_SEG;
                        $titulo = $nombre_archivo;
                    } elseif ($checkbox_campania) {
                        $nombre_archivo = $this::REPORTE_PREF_X_CAMP;
                        $titulo = $nombre_archivo;
                    } else {
                        $nombre_archivo = $this::REPORTE_PREF_X_EMP;
                        $titulo = $nombre_archivo;
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $titulo)
                        ->mergeCells('A1:C1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                        ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                        ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                    #endregion

                    #region Seccion Empresa
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A6', 'Premios aplicados por rubros')
                        ->mergeCells('A6:I6')
                        ->setCellValue('J6', 'Premios redimidos por rubros')
                        ->mergeCells('J6:R6');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A7', 'Belleza')
                        ->setCellValue('B7', 'Productos')
                        ->setCellValue('C7', 'Restaurantes')
                        ->setCellValue('D7', 'Viajes')
                        ->setCellValue('E7', 'Salud')
                        ->setCellValue('F7', 'Entretenimiento')
                        ->setCellValue('G7', 'Tiendas')
                        ->setCellValue('H7', 'Otros')
                        ->setCellValue('I7', 'Total')
                        ->setCellValue('J7', 'Belleza')
                        ->setCellValue('K7', 'Productos')
                        ->setCellValue('L7', 'Restaurantes')
                        ->setCellValue('M7', 'Viajes')
                        ->setCellValue('N7', 'Salud')
                        ->setCellValue('O7', 'Entretenimiento')
                        ->setCellValue('P7', 'Tiendas')
                        ->setCellValue('Q7', 'Otros')
                        ->setCellValue('R7', 'Total');

                    $i = $inicio + 2;

                    $preferencias = array(
                        "ABelleza" => 0,
                        "AProductos" => 0,
                        "ARestaurantes" => 0,
                        "AViajes" => 0,
                        "ASalud" => 0,
                        "AEntretenimiento" => 0,
                        "ATiendas" => 0,
                        "AOtros" => 0,
                        "ATotal" => 0,
                        "RBelleza" => 0,
                        "RProductos" => 0,
                        "RRestaurantes" => 0,
                        "RViajes" => 0,
                        "RSalud" => 0,
                        "REntretenimiento" => 0,
                        "RTiendas" => 0,
                        "ROtros" => 0,
                        "RTotal" => 0
                    );

                    foreach ($resultado as $registro) {
                        if ($registro->Rubro == "Belleza") {
                            $preferencias['ABelleza'] = $preferencias['ABelleza'] + $registro->TotalAplicados;
                            $preferencias['RBelleza'] = $preferencias['RBelleza'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Productos") {
                            $preferencias['AProductos'] = $preferencias['AProductos'] + $registro->TotalAplicados;
                            $preferencias['RProductos'] = $preferencias['RProductos'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Restaurantes") {
                            $preferencias['ARestaurantes'] = $preferencias['ARestaurantes'] + $registro->TotalAplicados;
                            $preferencias['RRestaurantes'] = $preferencias['RRestaurantes'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Viajes") {
                            $preferencias['AViajes'] = $preferencias['AViajes'] + $registro->TotalAplicados;
                            $preferencias['RViajes'] = $preferencias['RViajes'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Salud") {
                            $preferencias['ASalud'] = $preferencias['ASalud'] + $registro->TotalAplicados;
                            $preferencias['RSalud'] = $preferencias['RSalud'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Entretenimiento") {
                            $preferencias['AEntretenimiento'] = $preferencias['AEntretenimiento'] + $registro->TotalAplicados;
                            $preferencias['REntretenimiento'] = $preferencias['REntretenimiento'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Tiendas") {
                            $preferencias['ATiendas'] = $preferencias['ATiendas'] + $registro->TotalAplicados;
                            $preferencias['RTiendas'] = $preferencias['RTiendas'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Otros") {
                            $preferencias['AOtros'] = $preferencias['AOtros'] + $registro->TotalAplicados;
                            $preferencias['ROtros'] = $preferencias['ROtros'] + $registro->Redimidos;
                        }

                        $preferencias['ATotal'] = $preferencias['ATotal'] + $registro->TotalAplicados;
                        $preferencias['RTotal'] = $preferencias['RTotal'] + $registro->Redimidos;
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $i, (int)$preferencias['ABelleza'])
                        ->setCellValue('B' . $i, (int)$preferencias['AProductos'])
                        ->setCellValue('C' . $i, (int)$preferencias['ARestaurantes'])
                        ->setCellValue('D' . $i, (int)$preferencias['AViajes'])
                        ->setCellValue('E' . $i, (int)$preferencias['ASalud'])
                        ->setCellValue('F' . $i, (int)$preferencias['AEntretenimiento'])
                        ->setCellValue('G' . $i, (int)$preferencias['ATiendas'])
                        ->setCellValue('H' . $i, (int)$preferencias['AOtros'])
                        ->setCellValue('I' . $i, (int)$preferencias['ATotal'])
                        ->setCellValue('J' . $i, (int)$preferencias['RBelleza'])
                        ->setCellValue('K' . $i, (int)$preferencias['RProductos'])
                        ->setCellValue('L' . $i, (int)$preferencias['RRestaurantes'])
                        ->setCellValue('M' . $i, (int)$preferencias['RViajes'])
                        ->setCellValue('N' . $i, (int)$preferencias['RSalud'])
                        ->setCellValue('O' . $i, (int)$preferencias['REntretenimiento'])
                        ->setCellValue('P' . $i, (int)$preferencias['RTiendas'])
                        ->setCellValue('Q' . $i, (int)$preferencias['ROtros'])
                        ->setCellValue('R' . $i, (int)$preferencias['RTotal']);

                    $objPHPExcel->getActiveSheet()->getStyle('A8:R' . ($i))->applyFromArray($styleArray2);

                    #endregion

                    #region Seccion Campañas
                    if ($checkbox_campania or $checkbox_segmento or $checkbox_usuario) {
                        $resultadoCampania = $this->getAsignacionTable()
                            ->reportePreferencia($checkbox_empresa, $empresa, $checkbox_campania, $campania);

                        $i = $i + 3;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->setCellValue('C' . $i, 'Premios aplicados por rubros')
                            ->mergeCells('C' . $i . ':K' . $i)
                            ->setCellValue('L' . $i, 'Premios redimidos por rubros')
                            ->mergeCells('L' . $i . ':T' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':T' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':T' . $i)->applyFromArray($styleArray);
                        }
                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('C' . $i, 'Belleza')
                            ->setCellValue('D' . $i, 'Productos')
                            ->setCellValue('E' . $i, 'Restaurantes')
                            ->setCellValue('F' . $i, 'Viajes')
                            ->setCellValue('G' . $i, 'Salud')
                            ->setCellValue('H' . $i, 'Entretenimiento')
                            ->setCellValue('I' . $i, 'Tiendas')
                            ->setCellValue('J' . $i, 'Otros')
                            ->setCellValue('K' . $i, 'Total')
                            ->setCellValue('L' . $i, 'Belleza')
                            ->setCellValue('M' . $i, 'Productos')
                            ->setCellValue('N' . $i, 'Restaurantes')
                            ->setCellValue('O' . $i, 'Viajes')
                            ->setCellValue('P' . $i, 'Salud')
                            ->setCellValue('Q' . $i, 'Entretenimiento')
                            ->setCellValue('R' . $i, 'Tiendas')
                            ->setCellValue('S' . $i, 'Otros')
                            ->setCellValue('T' . $i, 'Total');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':T' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':T' . $i)->applyFromArray($styleArray);
                        }

                        $i++;
                        $total = $i;

                        $preferencias = array();
                        $datoCampania = "";
                        $count = 0;
                        foreach ($resultadoCampania as $registro) {
                            if ($datoCampania == "") {
                                $datoCampania = $registro->Campania;
                                $preferencias[$count]['Empresa'] = $registro->Empresa;
                                $preferencias[$count]['Campania'] = $registro->Campania;
                            } else {
                                if ($datoCampania != $registro->Campania) {
                                    $datoCampania = $registro->Campania;
                                    $count++;
                                    $preferencias[$count]['Empresa'] = $registro->Empresa;
                                    $preferencias[$count]['Campania'] = $registro->Campania;
                                }
                            }

                            if ($registro->Rubro == "Belleza") {
                                @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                                @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                                @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                            }

                            @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                        }

                        for ($j = 0; $j <= $count; $j++) {
                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)->
                                setCellValue('A' . $i, @$preferencias[$j]['Empresa']);
                            }
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, @$preferencias[$j]['Campania'])
                                ->setCellValue('C' . $i, (int)@$preferencias[$j]['ABelleza'])
                                ->setCellValue('D' . $i, (int)@$preferencias[$j]['AProductos'])
                                ->setCellValue('E' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                                ->setCellValue('F' . $i, (int)@$preferencias[$j]['AViajes'])
                                ->setCellValue('G' . $i, (int)@$preferencias[$j]['ASalud'])
                                ->setCellValue('H' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                                ->setCellValue('I' . $i, (int)@$preferencias[$j]['ATiendas'])
                                ->setCellValue('J' . $i, (int)@$preferencias[$j]['AOtros'])
                                ->setCellValue('K' . $i, (int)@$preferencias[$j]['ATotal'])
                                ->setCellValue('L' . $i, (int)@$preferencias[$j]['RBelleza'])
                                ->setCellValue('M' . $i, (int)@$preferencias[$j]['RProductos'])
                                ->setCellValue('N' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                                ->setCellValue('O' . $i, (int)@$preferencias[$j]['RViajes'])
                                ->setCellValue('P' . $i, (int)@$preferencias[$j]['RSalud'])
                                ->setCellValue('Q' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                                ->setCellValue('R' . $i, (int)@$preferencias[$j]['RTiendas'])
                                ->setCellValue('S' . $i, (int)@$preferencias[$j]['ROtros'])
                                ->setCellValue('T' . $i, (int)@$preferencias[$j]['RTotal']);
                            $i++;
                        }

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $total . ':T' . ($i - 1))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $total . ':T' . ($i - 1))->applyFromArray($styleArray2);
                        }
                    }
                    #endregion

                    #region Seccion Segmentos
                    if ($checkbox_segmento or $checkbox_usuario) {
                        $resultadoSegmento = $this->getAsignacionTable()
                            ->reportePreferencia(
                                $checkbox_empresa,
                                $empresa,
                                $checkbox_campania,
                                $campania,
                                $checkbox_segmento,
                                $segmento
                            );

                        $i = $i + 3;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->setCellValue('C' . $i, 'Segmento')
                            ->mergeCells('C' . $i . ':C' . ($i + 1))
                            ->setCellValue('D' . $i, 'Premios aplicados por rubros')
                            ->mergeCells('D' . $i . ':L' . $i)
                            ->setCellValue('M' . $i, 'Premios redimidos por rubros')
                            ->mergeCells('M' . $i . ':U' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':U' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':U' . $i)->applyFromArray($styleArray);
                        }

                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . $i, 'Belleza')
                            ->setCellValue('E' . $i, 'Productos')
                            ->setCellValue('F' . $i, 'Restaurantes')
                            ->setCellValue('G' . $i, 'Viajes')
                            ->setCellValue('H' . $i, 'Salud')
                            ->setCellValue('I' . $i, 'Entretenimiento')
                            ->setCellValue('J' . $i, 'Tiendas')
                            ->setCellValue('K' . $i, 'Otros')
                            ->setCellValue('L' . $i, 'Total')
                            ->setCellValue('M' . $i, 'Belleza')
                            ->setCellValue('N' . $i, 'Productos')
                            ->setCellValue('O' . $i, 'Restaurantes')
                            ->setCellValue('P' . $i, 'Viajes')
                            ->setCellValue('Q' . $i, 'Salud')
                            ->setCellValue('R' . $i, 'Entretenimiento')
                            ->setCellValue('S' . $i, 'Tiendas')
                            ->setCellValue('T' . $i, 'Otros')
                            ->setCellValue('U' . $i, 'Total');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':U' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':U' . $i)->applyFromArray($styleArray);
                        }

                        $i++;
                        $total = $i;

                        $preferencias = array();
                        $campaniaTemp = "";
                        $segmentoTemp = "";
                        $count = 0;
                        foreach ($resultadoSegmento as $registro) {
                            if ($campaniaTemp == "" && $segmentoTemp == "") {
                                $campaniaTemp = $registro->Campania;
                                $segmentoTemp = $registro->Segmento;
                                $preferencias[$count]['Empresa'] = $registro->Empresa;
                                $preferencias[$count]['Campania'] = $registro->Campania;
                            } else {
                                if ($campaniaTemp != $registro->Campania || $segmentoTemp != $registro->Segmento) {
                                    $campaniaTemp = $registro->Campania;
                                    $segmentoTemp = $registro->Segmento;
                                    $count++;
                                    $preferencias[$count]['Empresa'] = $registro->Empresa;
                                    $preferencias[$count]['Campania'] = $registro->Campania;
                                }
                            }

                            $preferencias[$count]['Segmento'] = $registro->Segmento;
                            if ($registro->Rubro == "Belleza") {
                                @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                                @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                                @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                            }

                            @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                        }

                        for ($j = 0; $j <= $count; $j++) {
                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, @$preferencias[$j]['Empresa']);
                            }

                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, @$preferencias[$j]['Campania'])
                                ->setCellValue('C' . $i, @$preferencias[$j]['Segmento'])
                                ->setCellValue('D' . $i, (int)@$preferencias[$j]['ABelleza'])
                                ->setCellValue('E' . $i, (int)@$preferencias[$j]['AProductos'])
                                ->setCellValue('F' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                                ->setCellValue('G' . $i, (int)@$preferencias[$j]['AViajes'])
                                ->setCellValue('H' . $i, (int)@$preferencias[$j]['ASalud'])
                                ->setCellValue('I' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                                ->setCellValue('J' . $i, (int)@$preferencias[$j]['ATiendas'])
                                ->setCellValue('K' . $i, (int)@$preferencias[$j]['AOtros'])
                                ->setCellValue('L' . $i, (int)@$preferencias[$j]['ATotal'])
                                ->setCellValue('M' . $i, (int)@$preferencias[$j]['RBelleza'])
                                ->setCellValue('N' . $i, (int)@$preferencias[$j]['RProductos'])
                                ->setCellValue('O' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                                ->setCellValue('P' . $i, (int)@$preferencias[$j]['RViajes'])
                                ->setCellValue('Q' . $i, (int)@$preferencias[$j]['RSalud'])
                                ->setCellValue('R' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                                ->setCellValue('S' . $i, (int)@$preferencias[$j]['RTiendas'])
                                ->setCellValue('T' . $i, (int)@$preferencias[$j]['ROtros'])
                                ->setCellValue('U' . $i, (int)@$preferencias[$j]['RTotal']);
                            $i++;
                        }

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $total . ':U' . ($i - 1))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $total . ':U' . ($i - 1))->applyFromArray($styleArray2);
                        }
                    }
                    #endregion

                    #region Seccion Usuario
                    if ($checkbox_usuario) {
                        $resultadoUsuarios = $this->getAsignacionTable()
                            ->reportePreferencia(
                                $checkbox_empresa,
                                $empresa,
                                $checkbox_campania,
                                $campania,
                                $checkbox_segmento,
                                $segmento,
                                $checkbox_usuario,
                                $usuario
                            );

                        $i = $i + 3;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->setCellValue('C' . $i, 'Segmento')
                            ->mergeCells('C' . $i . ':C' . ($i + 1))
                            ->setCellValue('D' . $i, 'Usuarios')
                            ->mergeCells('D' . $i . ':D' . ($i + 1))
                            ->setCellValue('E' . $i, 'Premios aplicados por rubros')
                            ->mergeCells('E' . $i . ':M' . $i)
                            ->setCellValue('N' . $i, 'Premios redimidos por rubros')
                            ->mergeCells('N' . $i . ':V' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':V' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':V' . $i)->applyFromArray($styleArray);
                        }

                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('E' . $i, 'Belleza')
                            ->setCellValue('F' . $i, 'Productos')
                            ->setCellValue('G' . $i, 'Restaurantes')
                            ->setCellValue('H' . $i, 'Viajes')
                            ->setCellValue('I' . $i, 'Salud')
                            ->setCellValue('J' . $i, 'Entretenimiento')
                            ->setCellValue('K' . $i, 'Tiendas')
                            ->setCellValue('L' . $i, 'Otros')
                            ->setCellValue('M' . $i, 'Total')
                            ->setCellValue('N' . $i, 'Belleza')
                            ->setCellValue('O' . $i, 'Productos')
                            ->setCellValue('P' . $i, 'Restaurantes')
                            ->setCellValue('Q' . $i, 'Viajes')
                            ->setCellValue('R' . $i, 'Salud')
                            ->setCellValue('S' . $i, 'Entretenimiento')
                            ->setCellValue('T' . $i, 'Tiendas')
                            ->setCellValue('U' . $i, 'Otros')
                            ->setCellValue('V' . $i, 'Total');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':V' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':V' . $i)->applyFromArray($styleArray);
                        }
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':D' . (count($resultadoUsuarios) + $i))->applyFromArray($cellArray);

                        $i++;
                        $total = $i;
                        $preferencias = array();
                        $dataCampania = "";
                        $dataSegmento = "";
                        $dataUsuario = "";
                        $count = 0;
                        foreach ($resultadoUsuarios as $registro) {
                            if ($dataUsuario == "" && $dataCampania == "" && $dataSegmento == "") {
                                $dataCampania = $registro->Campania;
                                $dataSegmento = $registro->Segmento;
                                $dataUsuario = $registro->NumeroDocumento;
                                $preferencias[$count]['Empresa'] = $registro->Empresa;
                                $preferencias[$count]['Campania'] = $registro->Campania;
                            } else {
                                if ($dataCampania != $registro->Campania ||
                                    $dataSegmento != $registro->Segmento ||
                                    $dataUsuario != $registro->NumeroDocumento
                                ) {
                                    $dataCampania = $registro->Campania;
                                    $dataSegmento = $registro->Segmento;
                                    $dataUsuario = $registro->NumeroDocumento;
                                    $count++;
                                    $preferencias[$count]['Empresa'] = $registro->Empresa;
                                    $preferencias[$count]['Campania'] = $registro->Campania;
                                }
                            }

                            $preferencias[$count]['Segmento'] = $registro->Segmento;
                            $preferencias[$count]['NumeroDocumento'] = $registro->NumeroDocumento;
                            if ($registro->Rubro == "Belleza") {
                                @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                                @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                                @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                            }

                            @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                        }

                        for ($j = 0; $j <= $count; $j++) {
                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, @$preferencias[$j]['Empresa']);
                            }

                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, @$preferencias[$j]['Campania'])
                                ->setCellValue('C' . $i, @$preferencias[$j]['Segmento'])
                                ->setCellValue('D' . $i, @$preferencias[$j]['NumeroDocumento'])
                                ->setCellValue('E' . $i, (int)@$preferencias[$j]['ABelleza'])
                                ->setCellValue('F' . $i, (int)@$preferencias[$j]['AProductos'])
                                ->setCellValue('G' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                                ->setCellValue('H' . $i, (int)@$preferencias[$j]['AViajes'])
                                ->setCellValue('I' . $i, (int)@$preferencias[$j]['ASalud'])
                                ->setCellValue('J' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                                ->setCellValue('K' . $i, (int)@$preferencias[$j]['ATiendas'])
                                ->setCellValue('L' . $i, (int)@$preferencias[$j]['AOtros'])
                                ->setCellValue('M' . $i, (int)@$preferencias[$j]['ATotal'])
                                ->setCellValue('N' . $i, (int)@$preferencias[$j]['RBelleza'])
                                ->setCellValue('O' . $i, (int)@$preferencias[$j]['RProductos'])
                                ->setCellValue('P' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                                ->setCellValue('Q' . $i, (int)@$preferencias[$j]['RViajes'])
                                ->setCellValue('R' . $i, (int)@$preferencias[$j]['RSalud'])
                                ->setCellValue('S' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                                ->setCellValue('T' . $i, (int)@$preferencias[$j]['RTiendas'])
                                ->setCellValue('U' . $i, (int)@$preferencias[$j]['ROtros'])
                                ->setCellValue('V' . $i, (int)@$preferencias[$j]['RTotal']);
                            $i++;
                        }

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $total . ':V' . ($i - 1))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $total . ':V' . ($i - 1))->applyFromArray($styleArray2);
                        }
                    }
                    #endregion
                } else {
                    #region General
                    $objPHPExcel->getActiveSheet()->setAutoFilter('A7:S' . ($registros + $inicio));
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
                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A6:S6')->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getStyle('A7:S7')->applyFromArray($styleArray);

                    $nombre_archivo = $this::REPORTE_PREF;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $nombre_archivo)
                        ->mergeCells('A1:B1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', 'Por Empresa')
                        ->setCellValue('B3', 'Todas')
                        ->setCellValue('B4', 'Todos');


                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A6', 'Empresa')
                        ->mergeCells('A6:A7')
                        ->setCellValue('B6', 'Premios aplicados por rubros')
                        ->mergeCells('B6:J6')
                        ->setCellValue('K6', 'Premios redimidos por rubros')
                        ->mergeCells('K6:Q6');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B7', 'Belleza')
                        ->setCellValue('C7', 'Productos')
                        ->setCellValue('D7', 'Restaurantes')
                        ->setCellValue('E7', 'Viajes')
                        ->setCellValue('F7', 'Salud')
                        ->setCellValue('G7', 'Entretenimiento')
                        ->setCellValue('H7', 'Tienda')
                        ->setCellValue('I7', 'Otros')
                        ->setCellValue('J7', 'Total')
                        ->setCellValue('K7', 'Belleza')
                        ->setCellValue('L7', 'Productos')
                        ->setCellValue('M7', 'Restaurantes')
                        ->setCellValue('N7', 'Viajes')
                        ->setCellValue('O7', 'Salud')
                        ->setCellValue('P7', 'Entretenimiento')
                        ->setCellValue('Q7', 'Tienda')
                        ->setCellValue('R7', 'Otros')
                        ->setCellValue('S7', 'Total');

                    $i = $inicio + 2;

                    $preferencias = array();
                    $empresa = "";
                    $count = 0;
                    foreach ($resultado as $registro) {
                        if ($empresa == "") {
                            $empresa = $registro->Empresa;
                            $preferencias[$count]['Empresa'] = $registro->Empresa;
                        } else {
                            if ($empresa != $registro->Empresa) {
                                $empresa = $registro->Empresa;
                                $count++;
                                $preferencias[$count]['Empresa'] = $registro->Empresa;
                            }
                        }

                        if ($registro->Rubro == "Belleza") {
                            @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Productos") {
                            @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Restaurantes") {
                            @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Viajes") {
                            @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Salud") {
                            @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Entretenimiento") {
                            @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                            @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Tiendas") {
                            @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                        } elseif ($registro->Rubro == "Otros") {
                            @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                            @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                        }

                        @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                    }

                    for ($j = 0; $j <= $count; $j++) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, @$preferencias[$j]['Empresa'])
                            ->setCellValue('B' . $i, (int)@$preferencias[$j]['ABelleza'])
                            ->setCellValue('C' . $i, (int)@$preferencias[$j]['AProductos'])
                            ->setCellValue('D' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                            ->setCellValue('E' . $i, (int)@$preferencias[$j]['AViajes'])
                            ->setCellValue('F' . $i, (int)@$preferencias[$j]['ASalud'])
                            ->setCellValue('G' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                            ->setCellValue('H' . $i, (int)@$preferencias[$j]['ATiendas'])
                            ->setCellValue('I' . $i, (int)@$preferencias[$j]['AOtros'])
                            ->setCellValue('J' . $i, (int)@$preferencias[$j]['ATotal'])
                            ->setCellValue('K' . $i, (int)@$preferencias[$j]['RBelleza'])
                            ->setCellValue('L' . $i, (int)@$preferencias[$j]['RProductos'])
                            ->setCellValue('M' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                            ->setCellValue('N' . $i, (int)@$preferencias[$j]['RViajes'])
                            ->setCellValue('O' . $i, (int)@$preferencias[$j]['RSalud'])
                            ->setCellValue('P' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                            ->setCellValue('Q' . $i, (int)@$preferencias[$j]['RTiendas'])
                            ->setCellValue('R' . $i, (int)@$preferencias[$j]['ROtros'])
                            ->setCellValue('S' . $i, (int)@$preferencias[$j]['RTotal']);
                        $i++;
                    }
                    $objPHPExcel->getActiveSheet()->getStyle('A8:S' . ($i - 1))->applyFromArray($styleArray2);

                    #endregion
                }
            } elseif (!$checkbox_demo && $checkbox_comp && $checkbox_pref) {
                $resultadoComp = $this->getAsignacionTable()->reporteComportamiento($checkbox_empresa, $empresa);
                $resultadoPref = $this->getAsignacionTable()->reportePreferencia($checkbox_empresa, $empresa);

                $inicio = 6;
                if ($checkbox_empresa || $checkbox_campania || $checkbox_segmento || $checkbox_usuario) {
                    #region Opciones
                    $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                    $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                    $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);

                    if ($checkbox_usuario) {
                        $nombre_archivo = $this::REPORTE_USU_COMP_PREF;
                        $titulo = $nombre_archivo;
                    } elseif ($checkbox_segmento) {
                        $nombre_archivo = $this::REPORTE_SEG_COMP_PREF;
                        $titulo = $nombre_archivo;
                    } elseif ($checkbox_campania) {
                        $nombre_archivo = $this::REPORTE_CAMP_COMP_PREF;
                        $titulo = $nombre_archivo;
                    } else {
                        $nombre_archivo = $this::REPORTE_EC_COMP_PREF;
                        $titulo = $nombre_archivo;
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $titulo)
                        ->mergeCells('A1:F1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                        ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                        ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                    $i = $inicio;
                    #endregion

                    #region Seccion Empresa Comportamiento
                    if (!$checkbox_usuario) {
                        $i = $inicio + 2;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A6', 'Empresa')
                                ->mergeCells('A6:A7');
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B6', 'Usuario')
                            ->mergeCells('B6:D6')
                            ->setCellValue('E6', 'Premios')
                            ->mergeCells('E6:H6')
                            ->setCellValue('I6', 'Premios en Rubros aplicados')
                            ->mergeCells('I6:Q6')
                            ->setCellValue('R6', 'Rubros en rubros redimidos')
                            ->mergeCells('R6:Z6');

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B7', 'Asignados')
                            ->setCellValue('C7', 'Aplicados')
                            ->setCellValue('D7', 'Redimidos')
                            ->setCellValue('E7', 'Asignados')
                            ->setCellValue('F7', 'Aplicados')
                            ->setCellValue('G7', 'Redimidos')
                            ->setCellValue('H7', 'Saldo')
                            ->setCellValue('I7', 'Belleza')
                            ->setCellValue('J7', 'Productos')
                            ->setCellValue('K7', 'Restaurantes')
                            ->setCellValue('L7', 'Viajes')
                            ->setCellValue('M7', 'Salud')
                            ->setCellValue('N7', 'Entretenimiento')
                            ->setCellValue('O7', 'Tiendas')
                            ->setCellValue('P7', 'Otros')
                            ->setCellValue('Q7', 'Total')
                            ->setCellValue('R7', 'Belleza')
                            ->setCellValue('S7', 'Productos')
                            ->setCellValue('T7', 'Restaurantes')
                            ->setCellValue('U7', 'Viajes')
                            ->setCellValue('V7', 'Salud')
                            ->setCellValue('W7', 'Entretenimiento')
                            ->setCellValue('X7', 'Tiendas')
                            ->setCellValue('Y7', 'Otros')
                            ->setCellValue('Z7', 'Total');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A6:Z7')->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B6:Z7')->applyFromArray($styleArray);
                        }

                        $sumUsuAsignados = 0;
                        $sumUsuAplicados = 0;
                        $sumUsuRedimidos = 0;
                        $sumTotalAsignados = 0;
                        $sumTotalAplicados = 0;
                        $sumRedimidos = 0;
                        $sumSaldo = 0;
                        $dataEmpresas = "";

                        foreach ($resultadoComp as $registro) {
                            $dataEmpresas = $dataEmpresas . $registro->Empresa . "; ";
                            $sumUsuAsignados = $sumUsuAsignados + $registro->UsuAsignados;
                            $sumUsuAplicados = $sumUsuAplicados + $registro->UsuAplicados;
                            $sumUsuRedimidos = $sumUsuRedimidos + $registro->UsuRedimidos;
                            $sumTotalAsignados = $sumTotalAsignados + $registro->TotalAsignados;
                            $sumTotalAplicados = $sumTotalAplicados + $registro->TotalAplicados;
                            $sumRedimidos = $sumRedimidos + $registro->Redimidos;
                            $sumSaldo = $sumSaldo + $registro->TotalAsignados - $registro->TotalAplicados;
                        }

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, $dataEmpresas);
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, $sumUsuAsignados)
                            ->setCellValue('C' . $i, $sumUsuAplicados)
                            ->setCellValue('D' . $i, $sumUsuRedimidos)
                            ->setCellValue('E' . $i, $sumTotalAsignados)
                            ->setCellValue('F' . $i, $sumTotalAplicados)
                            ->setCellValue('G' . $i, $sumRedimidos)
                            ->setCellValue('H' . $i, $sumSaldo);

                        $preferencias = array(
                            "ABelleza" => 0,
                            "AProductos" => 0,
                            "ARestaurantes" => 0,
                            "AViajes" => 0,
                            "ASalud" => 0,
                            "AEntretenimiento" => 0,
                            "ATiendas" => 0,
                            "AOtros" => 0,
                            "ATotal" => 0,
                            "RBelleza" => 0,
                            "RProductos" => 0,
                            "RRestaurantes" => 0,
                            "RViajes" => 0,
                            "RSalud" => 0,
                            "REntretenimiento" => 0,
                            "RTiendas" => 0,
                            "ROtros" => 0,
                            "RTotal" => 0
                        );

                        foreach ($resultadoPref as $registro) {
                            if ($registro->Rubro == "Belleza") {
                                $preferencias['ABelleza'] = $preferencias['ABelleza'] + $registro->TotalAplicados;
                                $preferencias['RBelleza'] = $preferencias['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                $preferencias['AProductos'] = $preferencias['AProductos'] + $registro->TotalAplicados;
                                $preferencias['RProductos'] = $preferencias['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                $preferencias['ARestaurantes'] = $preferencias['ARestaurantes'] + $registro->TotalAplicados;
                                $preferencias['RRestaurantes'] = $preferencias['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                $preferencias['AViajes'] = $preferencias['AViajes'] + $registro->TotalAplicados;
                                $preferencias['RViajes'] = $preferencias['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                $preferencias['ASalud'] = $preferencias['ASalud'] + $registro->TotalAplicados;
                                $preferencias['RSalud'] = $preferencias['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                $preferencias['AEntretenimiento'] = $preferencias['AEntretenimiento'] + $registro->TotalAplicados;
                                $preferencias['REntretenimiento'] = $preferencias['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                $preferencias['ATiendas'] = $preferencias['ATiendas'] + $registro->TotalAplicados;
                                $preferencias['RTiendas'] = $preferencias['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                $preferencias['AOtros'] = $preferencias['AOtros'] + $registro->TotalAplicados;
                                $preferencias['ROtros'] = $preferencias['ROtros'] + $registro->Redimidos;
                            }

                            $preferencias['ATotal'] = $preferencias['ATotal'] + $registro->TotalAplicados;
                            $preferencias['RTotal'] = $preferencias['RTotal'] + $registro->Redimidos;
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('I' . $i, (int)$preferencias['ABelleza'])
                            ->setCellValue('J' . $i, (int)$preferencias['AProductos'])
                            ->setCellValue('K' . $i, (int)$preferencias['ARestaurantes'])
                            ->setCellValue('L' . $i, (int)$preferencias['AViajes'])
                            ->setCellValue('M' . $i, (int)$preferencias['ASalud'])
                            ->setCellValue('N' . $i, (int)$preferencias['AEntretenimiento'])
                            ->setCellValue('O' . $i, (int)$preferencias['ATiendas'])
                            ->setCellValue('P' . $i, (int)$preferencias['AOtros'])
                            ->setCellValue('Q' . $i, (int)$preferencias['ATotal'])
                            ->setCellValue('R' . $i, (int)$preferencias['RBelleza'])
                            ->setCellValue('S' . $i, (int)$preferencias['RProductos'])
                            ->setCellValue('T' . $i, (int)$preferencias['RRestaurantes'])
                            ->setCellValue('U' . $i, (int)$preferencias['RViajes'])
                            ->setCellValue('V' . $i, (int)$preferencias['RSalud'])
                            ->setCellValue('W' . $i, (int)$preferencias['REntretenimiento'])
                            ->setCellValue('X' . $i, (int)$preferencias['RTiendas'])
                            ->setCellValue('Y' . $i, (int)$preferencias['ROtros'])
                            ->setCellValue('Z' . $i, (int)$preferencias['RTotal']);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A8:Z' . ($i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B8:Z' . ($i))->applyFromArray($styleArray2);
                        }
                    }
                    #endregion

                    $posCampania = $i;
                    #region Seccion Campañas Comportamiento
                    if (($checkbox_campania and !$checkbox_usuario) or ($checkbox_segmento and !$checkbox_usuario)) {
                        $resultadoCampania = $this->getAsignacionTable()
                            ->reporteComportamiento($checkbox_empresa, $empresa, $checkbox_campania, $campania);

                        $i = $i + 3;
                        $posCampania = $i;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->setCellValue('C' . $i, 'Usuarios')
                            ->setCellValue('F' . $i, 'Premios')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->mergeCells('C' . $i . ':E' . $i)
                            ->mergeCells('F' . $i . ':I' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
                        }

                        $i++;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('C' . $i, 'Asignados')
                            ->setCellValue('D' . $i, 'Aplicados')
                            ->setCellValue('E' . $i, 'Redimidos')
                            ->setCellValue('F' . $i, 'Asignados')
                            ->setCellValue('G' . $i, 'Aplicados')
                            ->setCellValue('H' . $i, 'Redimidos')
                            ->setCellValue('I' . $i, 'Saldo');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . (count($resultadoCampania) + $i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . (count($resultadoCampania) + $i))->applyFromArray($styleArray2);
                        }
                        $i++;

                        foreach ($resultadoCampania as $registro) {
                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, $registro->Empresa);
                            }

                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, $registro->Campania)
                                ->setCellValue('C' . $i, (int)$registro->UsuAsignados)
                                ->setCellValue('D' . $i, (int)$registro->UsuAplicados)
                                ->setCellValue('E' . $i, (int)$registro->UsuRedimidos)
                                ->setCellValue('F' . $i, (int)$registro->TotalAsignados)
                                ->setCellValue('G' . $i, (int)$registro->TotalAplicados)
                                ->setCellValue('H' . $i, (int)$registro->Redimidos)
                                ->setCellValue('I' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                            $i++;
                        }
                    }
                    #endregion

                    #region Seccion Campañas Preferencia
                    if (($checkbox_campania and !$checkbox_usuario) or ($checkbox_segmento and !$checkbox_usuario)) {
                        $resultadoCampania = $this->getAsignacionTable()
                            ->reportePreferencia($checkbox_empresa, $empresa, $checkbox_campania, $campania);

                        $i = $posCampania;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('J' . $i, 'Premios aplicados por rubros')
                            ->mergeCells('J' . $i . ':R' . $i)
                            ->setCellValue('S' . $i, 'Premios redimidos por rubros')
                            ->mergeCells('S' . $i . ':AA' . $i);

                        $objPHPExcel->getActiveSheet()->getStyle('J' . $i . ':AA' . $i)->applyFromArray($styleArray);

                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('J' . $i, 'Belleza')
                            ->setCellValue('K' . $i, 'Productos')
                            ->setCellValue('L' . $i, 'Restaurantes')
                            ->setCellValue('M' . $i, 'Viajes')
                            ->setCellValue('N' . $i, 'Salud')
                            ->setCellValue('O' . $i, 'Entretenimiento')
                            ->setCellValue('P' . $i, 'Tiendas')
                            ->setCellValue('Q' . $i, 'Otros')
                            ->setCellValue('R' . $i, 'Total')
                            ->setCellValue('S' . $i, 'Belleza')
                            ->setCellValue('T' . $i, 'Productos')
                            ->setCellValue('U' . $i, 'Restaurantes')
                            ->setCellValue('V' . $i, 'Viajes')
                            ->setCellValue('W' . $i, 'Salud')
                            ->setCellValue('X' . $i, 'Entretenimiento')
                            ->setCellValue('Y' . $i, 'Tiendas')
                            ->setCellValue('Z' . $i, 'Otros')
                            ->setCellValue('AA' . $i, 'Total');

                        $objPHPExcel->getActiveSheet()->getStyle('J' . $i . ':AA' . $i)->applyFromArray($styleArray);

                        $i++;
                        $total = $i;

                        $preferencias = array();
                        $datoCampania = "";
                        $count = 0;
                        foreach ($resultadoCampania as $registro) {
                            if ($datoCampania == "") {
                                $datoCampania = $registro->Campania;
                                $preferencias[$count]['Campania'] = $registro->Campania;
                            } else {
                                if ($datoCampania != $registro->Campania) {
                                    $datoCampania = $registro->Campania;
                                    $count++;
                                    $preferencias[$count]['Campania'] = $registro->Campania;
                                }
                            }

                            if ($registro->Rubro == "Belleza") {
                                @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                                @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                                @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                            }

                            @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                        }

                        for ($j = 0; $j <= $count; $j++) {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('J' . $i, (int)@$preferencias[$j]['ABelleza'])
                                ->setCellValue('K' . $i, (int)@$preferencias[$j]['AProductos'])
                                ->setCellValue('L' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                                ->setCellValue('M' . $i, (int)@$preferencias[$j]['AViajes'])
                                ->setCellValue('N' . $i, (int)@$preferencias[$j]['ASalud'])
                                ->setCellValue('O' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                                ->setCellValue('P' . $i, (int)@$preferencias[$j]['ATiendas'])
                                ->setCellValue('Q' . $i, (int)@$preferencias[$j]['AOtros'])
                                ->setCellValue('R' . $i, (int)@$preferencias[$j]['ATotal'])
                                ->setCellValue('S' . $i, (int)@$preferencias[$j]['RBelleza'])
                                ->setCellValue('T' . $i, (int)@$preferencias[$j]['RProductos'])
                                ->setCellValue('U' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                                ->setCellValue('V' . $i, (int)@$preferencias[$j]['RViajes'])
                                ->setCellValue('W' . $i, (int)@$preferencias[$j]['RSalud'])
                                ->setCellValue('X' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                                ->setCellValue('Y' . $i, (int)@$preferencias[$j]['RTiendas'])
                                ->setCellValue('Z' . $i, (int)@$preferencias[$j]['ROtros'])
                                ->setCellValue('AA' . $i, (int)@$preferencias[$j]['RTotal']);
                            $i++;
                        }

                        $objPHPExcel->getActiveSheet()->getStyle('J' . $total . ':AA' . ($i - 1))->applyFromArray($styleArray2);
                    }
                    #endregion

                    $posSegmento = $i;
                    #region Seccion Segmentos Comportamiento
                    if ($checkbox_segmento and !$checkbox_usuario) {
                        $resultadoSegmento = $this->getAsignacionTable()
                            ->reporteComportamiento(
                                $checkbox_empresa,
                                $empresa,
                                $checkbox_campania,
                                $campania,
                                $checkbox_segmento,
                                $segmento
                            );

                        $i = $i + 3;
                        $posSegmento = $i;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->setCellValue('C' . $i, 'Segmentos')
                            ->setCellValue('D' . $i, 'Usuarios')
                            ->setCellValue('G' . $i, 'Premios')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->mergeCells('C' . $i . ':C' . ($i + 1))
                            ->mergeCells('D' . $i . ':F' . $i)
                            ->mergeCells('G' . $i . ':J' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':J' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);
                        }

                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D' . $i, 'Asignados')
                            ->setCellValue('E' . $i, 'Aplicados')
                            ->setCellValue('F' . $i, 'Redimidos')
                            ->setCellValue('G' . $i, 'Asignados')
                            ->setCellValue('H' . $i, 'Aplicados')
                            ->setCellValue('I' . $i, 'Redimidos')
                            ->setCellValue('J' . $i, 'Saldo');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':J' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':J' . (count($resultadoSegmento) + $i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':J' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':J' . (count($resultadoSegmento) + $i))->applyFromArray($styleArray2);
                        }

                        $i++;

                        $campaniaTemp = "";
                        foreach ($resultadoSegmento as $registro) {
                            if ($campaniaTemp == "") {
                                $campaniaTemp = $registro->Campania;
                            } else {
                                if ($campaniaTemp != $registro->Campania) {
                                    $campaniaTemp = $registro->Campania;
                                }
                            }

                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, $registro->Empresa);
                            }
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, $campaniaTemp)
                                ->setCellValue('C' . $i, $registro->Segmento)
                                ->setCellValue('D' . $i, (int)$registro->UsuAsignados)
                                ->setCellValue('E' . $i, (int)$registro->UsuAplicados)
                                ->setCellValue('F' . $i, (int)$registro->UsuRedimidos)
                                ->setCellValue('G' . $i, (int)$registro->TotalAsignados)
                                ->setCellValue('H' . $i, (int)$registro->TotalAplicados)
                                ->setCellValue('I' . $i, (int)$registro->Redimidos)
                                ->setCellValue('J' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                            $i++;
                        }
                    }
                    #endregion

                    #region Seccion Segmentos Preferencias
                    if ($checkbox_segmento and !$checkbox_usuario) {
                        $resultadoSegmento = $this->getAsignacionTable()
                            ->reportePreferencia(
                                $checkbox_empresa,
                                $empresa,
                                $checkbox_campania,
                                $campania,
                                $checkbox_segmento,
                                $segmento
                            );

                        $i = $posSegmento;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . $i, 'Premios aplicados por rubros')
                            ->mergeCells('K' . $i . ':S' . $i)
                            ->setCellValue('T' . $i, 'Premios redimidos por rubros')
                            ->mergeCells('T' . $i . ':AB' . $i);

                        $objPHPExcel->getActiveSheet()->getStyle('K' . $i . ':AB' . $i)->applyFromArray($styleArray);

                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . $i, 'Belleza')
                            ->setCellValue('L' . $i, 'Productos')
                            ->setCellValue('M' . $i, 'Restaurantes')
                            ->setCellValue('N' . $i, 'Viajes')
                            ->setCellValue('O' . $i, 'Salud')
                            ->setCellValue('P' . $i, 'Entretenimiento')
                            ->setCellValue('Q' . $i, 'Tiendas')
                            ->setCellValue('R' . $i, 'Otros')
                            ->setCellValue('S' . $i, 'Total')
                            ->setCellValue('T' . $i, 'Belleza')
                            ->setCellValue('U' . $i, 'Productos')
                            ->setCellValue('V' . $i, 'Restaurantes')
                            ->setCellValue('W' . $i, 'Viajes')
                            ->setCellValue('X' . $i, 'Salud')
                            ->setCellValue('Y' . $i, 'Entretenimiento')
                            ->setCellValue('Z' . $i, 'Tiendas')
                            ->setCellValue('AA' . $i, 'Otros')
                            ->setCellValue('AB' . $i, 'Total');

                        $objPHPExcel->getActiveSheet()->getStyle('K' . $i . ':AB' . $i)->applyFromArray($styleArray);

                        $i++;
                        $total = $i;

                        $preferencias = array();
                        $campaniaTemp = "";
                        $segmentoTemp = "";
                        $count = 0;
                        foreach ($resultadoSegmento as $registro) {
                            if ($campaniaTemp == "" && $segmentoTemp == "") {
                                $campaniaTemp = $registro->Campania;
                                $segmentoTemp = $registro->Segmento;
                                $preferencias[$count]['Campania'] = $registro->Campania;
                            } else {
                                if ($campaniaTemp != $registro->Campania || $segmentoTemp != $registro->Segmento) {
                                    $campaniaTemp = $registro->Campania;
                                    $segmentoTemp = $registro->Segmento;
                                    $count++;
                                    $preferencias[$count]['Campania'] = $registro->Campania;
                                }
                            }

                            $preferencias[$count]['Segmento'] = $registro->Segmento;
                            if ($registro->Rubro == "Belleza") {
                                @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                                @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                                @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                            }

                            @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                        }

                        for ($j = 0; $j <= $count; $j++) {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('K' . $i, (int)@$preferencias[$j]['ABelleza'])
                                ->setCellValue('L' . $i, (int)@$preferencias[$j]['AProductos'])
                                ->setCellValue('M' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                                ->setCellValue('N' . $i, (int)@$preferencias[$j]['AViajes'])
                                ->setCellValue('O' . $i, (int)@$preferencias[$j]['ASalud'])
                                ->setCellValue('P' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                                ->setCellValue('Q' . $i, (int)@$preferencias[$j]['ATiendas'])
                                ->setCellValue('R' . $i, (int)@$preferencias[$j]['AOtros'])
                                ->setCellValue('S' . $i, (int)@$preferencias[$j]['ATotal'])
                                ->setCellValue('T' . $i, (int)@$preferencias[$j]['RBelleza'])
                                ->setCellValue('U' . $i, (int)@$preferencias[$j]['RProductos'])
                                ->setCellValue('V' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                                ->setCellValue('W' . $i, (int)@$preferencias[$j]['RViajes'])
                                ->setCellValue('X' . $i, (int)@$preferencias[$j]['RSalud'])
                                ->setCellValue('Y' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                                ->setCellValue('Z' . $i, (int)@$preferencias[$j]['RTiendas'])
                                ->setCellValue('AA' . $i, (int)@$preferencias[$j]['ROtros'])
                                ->setCellValue('AB' . $i, (int)@$preferencias[$j]['RTotal']);
                            $i++;
                        }

                        $objPHPExcel->getActiveSheet()->getStyle('K' . $total . ':AB' . ($i - 1))->applyFromArray($styleArray2);
                    }
                    #endregion

                    $posUsuarios = $i;
                    #region Seccion Usuario Comportamiento
                    if ($checkbox_usuario) {
                        $resultadoUsuarios = $this->getAsignacionTable()
                            ->reporteComportamiento(
                                $checkbox_empresa,
                                $empresa,
                                $checkbox_campania,
                                $campania,
                                $checkbox_segmento,
                                $segmento,
                                $checkbox_usuario,
                                $usuario
                            );

                        $i = $i + 3;
                        $posUsuarios = $i;

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $i, 'Empresas')
                                ->mergeCells('A' . $i . ':A' . ($i + 1));
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B' . $i, 'Campañas')
                            ->setCellValue('C' . $i, 'Segmentos')
                            ->setCellValue('D' . $i, 'Usuarios')
                            ->setCellValue('E' . $i, 'Correos')
                            ->setCellValue('F' . $i, 'Premios')
                            ->mergeCells('B' . $i . ':B' . ($i + 1))
                            ->mergeCells('C' . $i . ':C' . ($i + 1))
                            ->mergeCells('D' . $i . ':D' . ($i + 1))
                            ->mergeCells('E' . $i . ':E' . ($i + 1))
                            ->mergeCells('F' . $i . ':I' . $i);

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
                        }
                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('F' . $i, 'Asignados')
                            ->setCellValue('G' . $i, 'Aplicados')
                            ->setCellValue('H' . $i, 'Redimidos')
                            ->setCellValue('I' . $i, 'Saldo');

                        if ($this->identity()->TipoUsuario != "cliente") {
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':I' . (count($resultadoUsuarios) + $i))->applyFromArray($styleArray2);
                        } else {
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . $i)->applyFromArray($styleArray);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':I' . (count($resultadoUsuarios) + $i))->applyFromArray($styleArray2);
                        }
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':D' . (count($resultadoUsuarios) + $i))->applyFromArray($cellArray);
                        $i++;

                        $campaniaTemp = "";
                        $segmentoTemp = "";
                        foreach ($resultadoUsuarios as $registro) {
                            if ($campaniaTemp == "") {
                                $campaniaTemp = $registro->Campania;
                            } else {
                                if ($campaniaTemp != $registro->Campania) {
                                    $campaniaTemp = $registro->Campania;
                                }
                            }

                            if ($segmentoTemp == "") {
                                $segmentoTemp = $registro->Segmento;
                            } else {
                                if ($segmentoTemp != $registro->Segmento) {
                                    $segmentoTemp = $registro->Segmento;
                                }
                            }

                            if ($this->identity()->TipoUsuario != "cliente") {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue('A' . $i, $registro->Empresa);
                            }
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('B' . $i, $campaniaTemp)
                                ->setCellValue('C' . $i, $segmentoTemp)
                                ->setCellValue('D' . $i, $registro->NumeroDocumento)
                                ->setCellValue('E' . $i, $registro->Correos)
                                ->setCellValue('F' . $i, (int)$registro->TotalAsignados)
                                ->setCellValue('G' . $i, (int)$registro->TotalAplicados)
                                ->setCellValue('H' . $i, (int)$registro->Redimidos)
                                ->setCellValue('I' . $i, $registro->TotalAsignados - $registro->TotalAplicados);
                            $i++;
                        }
                    }
                    #endregion

                    #region Seccion Usuario Preferencia
                    if ($checkbox_usuario) {
                        $resultadoUsuarios = $this->getAsignacionTable()
                            ->reportePreferencia(
                                $checkbox_empresa,
                                $empresa,
                                $checkbox_campania,
                                $campania,
                                $checkbox_segmento,
                                $segmento,
                                $checkbox_usuario,
                                $usuario
                            );

                        $i = $posUsuarios;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('J' . $i, 'Premios aplicados por rubros')
                            ->mergeCells('J' . $i . ':R' . $i)
                            ->setCellValue('S' . $i, 'Premios redimidos por rubros')
                            ->mergeCells('S' . $i . ':AA' . $i);

                        $objPHPExcel->getActiveSheet()->getStyle('J' . $i . ':AA' . $i)->applyFromArray($styleArray);

                        $i++;

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('J' . $i, 'Belleza')
                            ->setCellValue('K' . $i, 'Productos')
                            ->setCellValue('L' . $i, 'Restaurantes')
                            ->setCellValue('M' . $i, 'Viajes')
                            ->setCellValue('N' . $i, 'Salud')
                            ->setCellValue('O' . $i, 'Entretenimiento')
                            ->setCellValue('P' . $i, 'Tiendas')
                            ->setCellValue('Q' . $i, 'Otros')
                            ->setCellValue('R' . $i, 'Total')
                            ->setCellValue('S' . $i, 'Belleza')
                            ->setCellValue('T' . $i, 'Productos')
                            ->setCellValue('U' . $i, 'Restaurantes')
                            ->setCellValue('V' . $i, 'Viajes')
                            ->setCellValue('W' . $i, 'Salud')
                            ->setCellValue('X' . $i, 'Entretenimiento')
                            ->setCellValue('Y' . $i, 'Tiendas')
                            ->setCellValue('Z' . $i, 'Otros')
                            ->setCellValue('AA' . $i, 'Total');

                        $objPHPExcel->getActiveSheet()->getStyle('J' . $i . ':AA' . $i)->applyFromArray($styleArray);

                        $i++;
                        $total = $i;
                        $preferencias = array();
                        $dataCampania = "";
                        $dataSegmento = "";
                        $dataUsuario = "";
                        $count = 0;
                        foreach ($resultadoUsuarios as $registro) {
                            if ($dataUsuario == "" && $dataCampania == "" && $dataSegmento == "") {
                                $dataCampania = $registro->Campania;
                                $dataSegmento = $registro->Segmento;
                                $dataUsuario = $registro->NumeroDocumento;
                                $preferencias[$count]['Campania'] = $registro->Campania;
                            } else {
                                if ($dataCampania != $registro->Campania ||
                                    $dataSegmento != $registro->Segmento ||
                                    $dataUsuario != $registro->NumeroDocumento
                                ) {
                                    $dataCampania = $registro->Campania;
                                    $dataSegmento = $registro->Segmento;
                                    $dataUsuario = $registro->NumeroDocumento;
                                    $count++;
                                    $preferencias[$count]['Campania'] = $registro->Campania;
                                }
                            }

                            $preferencias[$count]['Segmento'] = $registro->Segmento;
                            $preferencias[$count]['NumeroDocumento'] = $registro->NumeroDocumento;
                            if ($registro->Rubro == "Belleza") {
                                @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                                @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                                @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                                @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                            }

                            @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                            @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                        }

                        for ($j = 0; $j <= $count; $j++) {
                            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('J' . $i, (int)@$preferencias[$j]['ABelleza'])
                                ->setCellValue('K' . $i, (int)@$preferencias[$j]['AProductos'])
                                ->setCellValue('L' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                                ->setCellValue('M' . $i, (int)@$preferencias[$j]['AViajes'])
                                ->setCellValue('N' . $i, (int)@$preferencias[$j]['ASalud'])
                                ->setCellValue('O' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                                ->setCellValue('P' . $i, (int)@$preferencias[$j]['ATiendas'])
                                ->setCellValue('Q' . $i, (int)@$preferencias[$j]['AOtros'])
                                ->setCellValue('R' . $i, (int)@$preferencias[$j]['ATotal'])
                                ->setCellValue('S' . $i, (int)@$preferencias[$j]['RBelleza'])
                                ->setCellValue('T' . $i, (int)@$preferencias[$j]['RProductos'])
                                ->setCellValue('U' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                                ->setCellValue('V' . $i, (int)@$preferencias[$j]['RViajes'])
                                ->setCellValue('W' . $i, (int)@$preferencias[$j]['RSalud'])
                                ->setCellValue('X' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                                ->setCellValue('Y' . $i, (int)@$preferencias[$j]['RTiendas'])
                                ->setCellValue('Z' . $i, (int)@$preferencias[$j]['ROtros'])
                                ->setCellValue('AA' . $i, (int)@$preferencias[$j]['RTotal']);
                            $i++;
                        }

                        $objPHPExcel->getActiveSheet()->getStyle('J' . $total . ':AA' . ($i - 1))->applyFromArray($styleArray2);
                    }
                    #endregion
                } else {
                    #region Opciones
                    $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                    $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                    $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);

                    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);

                    $nombre_archivo = $this::REPORTE_EC_COMP_PREF;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A1', $nombre_archivo)
                        ->mergeCells('A1:F1')
                        ->setCellValue('A2', 'Empresa Cliente: ')
                        ->setCellValue('A3', 'Campañas: ')
                        ->setCellValue('A4', 'Segmentos: ');

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                        ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                        ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                    $i = $inicio;
                    #endregion

                    #region Seccion Empresa Comportamiento
                    if (!$checkbox_usuario) {
                        $i = $inicio + 2;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A6', 'Empresa')
                            ->mergeCells('A6:A7')
                            ->setCellValue('B6', 'Usuario')
                            ->mergeCells('B6:D6')
                            ->setCellValue('E6', 'Premios')
                            ->mergeCells('E6:H6')
                            ->setCellValue('I6', 'Premios en Rubros aplicados')
                            ->mergeCells('I6:Q6')
                            ->setCellValue('R6', 'Rubros en rubros redimidos')
                            ->mergeCells('R6:Z6');

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('B7', 'Asignados')
                            ->setCellValue('C7', 'Aplicados')
                            ->setCellValue('D7', 'Redimidos')
                            ->setCellValue('E7', 'Asignados')
                            ->setCellValue('F7', 'Aplicados')
                            ->setCellValue('G7', 'Redimidos')
                            ->setCellValue('H7', 'Saldo')
                            ->setCellValue('I7', 'Belleza')
                            ->setCellValue('J7', 'Productos')
                            ->setCellValue('K7', 'Restaurantes')
                            ->setCellValue('L7', 'Viajes')
                            ->setCellValue('M7', 'Salud')
                            ->setCellValue('N7', 'Entretenimiento')
                            ->setCellValue('O7', 'Tiendas')
                            ->setCellValue('P7', 'Otros')
                            ->setCellValue('Q7', 'Total')
                            ->setCellValue('R7', 'Belleza')
                            ->setCellValue('S7', 'Productos')
                            ->setCellValue('T7', 'Restaurantes')
                            ->setCellValue('U7', 'Viajes')
                            ->setCellValue('V7', 'Salud')
                            ->setCellValue('W7', 'Entretenimiento')
                            ->setCellValue('X7', 'Tiendas')
                            ->setCellValue('Y7', 'Otros')
                            ->setCellValue('Z7', 'Total');

                        $objPHPExcel->getActiveSheet()->getStyle('A6:Z7')->applyFromArray($styleArray);

                        $sumUsuAsignados = 0;
                        $sumUsuAplicados = 0;
                        $sumUsuRedimidos = 0;
                        $sumTotalAsignados = 0;
                        $sumTotalAplicados = 0;
                        $sumRedimidos = 0;
                        $sumSaldo = 0;
                        $dataEmpresas = "";

                        foreach ($resultadoComp as $registro) {
                            $dataEmpresas = $dataEmpresas . $registro->Empresa . "; ";
                            $sumUsuAsignados = $sumUsuAsignados + $registro->UsuAsignados;
                            $sumUsuAplicados = $sumUsuAplicados + $registro->UsuAplicados;
                            $sumUsuRedimidos = $sumUsuRedimidos + $registro->UsuRedimidos;
                            $sumTotalAsignados = $sumTotalAsignados + $registro->TotalAsignados;
                            $sumTotalAplicados = $sumTotalAplicados + $registro->TotalAplicados;
                            $sumRedimidos = $sumRedimidos + $registro->Redimidos;
                            $sumSaldo = $sumSaldo + $registro->TotalAsignados - $registro->TotalAplicados;
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $dataEmpresas)
                            ->setCellValue('B' . $i, $sumUsuAsignados)
                            ->setCellValue('C' . $i, $sumUsuAplicados)
                            ->setCellValue('D' . $i, $sumUsuRedimidos)
                            ->setCellValue('E' . $i, $sumTotalAsignados)
                            ->setCellValue('F' . $i, $sumTotalAplicados)
                            ->setCellValue('G' . $i, $sumRedimidos)
                            ->setCellValue('H' . $i, $sumSaldo);

                        $preferencias = array(
                            "ABelleza" => 0,
                            "AProductos" => 0,
                            "ARestaurantes" => 0,
                            "AViajes" => 0,
                            "ASalud" => 0,
                            "AEntretenimiento" => 0,
                            "ATiendas" => 0,
                            "AOtros" => 0,
                            "ATotal" => 0,
                            "RBelleza" => 0,
                            "RProductos" => 0,
                            "RRestaurantes" => 0,
                            "RViajes" => 0,
                            "RSalud" => 0,
                            "REntretenimiento" => 0,
                            "RTiendas" => 0,
                            "ROtros" => 0,
                            "RTotal" => 0
                        );

                        foreach ($resultadoPref as $registro) {
                            if ($registro->Rubro == "Belleza") {
                                $preferencias['ABelleza'] = $preferencias['ABelleza'] + $registro->TotalAplicados;
                                $preferencias['RBelleza'] = $preferencias['RBelleza'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Productos") {
                                $preferencias['AProductos'] = $preferencias['AProductos'] + $registro->TotalAplicados;
                                $preferencias['RProductos'] = $preferencias['RProductos'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Restaurantes") {
                                $preferencias['ARestaurantes'] = $preferencias['ARestaurantes'] + $registro->TotalAplicados;
                                $preferencias['RRestaurantes'] = $preferencias['RRestaurantes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Viajes") {
                                $preferencias['AViajes'] = $preferencias['AViajes'] + $registro->TotalAplicados;
                                $preferencias['RViajes'] = $preferencias['RViajes'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Salud") {
                                $preferencias['ASalud'] = $preferencias['ASalud'] + $registro->TotalAplicados;
                                $preferencias['RSalud'] = $preferencias['RSalud'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Entretenimiento") {
                                $preferencias['AEntretenimiento'] = $preferencias['AEntretenimiento'] + $registro->TotalAplicados;
                                $preferencias['REntretenimiento'] = $preferencias['REntretenimiento'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Tiendas") {
                                $preferencias['ATiendas'] = $preferencias['ATiendas'] + $registro->TotalAplicados;
                                $preferencias['RTiendas'] = $preferencias['RTiendas'] + $registro->Redimidos;
                            } elseif ($registro->Rubro == "Otros") {
                                $preferencias['AOtros'] = $preferencias['AOtros'] + $registro->TotalAplicados;
                                $preferencias['ROtros'] = $preferencias['ROtros'] + $registro->Redimidos;
                            }

                            $preferencias['ATotal'] = $preferencias['ATotal'] + $registro->TotalAplicados;
                            $preferencias['RTotal'] = $preferencias['RTotal'] + $registro->Redimidos;
                        }

                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('I' . $i, (int)$preferencias['ABelleza'])
                            ->setCellValue('J' . $i, (int)$preferencias['AProductos'])
                            ->setCellValue('K' . $i, (int)$preferencias['ARestaurantes'])
                            ->setCellValue('L' . $i, (int)$preferencias['AViajes'])
                            ->setCellValue('M' . $i, (int)$preferencias['ASalud'])
                            ->setCellValue('N' . $i, (int)$preferencias['AEntretenimiento'])
                            ->setCellValue('O' . $i, (int)$preferencias['ATiendas'])
                            ->setCellValue('P' . $i, (int)$preferencias['AOtros'])
                            ->setCellValue('Q' . $i, (int)$preferencias['ATotal'])
                            ->setCellValue('R' . $i, (int)$preferencias['RBelleza'])
                            ->setCellValue('S' . $i, (int)$preferencias['RProductos'])
                            ->setCellValue('T' . $i, (int)$preferencias['RRestaurantes'])
                            ->setCellValue('U' . $i, (int)$preferencias['RViajes'])
                            ->setCellValue('V' . $i, (int)$preferencias['RSalud'])
                            ->setCellValue('W' . $i, (int)$preferencias['REntretenimiento'])
                            ->setCellValue('X' . $i, (int)$preferencias['RTiendas'])
                            ->setCellValue('Y' . $i, (int)$preferencias['ROtros'])
                            ->setCellValue('Z' . $i, (int)$preferencias['RTotal']);

                        $objPHPExcel->getActiveSheet()->getStyle('A8:Z' . ($i))->applyFromArray($styleArray2);
                    }
                    #endregion
                }
            } elseif ($checkbox_demo && $checkbox_comp && !$checkbox_pref) {
                #region Opciones
                $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);

                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->getActiveSheet()->getStyle('A6:S7')->applyFromArray($styleArray);
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('B6:S7')->applyFromArray($styleArray);
                }

                $nombre_archivo = $this::REPORTE_USU_DEMO_COMP;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $nombre_archivo)
                    ->mergeCells('A1:F1')
                    ->setCellValue('A2', 'Empresa Cliente: ')
                    ->setCellValue('A3', 'Campañas: ')
                    ->setCellValue('A4', 'Segmentos: ');

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                    ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                    ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                $resultado = $this->getAsignacionTable()
                    ->reporteDemografico($checkbox_empresa, $empresa, $checkbox_campania, $campania, $checkbox_segmento, $segmento, $checkbox_usuario, $usuario);
                $inicio = 7;

                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A6', 'Empresa')
                        ->mergeCells("A6:A7");
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B6', 'Campaña')
                    ->mergeCells("B6:B7")
                    ->setCellValue('C6', 'Segmento')
                    ->mergeCells("C6:C7")
                    ->setCellValue('D6', 'Nro. Documento')
                    ->mergeCells("D6:D7")
                    ->setCellValue('E6', 'Correos')
                    ->mergeCells("E6:E7")
                    ->setCellValue('F6', 'Datos Demográficos')
                    ->mergeCells("F6:O6")
                    ->setCellValue('F7', 'Nombres')
                    ->setCellValue('G7', 'Apellidos')
                    ->setCellValue('H7', 'Celular')
                    ->setCellValue('I7', 'Año Nacimiento')
                    ->setCellValue('J7', 'Estado Civil')
                    ->setCellValue('K7', 'Nivel Educativo')
                    ->setCellValue('L7', 'Genero')
                    ->setCellValue('M7', 'Hijos')
                    ->setCellValue('N7', 'Distritos')
                    ->setCellValue('O7', 'Lugar de Trabajo')
                    ->setCellValue('P6', 'Premios')
                    ->mergeCells("P6:S6")
                    ->setCellValue('P7', 'Asignados')
                    ->setCellValue('Q7', 'Aplicados')
                    ->setCellValue('R7', 'Redimidos')
                    ->setCellValue('S7', 'Saldos');

                $i = $inicio + 1;
                #endregion

                #region Seccion Usuario
                foreach ($resultado as $registro) {
                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $registro->Empresa);
                    }

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $registro->Campania)
                        ->setCellValue('C' . $i, $registro->Segmento)
                        ->setCellValue('D' . $i, $registro->NumeroDocumento)
                        ->setCellValue('E' . $i, $registro->Correos)
                        ->setCellValue('F' . $i, $registro->Pregunta01)
                        ->setCellValue('G' . $i, $registro->Pregunta02)
                        ->setCellValue('H' . $i, $registro->Pregunta09)
                        ->setCellValue('I' . $i, $registro->Pregunta03)
                        ->setCellValue('J' . $i, $registro->Pregunta05)
                        ->setCellValue('K' . $i, $registro->Pregunta10)
                        ->setCellValue('L' . $i, $registro->Pregunta04)
                        ->setCellValue('M' . $i, $registro->Pregunta08)
                        ->setCellValue('N' . $i, $registro->Pregunta06)
                        ->setCellValue('O' . $i, $registro->Pregunta07);
                    $i++;
                }

                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->getActiveSheet()->getStyle('A8:O' . ($i - 1))->applyFromArray($styleArray2);
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('B8:O' . ($i - 1))->applyFromArray($styleArray2);
                }
                $objPHPExcel->getActiveSheet()->getStyle('D8:D' . (count($resultado) + $i))->applyFromArray($cellArray);

                $resultadoUsuarios = $this->getAsignacionTable()
                    ->reporteComportamiento(
                        $checkbox_empresa,
                        $empresa,
                        $checkbox_campania,
                        $campania,
                        $checkbox_segmento,
                        $segmento,
                        true,
                        $usuario
                    );

                $j = 8;
                foreach ($resultadoUsuarios as $registro) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('P' . $j, (int)$registro->TotalAsignados)
                        ->setCellValue('Q' . $j, (int)$registro->TotalAplicados)
                        ->setCellValue('R' . $j, (int)$registro->Redimidos)
                        ->setCellValue('S' . $j, $registro->TotalAsignados - $registro->TotalAplicados);
                    $j++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('P8:S' . ($j - 1))->applyFromArray($styleArray2);
                #endregion
            } elseif ($checkbox_demo && !$checkbox_comp && $checkbox_pref) {
                #region Opciones
                $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);

                $nombre_archivo = $this::REPORTE_USU_DEMO_PREF;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $nombre_archivo)
                    ->mergeCells('A1:F1')
                    ->setCellValue('A2', 'Empresa Cliente: ')
                    ->setCellValue('A3', 'Campañas: ')
                    ->setCellValue('A4', 'Segmentos: ');

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                    ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                    ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                $resultado = $this->getAsignacionTable()
                    ->reporteDemografico($checkbox_empresa, $empresa, $checkbox_campania, $campania, $checkbox_segmento, $segmento, $checkbox_usuario, $usuario);
                $inicio = 7;

                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A6', 'Empresas')
                        ->mergeCells("A6:A7");
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B6', 'Campaña')
                    ->mergeCells("B6:B7")
                    ->setCellValue('C6', 'Segmento')
                    ->mergeCells("C6:C7")
                    ->setCellValue('D6', 'Nro. Documento')
                    ->mergeCells("D6:D7")
                    ->setCellValue('E6', 'Correos')
                    ->mergeCells("E6:E7")
                    ->setCellValue('F6', 'Datos Demográficos')
                    ->mergeCells("F6:O6")
                    ->setCellValue('F7', 'Nombres')
                    ->setCellValue('G7', 'Apellidos')
                    ->setCellValue('H7', 'Celular')
                    ->setCellValue('I7', 'Año Nacimiento')
                    ->setCellValue('J7', 'Estado Civil')
                    ->setCellValue('K7', 'Nivel Educativo')
                    ->setCellValue('L7', 'Genero')
                    ->setCellValue('M7', 'Hijos')
                    ->setCellValue('N7', 'Distritos')
                    ->setCellValue('O7', 'Lugar de Trabajo')
                    ->setCellValue('P6', 'Premios aplicados por rubros')
                    ->mergeCells('P6:W6')
                    ->setCellValue('Y6', 'Premios redimidos por rubros')
                    ->mergeCells('Y6:AG6')
                    ->setCellValue('P7', 'Belleza')
                    ->setCellValue('Q7', 'Productos')
                    ->setCellValue('R7', 'Restaurantes')
                    ->setCellValue('S7', 'Viajes')
                    ->setCellValue('T7', 'Salud')
                    ->setCellValue('U7', 'Entretenimiento')
                    ->setCellValue('V7', 'Tiendas')
                    ->setCellValue('W7', 'Otros')
                    ->setCellValue('X7', 'Total')
                    ->setCellValue('Y7', 'Belleza')
                    ->setCellValue('Z7', 'Productos')
                    ->setCellValue('AA7', 'Restaurantes')
                    ->setCellValue('AB7', 'Viajes')
                    ->setCellValue('AC7', 'Salud')
                    ->setCellValue('AD7', 'Entretenimiento')
                    ->setCellValue('AE7', 'Tiendas')
                    ->setCellValue('AF7', 'Otros')
                    ->setCellValue('AG7', 'Total');

                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->getActiveSheet()->getStyle('A6:AG7')->applyFromArray($styleArray);
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('B6:AG7')->applyFromArray($styleArray);
                }
                $i = $inicio + 1;
                #endregion

                #region Seccion Usuario
                foreach ($resultado as $registro) {
                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $registro->Empresa);
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $registro->Campania)
                        ->setCellValue('C' . $i, $registro->Segmento)
                        ->setCellValue('D' . $i, $registro->NumeroDocumento)
                        ->setCellValue('E' . $i, $registro->Correos)
                        ->setCellValue('F' . $i, $registro->Pregunta01)
                        ->setCellValue('G' . $i, $registro->Pregunta02)
                        ->setCellValue('H' . $i, $registro->Pregunta09)
                        ->setCellValue('I' . $i, $registro->Pregunta03)
                        ->setCellValue('J' . $i, $registro->Pregunta05)
                        ->setCellValue('K' . $i, $registro->Pregunta10)
                        ->setCellValue('L' . $i, $registro->Pregunta04)
                        ->setCellValue('M' . $i, $registro->Pregunta08)
                        ->setCellValue('N' . $i, $registro->Pregunta06)
                        ->setCellValue('O' . $i, $registro->Pregunta07);
                    $i++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('D8:D' . (count($resultado) + $i))->applyFromArray($cellArray);
                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->getActiveSheet()->getStyle('A8:O' . ($i - 1))->applyFromArray($styleArray2);
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('B8:O' . ($i - 1))->applyFromArray($styleArray2);
                }

                $resultadoUsuarios = $this->getAsignacionTable()
                    ->reportePreferencia(
                        $checkbox_empresa,
                        $empresa,
                        $checkbox_campania,
                        $campania,
                        $checkbox_segmento,
                        $segmento,
                        true,
                        $usuario
                    );

                $i = 8;

                $total = $i;
                $preferencias = array();
                $dataCampania = "";
                $dataSegmento = "";
                $dataUsuario = "";
                $count = 0;
                foreach ($resultadoUsuarios as $registro) {
                    if ($dataUsuario == "" && $dataCampania == "" && $dataSegmento == "") {
                        $dataCampania = $registro->Campania;
                        $dataSegmento = $registro->Segmento;
                        $dataUsuario = $registro->NumeroDocumento;
                        $preferencias[$count]['Campania'] = $registro->Campania;
                    } else {
                        if ($dataCampania != $registro->Campania ||
                            $dataSegmento != $registro->Segmento ||
                            $dataUsuario != $registro->NumeroDocumento
                        ) {
                            $dataCampania = $registro->Campania;
                            $dataSegmento = $registro->Segmento;
                            $dataUsuario = $registro->NumeroDocumento;
                            $count++;
                            $preferencias[$count]['Campania'] = $registro->Campania;
                        }
                    }

                    $preferencias[$count]['Segmento'] = $registro->Segmento;
                    $preferencias[$count]['NumeroDocumento'] = $registro->NumeroDocumento;
                    if ($registro->Rubro == "Belleza") {
                        @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Productos") {
                        @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Restaurantes") {
                        @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Viajes") {
                        @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Salud") {
                        @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Entretenimiento") {
                        @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                        @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Tiendas") {
                        @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Otros") {
                        @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                        @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                    }

                    @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                    @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                }

                for ($j = 0; $j <= $count; $j++) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('P' . $i, (int)@$preferencias[$j]['ABelleza'])
                        ->setCellValue('Q' . $i, (int)@$preferencias[$j]['AProductos'])
                        ->setCellValue('R' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                        ->setCellValue('S' . $i, (int)@$preferencias[$j]['AViajes'])
                        ->setCellValue('T' . $i, (int)@$preferencias[$j]['ASalud'])
                        ->setCellValue('U' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                        ->setCellValue('V' . $i, (int)@$preferencias[$j]['ATiendas'])
                        ->setCellValue('W' . $i, (int)@$preferencias[$j]['AOtros'])
                        ->setCellValue('X' . $i, (int)@$preferencias[$j]['ATotal'])
                        ->setCellValue('Y' . $i, (int)@$preferencias[$j]['RBelleza'])
                        ->setCellValue('Z' . $i, (int)@$preferencias[$j]['RProductos'])
                        ->setCellValue('AA' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                        ->setCellValue('AB' . $i, (int)@$preferencias[$j]['RViajes'])
                        ->setCellValue('AC' . $i, (int)@$preferencias[$j]['RSalud'])
                        ->setCellValue('AD' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                        ->setCellValue('AE' . $i, (int)@$preferencias[$j]['RTiendas'])
                        ->setCellValue('AF' . $i, (int)@$preferencias[$j]['ROtros'])
                        ->setCellValue('AG' . $i, (int)@$preferencias[$j]['RTotal']);
                    $i++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('P' . $total . ':AG' . ($i - 1))->applyFromArray($styleArray2);
                #endregion
            } elseif ($checkbox_demo && $checkbox_comp && $checkbox_pref) {
                #region Opciones
                $datosEmpresa = $this->getEmpresaTable()->getEmpresa($empresa);
                $datosCampania = $this->getCampaniaPremiosTable()->getCampaniasP($campania);
                $datosSegmento = $this->getSegmentoPremiosTable()->getSegmentosPremios($segmento);

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($tittleStyleArray);
                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->getActiveSheet()->getStyle('A6:AK7')->applyFromArray($styleArray);
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('B6:AK7')->applyFromArray($styleArray);
                }

                $nombre_archivo = $this::REPORTE_USU_DEMO_COMP_PREF;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', $nombre_archivo)
                    ->mergeCells('A1:F1')
                    ->setCellValue('A2', 'Empresa Cliente: ')
                    ->setCellValue('A3', 'Campañas: ')
                    ->setCellValue('A4', 'Segmentos: ');

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B2', (!empty($datosEmpresa->NombreComercial) ? $datosEmpresa->NombreComercial : 'Todas'))
                    ->setCellValue('B3', (!empty($datosCampania->NombreCampania) ? $datosCampania->NombreCampania : 'Todas'))
                    ->setCellValue('B4', (!empty($datosSegmento->NombreSegmento) ? $datosSegmento->NombreSegmento : 'Todos'));

                $resultado = $this->getAsignacionTable()
                    ->reporteDemografico($checkbox_empresa, $empresa, $checkbox_campania, $campania, $checkbox_segmento, $segmento, $checkbox_usuario, $usuario);
                $inicio = 7;

                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A6', 'Empresa')
                        ->mergeCells("A6:A7");
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B6', 'Campaña')
                    ->mergeCells("B6:B7")
                    ->setCellValue('C6', 'Segmento')
                    ->mergeCells("C6:C7")
                    ->setCellValue('D6', 'Nro. Documento')
                    ->mergeCells("D6:D7")
                    ->setCellValue('E6', 'Correos')
                    ->mergeCells("E6:E7")
                    ->setCellValue('F6', 'Datos Demográficos')
                    ->mergeCells("F6:O6")
                    ->setCellValue('F7', 'Nombres')
                    ->setCellValue('G7', 'Apellidos')
                    ->setCellValue('H7', 'Celular')
                    ->setCellValue('I7', 'Año Nacimiento')
                    ->setCellValue('J7', 'Estado Civil')
                    ->setCellValue('K7', 'Nivel Educativo')
                    ->setCellValue('L7', 'Genero')
                    ->setCellValue('M7', 'Hijos')
                    ->setCellValue('N7', 'Distritos')
                    ->setCellValue('O7', 'Lugar de Trabajo');

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('P6', 'Premios')
                    ->mergeCells("P6:S6")
                    ->setCellValue('P7', 'Asignados')
                    ->setCellValue('Q7', 'Aplicados')
                    ->setCellValue('R7', 'Redimidos')
                    ->setCellValue('S7', 'Saldos');

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('T6', 'Premios aplicados por rubros')
                    ->mergeCells('T6:AB6')
                    ->setCellValue('AC6', 'Premios redimidos por rubros')
                    ->mergeCells('AC6:AK6')
                    ->setCellValue('T7', 'Belleza')
                    ->setCellValue('U7', 'Productos')
                    ->setCellValue('V7', 'Restaurantes')
                    ->setCellValue('W7', 'Viajes')
                    ->setCellValue('X7', 'Salud')
                    ->setCellValue('Y7', 'Entretenimiento')
                    ->setCellValue('Z7', 'Tiendas')
                    ->setCellValue('AA7', 'Otros')
                    ->setCellValue('AB7', 'Total')
                    ->setCellValue('AC7', 'Belleza')
                    ->setCellValue('AD7', 'Productos')
                    ->setCellValue('AE7', 'Restaurantes')
                    ->setCellValue('AF7', 'Viajes')
                    ->setCellValue('AG7', 'Salud')
                    ->setCellValue('AH7', 'Entretenimiento')
                    ->setCellValue('AI7', 'Tiendas')
                    ->setCellValue('AJ7', 'Otros')
                    ->setCellValue('AK7', 'Total');

                $i = $inicio + 1;
                #endregion

                #region Seccion Usuario
                foreach ($resultado as $registro) {
                    if ($this->identity()->TipoUsuario != "cliente") {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . $i, $registro->Empresa);
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $registro->Campania)
                        ->setCellValue('C' . $i, $registro->Segmento)
                        ->setCellValue('D' . $i, $registro->NumeroDocumento)
                        ->setCellValue('E' . $i, $registro->Correos)
                        ->setCellValue('F' . $i, $registro->Pregunta01)
                        ->setCellValue('G' . $i, $registro->Pregunta02)
                        ->setCellValue('H' . $i, $registro->Pregunta09)
                        ->setCellValue('I' . $i, $registro->Pregunta03)
                        ->setCellValue('J' . $i, $registro->Pregunta05)
                        ->setCellValue('K' . $i, $registro->Pregunta10)
                        ->setCellValue('L' . $i, $registro->Pregunta04)
                        ->setCellValue('M' . $i, $registro->Pregunta08)
                        ->setCellValue('N' . $i, $registro->Pregunta06)
                        ->setCellValue('O' . $i, $registro->Pregunta07);
                    $i++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('D8:D' . (count($resultado) + $i))->applyFromArray($cellArray);
                if ($this->identity()->TipoUsuario != "cliente") {
                    $objPHPExcel->getActiveSheet()->getStyle('A8:O' . ($i - 1))->applyFromArray($styleArray2);
                } else {
                    $objPHPExcel->getActiveSheet()->getStyle('B8:O' . ($i - 1))->applyFromArray($styleArray2);
                }

                $resultadoUsuarios = $this->getAsignacionTable()
                    ->reporteComportamiento(
                        $checkbox_empresa,
                        $empresa,
                        $checkbox_campania,
                        $campania,
                        $checkbox_segmento,
                        $segmento,
                        true,
                        $usuario
                    );

                $j = 8;
                foreach ($resultadoUsuarios as $registro) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('P' . $j, (int)$registro->TotalAsignados)
                        ->setCellValue('Q' . $j, (int)$registro->TotalAplicados)
                        ->setCellValue('R' . $j, (int)$registro->Redimidos)
                        ->setCellValue('S' . $j, $registro->TotalAsignados - $registro->TotalAplicados);
                    $j++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('P8:S' . ($j - 1))->applyFromArray($styleArray2);

                $resultadoUsuarios = $this->getAsignacionTable()
                    ->reportePreferencia(
                        $checkbox_empresa,
                        $empresa,
                        $checkbox_campania,
                        $campania,
                        $checkbox_segmento,
                        $segmento,
                        true,
                        $usuario
                    );

                $i = 8;

                $total = $i;
                $preferencias = array();
                $dataCampania = "";
                $dataSegmento = "";
                $dataUsuario = "";
                $count = 0;
                foreach ($resultadoUsuarios as $registro) {
                    if ($dataUsuario == "" && $dataCampania == "" && $dataSegmento == "") {
                        $dataCampania = $registro->Campania;
                        $dataSegmento = $registro->Segmento;
                        $dataUsuario = $registro->NumeroDocumento;
                        $preferencias[$count]['Campania'] = $registro->Campania;
                    } else {
                        if ($dataCampania != $registro->Campania ||
                            $dataSegmento != $registro->Segmento ||
                            $dataUsuario != $registro->NumeroDocumento
                        ) {
                            $dataCampania = $registro->Campania;
                            $dataSegmento = $registro->Segmento;
                            $dataUsuario = $registro->NumeroDocumento;
                            $count++;
                            $preferencias[$count]['Campania'] = $registro->Campania;
                        }
                    }

                    $preferencias[$count]['Segmento'] = $registro->Segmento;
                    $preferencias[$count]['NumeroDocumento'] = $registro->NumeroDocumento;
                    if ($registro->Rubro == "Belleza") {
                        @$preferencias[$count]['ABelleza'] = $preferencias[$count]['ABelleza'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RBelleza'] = $preferencias[$count]['RBelleza'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Productos") {
                        @$preferencias[$count]['AProductos'] = $preferencias[$count]['AProductos'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RProductos'] = $preferencias[$count]['RProductos'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Restaurantes") {
                        @$preferencias[$count]['ARestaurantes'] = $preferencias[$count]['ARestaurantes'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RRestaurantes'] = $preferencias[$count]['RRestaurantes'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Viajes") {
                        @$preferencias[$count]['AViajes'] = $preferencias[$count]['AViajes'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RViajes'] = $preferencias[$count]['RViajes'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Salud") {
                        @$preferencias[$count]['ASalud'] = $preferencias[$count]['ASalud'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RSalud'] = $preferencias[$count]['RSalud'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Entretenimiento") {
                        @$preferencias[$count]['AEntretenimiento'] = $preferencias[$count]['AEntretenimiento'] + $registro->TotalAplicados;
                        @$preferencias[$count]['REntretenimiento'] = $preferencias[$count]['REntretenimiento'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Tiendas") {
                        @$preferencias[$count]['ATiendas'] = $preferencias[$count]['ATiendas'] + $registro->TotalAplicados;
                        @$preferencias[$count]['RTiendas'] = $preferencias[$count]['RTiendas'] + $registro->Redimidos;
                    } elseif ($registro->Rubro == "Otros") {
                        @$preferencias[$count]['AOtros'] = $preferencias[$count]['AOtros'] + $registro->TotalAplicados;
                        @$preferencias[$count]['ROtros'] = $preferencias[$count]['ROtros'] + $registro->Redimidos;
                    }

                    @$preferencias[$count]['ATotal'] = $preferencias[$count]['ATotal'] + $registro->TotalAplicados;
                    @$preferencias[$count]['RTotal'] = $preferencias[$count]['RTotal'] + $registro->Redimidos;
                }

                for ($j = 0; $j <= $count; $j++) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('T' . $i, (int)@$preferencias[$j]['ABelleza'])
                        ->setCellValue('U' . $i, (int)@$preferencias[$j]['AProductos'])
                        ->setCellValue('V' . $i, (int)@$preferencias[$j]['ARestaurantes'])
                        ->setCellValue('W' . $i, (int)@$preferencias[$j]['AViajes'])
                        ->setCellValue('X' . $i, (int)@$preferencias[$j]['ASalud'])
                        ->setCellValue('Y' . $i, (int)@$preferencias[$j]['AEntretenimiento'])
                        ->setCellValue('Z' . $i, (int)@$preferencias[$j]['ATiendas'])
                        ->setCellValue('AA' . $i, (int)@$preferencias[$j]['AOtros'])
                        ->setCellValue('AB' . $i, (int)@$preferencias[$j]['ATotal'])
                        ->setCellValue('AC' . $i, (int)@$preferencias[$j]['RBelleza'])
                        ->setCellValue('AD' . $i, (int)@$preferencias[$j]['RProductos'])
                        ->setCellValue('AE' . $i, (int)@$preferencias[$j]['RRestaurantes'])
                        ->setCellValue('AF' . $i, (int)@$preferencias[$j]['RViajes'])
                        ->setCellValue('AG' . $i, (int)@$preferencias[$j]['RSalud'])
                        ->setCellValue('AH' . $i, (int)@$preferencias[$j]['REntretenimiento'])
                        ->setCellValue('AI' . $i, (int)@$preferencias[$j]['RTiendas'])
                        ->setCellValue('AJ' . $i, (int)@$preferencias[$j]['ROtros'])
                        ->setCellValue('AK' . $i, (int)@$preferencias[$j]['RTotal']);
                    $i++;
                }

                $objPHPExcel->getActiveSheet()->getStyle('T' . $total . ':AK' . ($i - 1))->applyFromArray($styleArray2);
                #endregion
            }

            $nombre_archivo = str_replace(" ", "_", $nombre_archivo);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $nombre_archivo . '.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        } else {
            exit;
        }
    }

    public function getDataEmpresaAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }
        $response = $this->getResponse();
        $request = $this->getRequest();
        $dataCampanias = array();
        $state = false;

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($result = $this->getEmpresaTable()->getEmpresa($id)) {
                        $campanias = $this->getCampaniaPremiosTable()->getCampaniasPByEmpresa($id);
                        foreach ($campanias as $value) {
                            $dataCampanias[] = array('id' => $value->id, 'text' => $value->NombreCampania);
                        }

                        $state = true;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'campanias' => $dataCampanias,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }

    public function getDataSegmentosAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $response = $this->getResponse();
        $request = $this->getRequest();
        $state = false;
        $dataSegmentos = array();

        $csrf = new Csrf();
        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = $post_data['id'];
            if (isset($post_data['csrf'])) {
                if ((filter_var($id, FILTER_VALIDATE_INT) !== false) and $csrf->verifyToken($post_data['csrf'])
                ) {
                    if ($result = $this->getSegmentoPremiosTable()->getAllSegmentos($id)) {
                        foreach ($result as $value) {
                            $dataSegmentos[] = array('id' => $value->id, 'text' => $value->NombreSegmento);
                        }
                        $state = true;
                    }
                }
            }
        }

        $csrf->cleanCsrf();
        $form = new BaseForm();

        return $response->setContent(Json::encode(
            array(
                'response' => $state,
                'segmentos' => $dataSegmentos,
                'csrf' => $form->get('csrf')->getValue()
            )
        ));
    }
}