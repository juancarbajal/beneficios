<?php

namespace App\Library;

use App\Model\Categoria;
use App\Model\DmMetCliente;
use App\Model\DmMetClientePreguntas;
use App\Model\Empresa;
use App\Model\EmpresaClienteCliente;
use App\Model\EstadoCivil;
use App\Model\Hijos;
use App\Model\ReporteExcel;
use App\Model\Rubro;

class Funciones
{
    public function exportacion($fechaInicio2, $fechaFin2, $empresa, $nombre_excel)
    {
        $mongo = new ClienteMongo();
        $getMetCliente = new DmMetCliente();
        $getMetClientePreguntas = new DmMetClientePreguntas();
        $getCategorias = new Categoria();
        $getEmpresa = new Empresa();

        $fechaInicio_defecto = '2015-01-01';
        $fechaFin_defecto = date('Y-m-d');

        $fechaInicio = ($fechaInicio2 == '') ? $fechaInicio_defecto : $fechaInicio2;
        $fechaFin = ($fechaFin2 == '') ? $fechaFin_defecto : $fechaFin2;
        $id_empresa = ($empresa == '') ? '' : $empresa;

        $nombre_empresa = ($id_empresa == '') ? 'Toda el site' :
            'Empresa: ' . $getEmpresa->getEmpresa($id_empresa)->NombreComercial;

        //REPORTE 3.1//////////////////////////////////////////////////////////////
        $descargasorredimidos_a = $getMetCliente->getDescargasOrRedimidos($id_empresa,
            $fechaInicio_defecto, $fechaFin_defecto);

        $descargasorredimidos_p = $getMetCliente->getDescargasOrRedimidos($id_empresa, $fechaInicio, $fechaFin);

        $descargas_acumulado = $descargasorredimidos_a->Descargas;
        $descargas_periodico = $descargasorredimidos_p->Descargas;

        $redimidos_acumulado = $descargasorredimidos_a->Redimidos;
        $redimidos_periodico = $descargasorredimidos_p->Redimidos;
        $categorias = $getCategorias::where('Eliminado', 0)->get();

        $idClientes = array();
        $idClientedata = $getMetCliente->getListClientesId($fechaInicio, $fechaFin);
        foreach ($idClientedata as $data) {
            $idClientes[] = $data->BNF_Cliente_id;
        }

        $edades = $getMetClientePreguntas->getEdades($id_empresa);
        $edades_periodo = $getMetClientePreguntas->getEdades($id_empresa, $idClientes);

        $hijos = $getMetClientePreguntas->getHijos($id_empresa);
        $hijos_periodo = $getMetClientePreguntas->getHijos($id_empresa, $idClientes);

        $estado_civil = $getMetClientePreguntas->getEstadoCivil($id_empresa);
        $estado_civil_periodo = $getMetClientePreguntas->getEstadoCivil($id_empresa, $idClientes);

        $nombres_acumulado = $getMetClientePreguntas->getPreguntaCampo($id_empresa, 'nombres');
        $nombres_periodo = $getMetClientePreguntas->getPreguntaCampo($id_empresa, 'nombres', $idClientes);

        $apellido_acumulado = $getMetClientePreguntas->getPreguntaCampo($id_empresa, 'apellidos');
        $apellido_periodo = $getMetClientePreguntas->getPreguntaCampo($id_empresa, 'apellidos', $idClientes);

        $genero_acumulado = $getMetClientePreguntas->getPreguntaGenero($id_empresa);
        $genero_periodo = $getMetClientePreguntas->getPreguntaGenero($id_empresa, $idClientes);

        $distrito_V_A = $getMetClientePreguntas->getPreguntaDistrito($id_empresa, 'distrito_vive');
        $distrito_V_P = $getMetClientePreguntas->getPreguntaDistrito($id_empresa, 'distrito_vive', $idClientes);

        $distrito_T_A = $getMetClientePreguntas->getPreguntaDistrito($id_empresa, 'distrito_trabaja');
        $distrito_T_P = $getMetClientePreguntas->getPreguntaDistrito($id_empresa, 'distrito_trabaja', $idClientes);

        $registros = count($descargas_acumulado) + count($categorias);

        $center = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );

        $styleArray2 = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );

        $objPHPExcel = new \PHPExcel();

        if ($registros > 0) {
            $session_cat = $mongo->get_session_category($fechaInicio_defecto, $fechaFin_defecto, $empresa);
            $session_cat_per = $mongo->get_session_category($fechaInicio, $fechaFin, $empresa);

            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setSubject("Reporte 3")
                ->setDescription("Reporte 3")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte 2");
            $objPHPExcel->getActiveSheet()->setTitle("Reporte 3");

            //desactiva cuadricula
            $objPHPExcel->getActiveSheet()->setShowGridlines(false);

            $letra = 65;
            while ($letra < 77) {
                $objPHPExcel->getActiveSheet()->getColumnDimension(chr($letra))->setAutoSize(true);
                $letra++;
            }

            $objPHPExcel->getActiveSheet()->getStyle('B9:D12')->applyFromArray($styleArray2);
            //CATEGORIAS
            $objPHPExcel->getActiveSheet()->getStyle('I8:L8')->applyFromArray($styleArray2);
            $cat_fila_fin = count($categorias) + 13;
            $objPHPExcel->getActiveSheet()->getStyle('H9:L' . $cat_fila_fin)->applyFromArray($styleArray2);//RECU
            //NOMBRES
            $objPHPExcel->getActiveSheet()->getStyle('C15:F16')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B17:F18')->applyFromArray($styleArray2);
            //APELLIDOS
            $objPHPExcel->getActiveSheet()->getStyle('C22:F23')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B24:F25')->applyFromArray($styleArray2);
            //EDADES
            $objPHPExcel->getActiveSheet()->getStyle('C27:F27')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B28:F33')->applyFromArray($styleArray2);
            //GENERO
            $objPHPExcel->getActiveSheet()->getStyle('C35:F35')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B36:F39')->applyFromArray($styleArray2);
            //hijos
            $objPHPExcel->getActiveSheet()->getStyle('C41:F42')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B43:F45')->applyFromArray($styleArray2);
            //estado civil
            $objPHPExcel->getActiveSheet()->getStyle('C47:F48')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B49:F53')->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->mergeCells('I8:J8')->getStyle('I8:J8')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('K8:L8')->getStyle('K8:L8')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C15:D15')->getStyle('C15:D15')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E15:F15')->getStyle('E15:F15')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C22:D22')->getStyle('C22:D22')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E22:F22')->getStyle('E22:F22')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C27:D27')->getStyle('C27:D27')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E27:F27')->getStyle('E27:F27')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C35:D35')->getStyle('C35:D35')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E35:F35')->getStyle('E35:F35')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C41:D41')->getStyle('C41:D41')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E41:F41')->getStyle('E41:F41')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C47:D47')->getStyle('C47:D47')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E47:F47')->getStyle('E47:F47')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('C56:D56')->getStyle('C56:D56')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E56:F56')->getStyle('E56:F56')->applyFromArray($center);

            $count_format = 17;
            $count_format_fin = 60 + count($distrito_T_A) + count($distrito_V_A);
            while ($count_format <= $count_format_fin) {
                $objPHPExcel->getActiveSheet()->getStyle('D' . $count_format)->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $count_format)->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $count_format++;
            }
            $count_format = 10;
            while ($count_format <= $cat_fila_fin) {
                $objPHPExcel->getActiveSheet()->getStyle('J' . $count_format)->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->getActiveSheet()->getStyle('L' . $count_format)->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $count_format++;
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'MODELO CRM')
                ->setCellValue('B3', 'NOMBRE')
                ->setCellValue('B4', 'Objetivo')
                ->setCellValue('B5', 'Fecha de inicio')
                ->setCellValue('B6', 'Fecha de fin')
                ->setCellValue('C3', 'Reporte general')
                ->setCellValue('C4', $nombre_empresa)
                ->setCellValue('C5', ($fechaInicio == $fechaInicio_defecto) ? '-----' : $fechaInicio)
                ->setCellValue('C6', ($fechaFin == $fechaFin_defecto) ? $fechaFin_defecto : $fechaFin)
                ->setCellValue('B10', 'Visitas')
                ->setCellValue('B11', 'Descargas')
                ->setCellValue('B12', 'Redenciones')
                ->setCellValue('C9', 'Periodo')
                ->setCellValue('C11', $descargas_periodico)
                ->setCellValue('C12', $redimidos_periodico)
                ->setCellValue('D9', 'Total')
                ->setCellValue('D11', $descargas_acumulado)
                ->setCellValue('D12', $redimidos_acumulado)
                //nombre
                ->setCellValue('B17', 'Nombre')
                ->setCellValue('B18', 'no definido')
                ->setCellValue('C15', 'Periodo')
                ->setCellValue('C16', 'Cantidad')
                ->setCellValue('C17', $nombres_periodo[0])
                ->setCellValue('C18', $nombres_periodo[1])
                ->setCellValue('D16', '%')
                ->setCellValue('D17', '=+C17/SUM(C$17:C$18)')
                ->setCellValue('D18', '=+C18/SUM(C$17:C$18)')
                ->setCellValue('E15', 'Total')
                ->setCellValue('E16', 'Cantidad')
                ->setCellValue('E17', $nombres_acumulado[0])
                ->setCellValue('E18', $nombres_acumulado[1])
                ->setCellValue('F16', '%')
                ->setCellValue('F17', '=+E17/SUM(E$17:E$18)')
                ->setCellValue('F18', '=+E18/SUM(E$17:E$18)')
                ->setCellValue('B24', 'Apellido')
                ->setCellValue('B25', 'no definido')
                ->setCellValue('C22', 'Periodo')
                ->setCellValue('C23', 'Cantidad')
                ->setCellValue('C24', $apellido_periodo[0])
                ->setCellValue('C25', $apellido_periodo[1])
                ->setCellValue('D23', '%')
                ->setCellValue('D24', '=+C24/SUM(C$24:C$25)')
                ->setCellValue('D25', '=+C25/SUM(C$24:C$25)')
                ->setCellValue('E22', 'Total')
                ->setCellValue('E23', 'Cantidad')
                ->setCellValue('E24', $apellido_acumulado[0])
                ->setCellValue('E25', $apellido_acumulado[1])
                ->setCellValue('F23', '%')
                ->setCellValue('F24', '=+E24/SUM(E$24:E$25)')
                ->setCellValue('F25', '=+E25/SUM(E$24:E$25)')
                //Edades
                ->setCellValue('B28', 'Edades')
                ->setCellValue('B29', 'de 0 a 20')
                ->setCellValue('B30', 'de 20 a 30')
                ->setCellValue('B31', 'de 30 a 40')
                ->setCellValue('B32', 'de 40 a +')
                ->setCellValue('B33', 'no definido')
                ->setCellValue('C27', 'Periodo')
                ->setCellValue('C28', 'Cantidad')
                ->setCellValue('D28', '%')
                ->setCellValue('E27', 'Total')
                ->setCellValue('E28', 'Cantidad')
                ->setCellValue('F28', '%');

            foreach ($edades_periodo as $dato) {
                $num_fila = ($dato->id == 1) ? 33 : 27 + $dato->id;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C' . $num_fila, $dato->Cantidad)
                    ->setCellValue('D' . $num_fila, '=+C' . $num_fila . '/SUM($C$29:$C$33)');
            }

            foreach ($edades as $dato) {
                $num_fila = ($dato->id == 1) ? 33 : 27 + $dato->id;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('E' . $num_fila, $dato->Cantidad)
                    ->setCellValue('F' . $num_fila, '=+E' . $num_fila . '/SUM($E$29:$E$33)');
            }

            //genero
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B36', 'Genero')
                ->setCellValue('B37', 'Masculino')
                ->setCellValue('B38', 'Femenino')
                ->setCellValue('B39', 'no definido')
                ->setCellValue('C35', 'Periodo')
                ->setCellValue('C36', 'Cantidad')
                ->setCellValue('C37', $genero_periodo[0])
                ->setCellValue('C38', $genero_periodo[1])
                ->setCellValue('C39', $genero_periodo[2])
                ->setCellValue('D36', '%')
                ->setCellValue('D37', '=+C37/SUM(C$37:C$39)')
                ->setCellValue('D38', '=+C38/SUM(C$37:C$39)')
                ->setCellValue('D39', '=+C39/SUM(C$37:C$39)')
                ->setCellValue('E35', 'Total')
                ->setCellValue('E36', 'Cantidad')
                ->setCellValue('E37', $genero_acumulado[0])
                ->setCellValue('E38', $genero_acumulado[1])
                ->setCellValue('E39', $genero_acumulado[2])
                ->setCellValue('F36', '%')
                ->setCellValue('F37', '=+E37/SUM(E$37:E$39)')
                ->setCellValue('F38', '=+E38/SUM(E$37:E$39)')
                ->setCellValue('F39', '=+E39/SUM(E$37:E$39)');


            //hijos
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B43', 'Con Hijos')
                ->setCellValue('B44', 'Sin Hijos')
                ->setCellValue('B45', 'no definido')
                ->setCellValue('C41', 'Periodo')
                ->setCellValue('C42', 'Cantidad')
                ->setCellValue('D42', '%')
                ->setCellValue('E41', 'Total')
                ->setCellValue('E42', 'Cantidad')
                ->setCellValue('F42', '%')
                ->setCellValue('C43', $hijos_periodo->SiHijos)
                ->setCellValue('D43', '=+C43/SUM($C$43:$C$45)')
                ->setCellValue('C44', $hijos_periodo->NoHijos)
                ->setCellValue('D44', '=+C44/SUM($C$43:$C$45)')
                ->setCellValue('C45', $hijos_periodo->NoDef)
                ->setCellValue('D45', '=+C45/SUM($C$43:$C$45)')
                ->setCellValue('E43', $hijos->SiHijos)
                ->setCellValue('F43', '=+E43/SUM($E$43:$E$45)')
                ->setCellValue('E44', $hijos->NoHijos)
                ->setCellValue('F44', '=+E44/SUM($E$43:$E$45)')
                ->setCellValue('E45', $hijos->NoDef)
                ->setCellValue('F45', '=+E45/SUM($E$43:$E$45)');


            //estdo civil
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B49', 'Casados')
                ->setCellValue('B50', 'Solteros')
                ->setCellValue('B51', 'Viudos')
                ->setCellValue('B52', 'Divorciado')
                ->setCellValue('B53', 'no definido')
                ->setCellValue('C47', 'Periodo')
                ->setCellValue('C48', 'Cantidad')
                ->setCellValue('D48', '%')
                ->setCellValue('E47', 'Total')
                ->setCellValue('E48', 'Cantidad')
                ->setCellValue('F48', '%');

            foreach ($estado_civil_periodo as $dato) {
                $num_fila = ($dato->id == 5) ? 52 : (($dato->id == 4) ? 53 : 48 + $dato->id);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C' . $num_fila, $dato->Cantidad)
                    ->setCellValue('D' . $num_fila, '=+C' . $num_fila . '/SUM(C$49:C$53)');
            }

            foreach ($estado_civil as $dato) {
                $num_fila = ($dato->id == 5) ? 52 : (($dato->id == 4) ? 53 : 48 + $dato->id);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('E' . $num_fila, $dato->Cantidad)
                    ->setCellValue('F' . $num_fila, '=+E' . $num_fila . '/SUM(E$49:E$53)');
            }

            //ubigeo
            $fila = count($distrito_V_A[0]) + 1;
            $objPHPExcel->getActiveSheet()->getStyle('C56:F56')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B57:F' . (57 + $fila))->applyFromArray($styleArray2);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B57', 'Distrito Vivienda')
                ->setCellValue('C56', 'Periodo')
                ->setCellValue('C57', 'Cantidad')
                ->setCellValue('D57', '%')
                ->setCellValue('E56', 'Total')
                ->setCellValue('E57', 'Cantidad')
                ->setCellValue('F57', '%');

            $num_fila = 58;
            $localidades = array();
            $localidades_fila = array();
            $state_fila = 0;
            foreach ($distrito_V_A[0] as $dato) {
                $localidades[$num_fila] = trim($dato->distrito_vive);
                $localidades_fila[trim($dato->distrito_vive)] = $num_fila;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B' . $num_fila, $dato->distrito_vive)
                    ->setCellValue('E' . $num_fila, $dato->Cantidad)
                    ->setCellValue('F' . $num_fila, '=+E' . $num_fila . '/SUM(E$58:E$' . (57 + $fila) . ')');
                $num_fila++;
                $state_fila = 1;
            }

            if ($localidades != array()) {
                foreach ($distrito_V_P[0] as $dato) {
                    $num_fila = $localidades_fila[trim($dato->distrito_vive)];
                    if ($localidades[$num_fila] == trim($dato->distrito_vive)) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('C' . $num_fila, $dato->Cantidad)
                            ->setCellValue('D' . $num_fila, '=+C' . $num_fila . '/SUM(C$58:C$' . (57 + $fila) . ')');
                    }
                }
            }
            if ($state_fila) $num_fila++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $num_fila, 'no definido')
                ->setCellValue('C' . $num_fila, $distrito_V_P[1])
                ->setCellValue('D' . $num_fila, '=+C' . $num_fila . '/SUM(C$58:C$' . (57 + $fila) . ')')
                ->setCellValue('E' . $num_fila, $distrito_V_A[1])
                ->setCellValue('F' . $num_fila, '=+E' . $num_fila . '/SUM(E$58:E$' . (57 + $fila) . ')');
            $num_fila++;

            /////////////////////////
            $fila = count($distrito_T_A[0]) + 1;

            $objPHPExcel->getActiveSheet()->mergeCells('C' . ($num_fila + 2) . ':D' . ($num_fila + 2))
                ->getStyle('C' . ($num_fila + 2) . ':D' . ($num_fila + 2))->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('E' . ($num_fila + 2) . ':F' . ($num_fila + 2))
                ->getStyle('E' . ($num_fila + 2) . ':F' . ($num_fila + 2))->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->getStyle('C' . ($num_fila + 2) . ':F' . ($num_fila + 2))
                ->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('B' . ($num_fila + 3) . ':F' . (($num_fila + 3) + $fila))
                ->applyFromArray($styleArray2);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . ($num_fila + 3), 'Distrito Trabajo')
                ->setCellValue('C' . ($num_fila + 2), 'Periodo')
                ->setCellValue('C' . ($num_fila + 3), 'Cantidad')
                ->setCellValue('D' . ($num_fila + 3), '%')
                ->setCellValue('E' . ($num_fila + 2), 'Total')
                ->setCellValue('E' . ($num_fila + 3), 'Cantidad')
                ->setCellValue('F' . ($num_fila + 3), '%');
            $num_fila = $num_fila + 4;
            $num_fila_ini = $num_fila;
            $localidades = array();
            $localidades_fila = array();
            foreach ($distrito_T_A[0] as $dato) {
                $localidades[$num_fila] = trim($dato->distrito_trabaja);
                $localidades_fila[trim($dato->distrito_trabaja)] = $num_fila;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B' . $num_fila, $dato->distrito_trabaja)
                    ->setCellValue('E' . $num_fila, $dato->Cantidad)
                    ->setCellValue('F' . $num_fila, '=+E' . $num_fila .
                        '/SUM(E$' . $num_fila_ini . ':E$' . ($num_fila_ini + $fila - 1) . ')');

                $num_fila++;
            }

            $fila_final = $num_fila;

            if ($localidades != array()) {
                foreach ($distrito_T_P[0] as $dato) {
                    $num_fila = $localidades_fila[trim($dato->distrito_trabaja)];
                    if ($localidades[$num_fila] == trim($dato->distrito_trabaja)) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('C' . $num_fila, $dato->Cantidad)
                            ->setCellValue('D' . $num_fila, '=+C' . $num_fila .
                                '/SUM(C$' . $num_fila_ini . ':C$' . ($num_fila_ini + $fila - 1) . ')');
                    }
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $fila_final, 'no definido')
                ->setCellValue('C' . $fila_final, $distrito_T_P[1])
                ->setCellValue('D' . $fila_final, '=+C' . $fila_final .
                    '/SUM(C$' . $num_fila_ini . ':C$' . ($num_fila_ini + $fila - 1) . ')')
                ->setCellValue('E' . $fila_final, $distrito_T_A[1])
                ->setCellValue('F' . $fila_final, '=+E' . $fila_final .
                    '/SUM(E$' . $num_fila_ini . ':E$' . ($num_fila_ini + $fila - 1) . ')');

            //CAT
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('H9', 'Categorias')
                ->setCellValue('I8', 'Periodo')
                ->setCellValue('I9', 'Cantidad')
                ->setCellValue('J9', '%')
                ->setCellValue('K8', 'Total')
                ->setCellValue('K9', 'Cantidad')
                ->setCellValue('L9', '%');

            $count = 10;
            $session_cat_acumulado = 0;
            $session_cat_periodo = 0;

            foreach ($categorias as $dato) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('H' . $count, $dato->Nombre);

                if (count($session_cat) > 0) {
                    $visitas_total = array();

                    for ($i = 0; $i < count($session_cat); $i++) {
                        if ($session_cat[$i]['_id'] == $dato->Slug) {
                            $visitas_total[] = $session_cat[$i];
                        }
                    }

                    if ($visitas_total) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . $count, $visitas_total[0]['count_e_n'])
                            ->setCellValue('L' . $count, '');
                        $session_cat_acumulado += $visitas_total[0]['count_e_n'];
                    }

                }

                if (count($session_cat_per) > 0) {

                    $visitas_periodo = array();

                    for ($i = 0; $i < count($session_cat_per); $i++) {
                        if ($session_cat_per[$i]['_id'] == $dato->Slug) {
                            $visitas_periodo[] = $session_cat_per[$i];
                        }
                    }

                    if ($visitas_periodo) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('I' . $count, $visitas_periodo[0]['count_e_n'])
                            ->setCellValue('J' . $count, '');
                        $session_cat_periodo += $visitas_periodo[0]['count_e_n'];
                    }
                }
                $count++;
            }

            $array_cat = array(
                'bus' => 'busqueda',
                'cam' => 'campaign',
                'com' => 'company',
                'tie' => 'tienda'
            );

            foreach ($array_cat as $dato) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('H' . $count, $dato);

                if (count($session_cat) > 0) {
                    $visitas_total = array();

                    for ($i = 0; $i < count($session_cat); $i++) {
                        if ($session_cat[$i]['_id'] == $dato) {
                            $visitas_total[] = $session_cat[$i];
                        }
                    }
                    if ($visitas_total) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('K' . $count, $visitas_total[0]['count_e_n'])
                            ->setCellValue('L' . $count, '');
                        $session_cat_acumulado += $visitas_total[0]['count_e_n'];
                    }

                }

                if (count($session_cat_per) > 0) {

                    $visitas_periodo = array();

                    for ($i = 0; $i < count($session_cat_per); $i++) {
                        if ($session_cat_per[$i]['_id'] == $dato) {
                            $visitas_periodo[] = $session_cat_per[$i];
                        }
                    }

                    if ($visitas_periodo) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('I' . $count, $visitas_periodo[0]['count_e_n'])
                            ->setCellValue('J' . $count, '');

                        $session_cat_periodo += $visitas_periodo[0]['count_e_n'];
                    }

                }
                $count++;
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('I' . $count, '=SUM(I10:I' . ($count - 1) . ')')
                ->setCellValue('K' . $count, '=SUM(K10:K' . ($count - 1) . ')')
                ->setCellValue('C10', $session_cat_periodo)
                ->setCellValue('D10', $session_cat_acumulado);
            for ($i = 10; $i < $count; $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('J' . $i, '=+I' . $i . '/$I$' . $count . '')
                    ->setCellValue('L' . $i, '=+K' . $i . '/$K$' . $count . '');
            }

        }

        //REPORTE 3.2//////////////////////////////////////////////////////////////
        $nombes_cat = array();
        $array_cat_id = array();
        $categorias = $getCategorias->getCategoriaIds();
        foreach ($categorias as $dato) {
            $nombes_cat[] = $dato->Nombre;
            $array_cat_id[] = $dato->id;
        }

        $cant_categorias = count($nombes_cat) + 4;
        $objSheet = $objPHPExcel->createSheet(1);
        $objSheet->setTitle('Repote 3.2');
        $objSheet->setShowGridlines(false);

        $letra = 66;
        $letra_tope = 90;
        $letra_inicio = 64;
        $letra_final = 'L';
        $letra_chr = null;
        while ($letra < (83 + (($cant_categorias * 2) * 4))) {
            if ($letra <= $letra_tope) {
                if ($letra_tope > 90) {
                    $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                    $letra_chr = chr($letra_inicio) . chr($letra_chr);
                } else {
                    $letra_chr = chr($letra);
                }
            } elseif ($letra <= $letra_tope + 26) {
                $letra_inicio++;
                $letra_tope = $letra_tope + 26;
                $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                $letra_chr = chr($letra_inicio) . chr($letra_chr);
            }

            $objSheet->getColumnDimension($letra_chr)->setAutoSize(true);
            $letra++;
            $letra_final = $letra_chr;
        }

        $objSheet
            ->setCellValue('B3', 'Fecha de ingreso')
            ->setCellValue('C3', 'Dni')
            ->setCellValue('D3', 'Nombre')
            ->setCellValue('E3', 'Apellidos')
            ->setCellValue('F3', 'Dias inactivo')
            ->setCellValue('G2', 'Periodo')
            ->setCellValue('G3', 'Visitas')
            ->setCellValue('H3', 'Descargas')
            ->setCellValue('I3', 'Redenciones')
            ->setCellValue('J2', 'Total')
            ->setCellValue('J3', 'Visitas')
            ->setCellValue('K3', 'Descargas')
            ->setCellValue('L3', 'Redenciones');

        $objSheet->getStyle('G2:I2')->applyFromArray($styleArray2);
        $objSheet->getStyle('J2:L2')->applyFromArray($styleArray2);
        $objSheet->mergeCells('G2:I2')->getStyle('G2:I2')->applyFromArray($center);
        $objSheet->mergeCells('J2:L2')->getStyle('J2:L2')->applyFromArray($center);

        $objSheet->getStyle('B3:' . $letra_final . '3')->applyFromArray($styleArray2);

        $letra = 77;
        $letra2 = 78;
        $letra_tope = 90;
        $letra_tope2 = 90;
        $letra_inicio = 64;
        $letra_inicio2 = 64;
        $letra_chr = null;
        $letra_chr2 = null;
        $letra_ini = null;
        $array_titulos = array(
            'Descargas en el periodo por categoria',
            'Redenciones en el periodo por categoria',
            'Descargas Totales por categoria',
            'Redenciones totales por categoria'
        );

        for ($i = 0; $i < 4; $i++) {
            $titulo = $array_titulos[$i];
            $count = 0;
            for ($j = 0; $j < count($nombes_cat); $j++) {
                if ($letra <= $letra_tope) {
                    if ($letra_tope > 90) {
                        $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                        $letra_chr = chr($letra_inicio) . chr($letra_chr);
                    } else {
                        $letra_chr = chr($letra);
                    }
                } elseif ($letra <= $letra_tope + 26) {
                    $letra_inicio++;
                    $letra_tope = $letra_tope + 26;
                    $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                    $letra_chr = chr($letra_inicio) . chr($letra_chr);
                }

                if ($count == 0) {
                    $letra_ini = $letra_chr;
                }

                if ($letra2 <= $letra_tope2) {
                    if ($letra_tope2 > 90) {
                        $letra_chr2 = ($letra2 - ($letra_tope2 - 26)) + 64;
                        $letra_chr2 = chr($letra_inicio2) . chr($letra_chr2);
                    } else {
                        $letra_chr2 = chr($letra2);
                    }
                } elseif ($letra2 <= $letra_tope2 + 26) {
                    $letra_inicio2++;
                    $letra_tope2 = $letra_tope2 + 26;
                    $letra_chr2 = ($letra2 - ($letra_tope2 - 26)) + 64;
                    $letra_chr2 = chr($letra_inicio2) . chr($letra_chr2);
                }

                $objSheet
                    ->setCellValue($letra_chr . '3', $nombes_cat[$j])
                    ->setCellValue($letra_chr2 . '3', '% ' . $nombes_cat[$j]);

                $letra = $letra + 2;
                $letra2 = $letra2 + 2;
                $count++;
            }

            $array_cat = array(
                'bus' => 'busqueda',
                'cam' => 'campaign',
                'com' => 'company',
                'tie' => 'tienda'
            );

            foreach ($array_cat as $key => $dato) {
                if ($letra <= $letra_tope) {
                    if ($letra_tope > 90) {
                        $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                        $letra_chr = chr($letra_inicio) . chr($letra_chr);
                    } else {
                        $letra_chr = chr($letra);
                    }
                } elseif ($letra <= $letra_tope + 26) {
                    $letra_inicio++;
                    $letra_tope = $letra_tope + 26;
                    $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                    $letra_chr = chr($letra_inicio) . chr($letra_chr);
                }

                if ($letra2 <= $letra_tope2) {
                    if ($letra_tope2 > 90) {
                        $letra_chr2 = ($letra2 - ($letra_tope2 - 26)) + 64;
                        $letra_chr2 = chr($letra_inicio2) . chr($letra_chr2);
                    } else {
                        $letra_chr2 = chr($letra2);
                    }
                } elseif ($letra2 <= $letra_tope2 + 26) {
                    $letra_inicio2++;
                    $letra_tope2 = $letra_tope2 + 26;
                    $letra_chr2 = ($letra2 - ($letra_tope2 - 26)) + 64;
                    $letra_chr2 = chr($letra_inicio2) . chr($letra_chr2);
                }
                $objSheet
                    ->setCellValue($letra_chr . '3', $dato)
                    ->setCellValue($letra_chr2 . '3', '% ' . $dato);

                $letra = $letra + 2;
                $letra2 = $letra2 + 2;
            }
            $objSheet->setCellValue($letra_ini . '2', $titulo);
            $objSheet->getStyle($letra_ini . '2:' . $letra_chr2 . '2')->applyFromArray($styleArray2);
            $objSheet->mergeCells($letra_ini . '2:' . $letra_chr2 . '2')
                ->getStyle($letra_ini . '2:' . $letra_chr2 . '2')->applyFromArray($center);
        }

        $array_demogrfico = array(
            'Edad',
            'Estado Civil',
            'Genero',
            'Hijos',
            'Distrito Vivienda',
            'Distrito Trabajo'
        );
        $count = 0;

        foreach ($array_demogrfico as $key => $dato) {
            if ($letra <= $letra_tope) {
                if ($letra_tope > 90) {
                    $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                    $letra_chr = chr($letra_inicio) . chr($letra_chr);
                } else {
                    $letra_chr = chr($letra);
                }
            } elseif ($letra <= $letra_tope + 26) {
                $letra_inicio++;
                $letra_tope = $letra_tope + 26;
                $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                $letra_chr = chr($letra_inicio) . chr($letra_chr);
            }

            if ($count == 0) {
                $letra_ini = $letra_chr;
            }

            $objSheet->setCellValue($letra_chr . '3', $dato);
            $letra++;
            $count++;
        }

        $objSheet->setCellValue($letra_ini . '2', 'Demografico');
        $objSheet->getStyle($letra_ini . '2:' . $letra_chr . '2')->applyFromArray($styleArray2);
        $objSheet->mergeCells($letra_ini . '2:' . $letra_chr . '2')
            ->getStyle($letra_ini . '2:' . $letra_chr . '2')->applyFromArray($center);
        //////////////////////////////////////////////////////////////////////////////////
        $pagina_analytics = 1;
        $lista_dnis = array();
        $dnis_Cop = array();

        $dnis = $mongo->get_dnis($fechaInicio_defecto, $fechaFin_defecto, $empresa);

        foreach ($dnis as $dato) {
            $dnis_Cop[$dato['_id']] = $dato['count_e_n'];
        }

        $dnis_acumulado = $dnis;

        foreach ($dnis_acumulado as $dato) {
            $lista_dnis[] = $dato['_id'];
        }

        if ($lista_dnis) {
            $array_acumulado = array();
            $array_periodo = array();
            $dnis_periodo_Cop = array();
            $array_letras = array();
            $array_demogrfico_campos = array(
                'Edad', 'estado', 'Genero', 'hijos', 'distrito_vive', 'distrito_trabaja'
            );

            $dnis_periodo = $mongo->get_dnis($fechaInicio, $fechaFin, $empresa);

            foreach ($dnis_periodo as $dato) {
                $dnis_periodo_Cop[$dato['_id']] = $dato['count_e_n'];
            }

            $fila_dnis = 4;

            //variables para calcular letra de las columnas
            $letra_tope = 90;
            $letra_inicio = 64;
            $letra = 77;
            $letra_chr = null;

            $cant_colum = (($cant_categorias * 2) * 4 + 6);

            $array_cat_id[] = 'Bus';
            $array_cat_id[] = 'Cam';
            $array_cat_id[] = 'Com';
            $array_cat_id[] = 'Tie';
            //var_dump($array_cat_id);exit;
            $contador = 0;
            $alternador = 0;
            $demo = 0;
            for ($i = 0; $i < $cant_colum; $i++) {
                if ($letra <= $letra_tope) {
                    if ($letra_tope > 90) {
                        $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                        $letra_chr = chr($letra_inicio) . chr($letra_chr);
                    } else {
                        $letra_chr = chr($letra);
                    }
                } elseif ($letra <= $letra_tope + 26) {
                    $letra_inicio++;
                    $letra_tope = $letra_tope + 26;
                    $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                    $letra_chr = chr($letra_inicio) . chr($letra_chr);
                }
                if ($i < 88) {
                    $array_letras[] = array($letra_chr, $array_cat_id[$contador]);
                } else {
                    $array_letras[] = array($letra_chr, $array_demogrfico_campos[$contador]);
                    $demo = 1;
                }

                $letra++;
                if ($alternador == 1 && $demo == 0) {
                    $contador++;
                    $alternador = -1;
                } else if ($demo == 1) {
                    $contador++;
                }
                $alternador++;
                if ($contador == 11) {
                    $contador = 0;
                }
            }

            ///var_dump($array_letras);exit;

            //////
            $list_categorias_ids = $getCategorias->getCategoriaIds();

            $data_cliente_total = $getMetCliente->getDataCliente(
                $lista_dnis, $list_categorias_ids, $id_empresa, $fechaInicio_defecto, $fechaFin_defecto
            );

            $des_red_total = (array)$getMetCliente->getDataDescargasRedimidos(
                $id_empresa, $list_categorias_ids, $fechaInicio_defecto, $fechaFin_defecto
            );

            foreach ($data_cliente_total as $data) {
                $array_acumulado[$data->NumeroDocumento] = (array)$data;
            }

            $data_cliente_periodo = $getMetCliente
                ->getDataCliente($lista_dnis, $list_categorias_ids, $id_empresa, $fechaInicio, $fechaFin);
            $des_red_periodo = (array)$getMetCliente
                ->getDataDescargasRedimidos($id_empresa, $list_categorias_ids, $fechaInicio, $fechaFin);

            foreach ($data_cliente_periodo as $data) {
                $array_periodo[$data->NumeroDocumento] = (array)$data;
            }

            //var_dump($array_periodo['000245227'][(($tipo == 'D') ?'DesCat':'RedCat') . 1]);exit;
            foreach ($lista_dnis as $dato) {
                $objSheet->setCellValue(
                    'B' . $fila_dnis,
                    date("Y-m-d", strtotime((!isset($array_acumulado[$dato])) ? '' : $array_acumulado[$dato]['FechaCreacion']))
                );
                $objSheet->setCellValue('C' . $fila_dnis, $dato);
                $objSheet->setCellValue('D' . $fila_dnis, (!isset($array_acumulado[$dato])) ? '' : $array_acumulado[$dato]['Nombre']);
                $objSheet->setCellValue('E' . $fila_dnis, (!isset($array_acumulado[$dato])) ? '' : $array_acumulado[$dato]['Apellido']);
                $objSheet->setCellValue('F' . $fila_dnis, (!isset($array_acumulado[$dato])) ? '' : $array_acumulado[$dato]['DiasUltimoLogin']);
                $objSheet->setCellValue('G' . $fila_dnis, isset($dnis_periodo_Cop[$dato]) ? $dnis_periodo_Cop[$dato] : 0);
                $objSheet->setCellValue('H' . $fila_dnis, (!isset($array_periodo[$dato])) ? 0 : (double)($array_periodo[$dato]['Descargas']));
                $objSheet->setCellValue('I' . $fila_dnis, (!isset($array_periodo[$dato])) ? 0 : (double)($array_periodo[$dato]['Redimidos']));
                $objSheet->setCellValue('J' . $fila_dnis, $dnis_Cop[$dato]);
                $objSheet->setCellValue('K' . $fila_dnis, (!isset($array_acumulado[$dato])) ? 0 : (double)$array_acumulado[$dato]['Descargas']);
                $objSheet->setCellValue('L' . $fila_dnis, (!isset($array_acumulado[$dato])) ? 0 : (double)$array_acumulado[$dato]['Redimidos']);

                $alternador = 0;
                $tipo = 'D';
                $bloque = 'A';
                $demo = 0;
                for ($i = 0; $i < count($array_letras); $i++) {
                    if ($demo == 1) {
                        $objSheet->setCellValue(
                            $array_letras[$i][0] . $fila_dnis,
                            (!isset($array_acumulado[$dato])) ? '' : $array_acumulado[$dato][$array_letras[$i][1]]
                        );
                    } else if ($bloque == 'A') {
                        if ($alternador == 0) {
                            $objSheet->setCellValue(
                                $array_letras[$i][0] . $fila_dnis,
                                (!isset($array_periodo[$dato])) ? '' : $array_periodo[$dato][(($tipo == 'D') ? 'DesCat' : 'RedCat') . $array_letras[$i][1]]
                            );
                        } else if ($alternador == 1) {
                            $num = (!isset($array_periodo[$dato])) ? 0 : $array_periodo[$dato][(($tipo == 'D') ? 'DesCat' : 'RedCat') . $array_letras[$i][1]];
                            $den = $des_red_total[(($tipo == 'D') ? 'DesCat' : 'RedCat') . $array_letras[$i][1]];
                            if ((int)$num > 0 and (int)$den > 0) {
                                $total = (double)round(($num / $den) * 100, 2);
                            } else {
                                $total = 0;
                            }
                            $objSheet->setCellValue(
                                $array_letras[$i][0] . $fila_dnis,
                                $total . '%'
                            );
                        }
                    } else if ($bloque == 'P') {
                        if ($alternador == 0) {
                            $objSheet->setCellValue(
                                $array_letras[$i][0] . $fila_dnis,
                                (!isset($array_acumulado[$dato])) ? '' : $array_acumulado[$dato][(($tipo == 'D') ? 'DesCat' : 'RedCat') . $array_letras[$i][1]]
                            );
                        } else if ($alternador == 1) {
                            $num = (!isset($array_acumulado[$dato])) ? 0 : $array_acumulado[$dato][(($tipo == 'D') ? 'DesCat' : 'RedCat') . $array_letras[$i][1]];
                            $den = $des_red_periodo[(($tipo == 'D') ? 'DesCat' : 'RedCat') . $array_letras[$i][1]];
                            if ((int)$num > 0 and (int)$den > 0) {
                                $total = (double)round(($num / $den) * 100, 2);
                            } else {
                                $total = 0;
                            }
                            $objSheet->setCellValue(
                                $array_letras[$i][0] . $fila_dnis,
                                $total . '%'
                            );
                        }
                    }

                    if ($alternador == 1) {
                        $alternador = -1;
                    }
                    $alternador++;
                    if ($i == 21 || $i == 65) {
                        $tipo = 'R';
                    } else if ($i == 43) {
                        $tipo = 'D';
                        $bloque = 'P';
                    } else if ($i == 87) {
                        $demo = 1;
                    }
                }
                $fila_dnis++;
            }
            $objSheet->getStyle('B3:' . $letra_final . ($fila_dnis - 1))->applyFromArray($styleArray2);
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte3.xlsx"');
        header('Cache-Control: max-age=0');

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Transfer-Encoding: binary ");

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/descargas/' . $nombre_excel);
        ReporteExcel::create(array('name' => $nombre_excel));
    }

    public function exportacion_2($fechaInicio2, $fechaFin2, $empresa, $costo, $meta, $nombre_excel)
    {
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
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
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );

        $mongo = new ClienteMongo();
        $getMetCliente = new DmMetCliente();
        $getCategorias = new Categoria();
        $getRubros = new Rubro();

        $fechaInicio_defecto = '2015-01-01';
        $fechaFin_defecto = date('Y-m-d');

        $fechaInicio = ($fechaInicio2 == '') ? $fechaInicio_defecto : $fechaInicio2;
        $fechaFin = ($fechaFin2 == '') ? $fechaFin_defecto : $fechaFin2;
        $id_empresa = ($empresa == '') ? '' : $empresa;
        $costo = ($costo == '') ? '' : $costo;
        $meta = ($meta == '') ? '' : $meta;

        $getEmpresa = new Empresa();

        $nombre_empresa = ($id_empresa == '') ? 'Todas Las Empresas' :
            'Empresa: ' . $getEmpresa->getEmpresa($id_empresa)->NombreComercial;


        // Analytics config
        // analytics new

        $getEmpresaCCTable = new EmpresaClienteCliente();

        $dnis_acumulado = $getEmpresaCCTable
            ->getClientesXEmpresa($id_empresa, $fechaInicio_defecto, $fechaFin_defecto);
        $dnis_periodico = $getEmpresaCCTable->getClientesXEmpresa($id_empresa, $fechaInicio, $fechaFin);

        $descargasorredimidos_a = $getMetCliente
            ->getDescargasOrRedimidos($id_empresa, $fechaInicio_defecto, $fechaFin_defecto);
        $descargasorredimidos_p = $getMetCliente->getDescargasOrRedimidos($id_empresa, $fechaInicio, $fechaFin);


        $descargas_acumulado = $descargasorredimidos_a->Descargas;
        $descargas_periodico = $descargasorredimidos_p->Descargas;

        $redimidos_acumulado = $descargasorredimidos_a->Redimidos;
        $redimidos_periodico = $descargasorredimidos_p->Redimidos;

        $categorias = $getCategorias->fetchAll();

        $lista_rubros = $getRubros->fetchAll();
        $count_rubros = count($lista_rubros);
        $getData = $getMetCliente->getDescargaRubros($lista_rubros, $id_empresa, $fechaInicio, $fechaFin);
        $registros = count($dnis_acumulado) + count($categorias);
        $objPHPExcel = new \PHPExcel();

        if ($registros > 0) {
            $session_cat = $mongo->get_session_category($fechaInicio_defecto, $fechaFin_defecto, $empresa);
            $session_cat_per = $mongo->get_session_category($fechaInicio, $fechaFin, $empresa);

            //Informacion del excel
            $objPHPExcel->
            getProperties()
                ->setCreator("Beneficios.pe")
                ->setLastModifiedBy("Beneficios.pe")
                ->setTitle("Reporte de Uso Anterior")
                ->setSubject("Reporte de Uso Anterior")
                ->setDescription("Reporte de Uso Anterior")
                ->setKeywords("Beneficios.pe")
                ->setCategory("Reporte de Uso Anterior");

            //desactiva cuadricula
            $objPHPExcel->getActiveSheet()->setShowGridlines(false);

            //cuadro rubros
            $letra = 65;
            $letra_tope = 90;
            $letra_inicio = 64;
            $letra_final = 'L';
            $letra_chr = null;
            while ($letra < (66 + $count_rubros)) {
                if ($letra <= $letra_tope) {
                    if ($letra_tope > 90) {
                        $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                        $letra_chr = chr($letra_inicio) . chr($letra_chr);
                    } else {
                        $letra_chr = chr($letra);
                    }
                } elseif ($letra <= $letra_tope + 26) {
                    $letra_inicio++;
                    $letra_tope = $letra_tope + 26;
                    $letra_chr = ($letra - ($letra_tope - 26)) + 64;
                    $letra_chr = chr($letra_inicio) . chr($letra_chr);
                }

                $objPHPExcel->getActiveSheet()->getColumnDimension($letra_chr)->setAutoSize(true);
                $letra++;
                $letra_final = $letra_chr;
            }

            $objPHPExcel->getActiveSheet()->getStyle('B17:' . $letra_final . '18')->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('B17:' . $letra_final . '17')->applyFromArray($styleArray);

            $objPHPExcel->getActiveSheet()->getStyle('B17:' . $letra_final . '17')->applyFromArray($styleArray2);
            //
            $columnas_final = 80 + count($categorias) * 2;
            //var_dump(count($categorias));exit;
            for ($i = 66; $i <= $columnas_final; $i++) {
                if ($i <= 90) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
                } else {
                    $columnas = ($i - 90) + 64;
                    $columnas = 'A' . chr($columnas);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnas)->setAutoSize(true);
                }
            }

            if ($columnas_final <= 90) {
                $columnas_final = chr($columnas_final);
            } else {
                $columnas_final = ($columnas_final - 90) + 64;
                $columnas_final = 'A' . chr($columnas_final);
            }

            $styleArray = array(
                'font' => array(
                    'bold' => false,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'C2D69B',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );
            $styleArray_acumulado = array(
                'font' => array(
                    'bold' => false,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FDE9D9',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );
            $styleArray_periodico = array(
                'font' => array(
                    'bold' => false,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'DAEEF3',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );
            $styleArray2 = array(
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
            );
            $styleArray3 = array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => '974806',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );
            $center = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );
            $scosto_meta = array(
                'font' => array(
                    'color' => array('rgb' => '846C2F'),
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'FFEB9C',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $delta = array(
                'font' => array(
                    'color' => array('rgb' => '006100'),
                ),
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startcolor' => array(
                        'argb' => 'C6EFCE',
                    ),
                    'endcolor' => array(
                        'argb' => 'FFFFFFFF',
                    ),
                ),
            );

            $rango = 'B7:' . $columnas_final . '13';

            $objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray2);

            $objPHPExcel->getActiveSheet()->getStyle('I7:' . $columnas_final . '7')->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('I9:' . $columnas_final . '10')->applyFromArray($styleArray3);
            $objPHPExcel->getActiveSheet()->getStyle('C7:E13')->applyFromArray($styleArray_acumulado);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H13')->applyFromArray($styleArray_periodico);

            $objPHPExcel->getActiveSheet()->mergeCells('C7:E7')->getStyle('C7:E7')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('F7:H7')->getStyle('F7:H7')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->mergeCells('I7:' . $columnas_final . '7')
                ->getStyle('I7:' . $columnas_final . '7')->applyFromArray($center);
            $objPHPExcel->getActiveSheet()->getStyle('C17:D17')->applyFromArray($center);

            $count_format = 10;
            while ($count_format <= 13) {
                $objPHPExcel->getActiveSheet()->getStyle('D' . $count_format)->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $count_format)->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $count_format++;
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B2', 'REPORTE DE USO ANTERIOR')
                ->setCellValue('B3', $nombre_empresa)
                ->setCellValue('B4', 'Periodo')
                ->setCellValue('B9', 'Dni')
                ->setCellValue('B10', 'Visitas de usuarios únicos')
                ->setCellValue('B11', 'Sesiones')
                ->setCellValue('B12', 'Descargas')
                ->setCellValue('B13', 'Redenciones')
                ->setCellValue('C3', 'Inicio')
                ->setCellValue('C4', ($fechaInicio == $fechaInicio_defecto) ? '-----' : $fechaInicio)
                ->setCellValue('C7', 'lineal acumulado')
                ->setCellValue('C8', 'TOTAL')
                ->setCellValue('C9', $dnis_acumulado)
                ->setCellValue('C12', $descargas_acumulado)
                ->setCellValue('C13', $redimidos_acumulado)
                ->setCellValue('D3', 'Fin')
                ->setCellValue('D4', $fechaFin)
                ->setCellValue('D8', 'CONVERSION %')
                ->setCellValue('D10', '=+C10/C9')
                ->setCellValue('D11', '=+C11/C9')
                ->setCellValue('D12', '=+C12/C9')
                ->setCellValue('D13', '=+C13/C9')
                ->setCellValue('E8', 'Descripcion')
                ->setCellValue('E10', 'Visitas unicas / dni')
                ->setCellValue('E11', 'Ratio sesion/dni')
                ->setCellValue('E12', 'Descargas/ dni')
                ->setCellValue('E13', 'Redenciones / dni')
                ->setCellValue('F7', 'delta del periodo')
                ->setCellValue('F8', 'TOTAL')
                ->setCellValue('F9', $dnis_periodico)
                ->setCellValue('F12', $descargas_periodico)
                ->setCellValue('F13', $redimidos_periodico)
                ->setCellValue('G8', 'CONVERSION %')
                ->setCellValue('G10', '=+F10/F9')
                ->setCellValue('G11', '=+F11/F10')
                ->setCellValue('G12', '=+F12/F11')
                ->setCellValue('G13', '=+F13/F12')
                ->setCellValue('H8', 'Descripcion')
                ->setCellValue('H10', 'Visitas unicas / dni')
                ->setCellValue('H11', 'Ratio sesion/dni')
                ->setCellValue('H12', 'Descargas/ dni')
                ->setCellValue('H13', 'Redenciones / dni')
                ->setCellValue('I7', 'CATEGORIA');

            $session_cat_acumulado = 0;
            $unique_cat_acumulado = 0;
            $session_cat_periodo = 0;
            $unique_cat_periodo = 0;

            $count = 8;
            foreach ($categorias as $dato) {

                $visitas_total = array();

                for ($i = 0; $i < count($session_cat); $i++) {
                    if ($session_cat[$i]['_id'] == $dato->Slug) {
                        $visitas_total[] = $session_cat[$i];
                    }
                }

                $visitas_periodo = array();

                for ($i = 0; $i < count($session_cat_per); $i++) {
                    if ($session_cat_per[$i]['_id'] == $dato->Slug) {
                        $visitas_periodo[] = $session_cat_per[$i];
                    }
                }

                $descargas_categoria = $getMetCliente
                    ->getDescargasCategoria($id_empresa, $fechaInicio, $fechaFin, $dato->id);
                $redimidos_categoria = $getMetCliente
                    ->getDescargasRedimidos($id_empresa, $fechaInicio, $fechaFin, $dato->id);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($count, 8, $dato->Nombre)
                    ->setCellValueByColumnAndRow($count, 11, @$visitas_periodo[0]['count_e_n'])
                    ->setCellValueByColumnAndRow($count, 12, $descargas_categoria->Descargas)
                    ->setCellValueByColumnAndRow($count, 13, $redimidos_categoria->Redimidos)
                    ->setCellValueByColumnAndRow($count + 1, 8, '% ' . $dato->Nombre)
                    ->setCellValueByColumnAndRow($count + 1, 11, '=+' . (int)@$visitas_periodo[0]['count_e_n'] . '/F11')
                    ->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(
                        $count + 1,
                        12,
                        '=+' . (int)$descargas_categoria->Descargas . '/F12'
                    )
                    ->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(
                        $count + 1,
                        13,
                        '=+' . (int)$redimidos_categoria->Redimidos . '/F13'
                    )
                    ->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                @$session_cat_acumulado += $visitas_total[0]['count_e_n'];
                @$unique_cat_acumulado += $visitas_total[0]['count_e_u'];
                @$session_cat_periodo += $visitas_periodo[0]['count_e_n'];
                @$unique_cat_periodo += $visitas_periodo[0]['count_e_u'];

                $count += 2;
            }

            $array_cat = array(
                'bus' => 'busqueda',
                'cam' => 'campaign',
                'com' => 'company',
                'tie' => 'tienda'
            );

            foreach ($array_cat as $key => $dato) {

                $visitas_total = array();

                for ($i = 0; $i < count($session_cat); $i++) {
                    if ($session_cat[$i]['_id'] == $dato) {
                        @$visitas_total[] = $session_cat[$i];
                    }
                }

                $visitas_periodo = array();

                for ($i = 0; $i < count($session_cat_per); $i++) {
                    if ($session_cat_per[$i]['_id'] == $dato) {
                        @$visitas_periodo[] = $session_cat_per[$i];
                    }
                }

                $descargas_categoria = $getMetCliente
                    ->getDescargasCategoria($id_empresa, $fechaInicio, $fechaFin, $key);
                $redimidos_categoria = $getMetCliente
                    ->getDescargasRedimidos($id_empresa, $fechaInicio, $fechaFin, $key);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($count, 8, $dato)
                    ->setCellValueByColumnAndRow($count, 11, @$visitas_periodo[0]['count_e_n'])
                    ->setCellValueByColumnAndRow($count, 12, $descargas_categoria->Descargas)
                    ->setCellValueByColumnAndRow($count, 13, $redimidos_categoria->Redimidos)
                    ->setCellValueByColumnAndRow($count + 1, 8, '% ' . $dato)
                    ->setCellValueByColumnAndRow($count + 1, 11, '=+' . (int)@$visitas_periodo[0]['count_e_n'] . '/F11')
                    ->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(
                        $count + 1,
                        12,
                        '=+' . (int)$descargas_categoria->Descargas . '/F12'
                    )
                    ->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow(
                        $count + 1,
                        13,
                        '=+' . (int)$redimidos_categoria->Redimidos . '/F13'
                    )
                    ->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
                @$session_cat_acumulado += $visitas_total[0]['count_e_n'];
                @$unique_cat_acumulado += $visitas_total[0]['count_e_u'];
                @$session_cat_periodo += $visitas_periodo[0]['count_e_n'];
                @$unique_cat_periodo += $visitas_periodo[0]['count_e_u'];

                $count += 2;
            }
            $diferencia_periodo = 0;
            $diferencia_total = 0;
            /*
            if ($id_empresa == '') {
                foreach ($config['diferencia_visitas_unicas'] as $key => $data) {
                    $diferencia_total += $data;
                    if ($this->checkInRange($fechaInicio, $fechaFin, $key)) {
                        $diferencia_periodo += $data;
                    }
                }
            }
            */

            $visitas_unicas = $mongo->getDnisUnicos($fechaInicio_defecto, $fechaFin_defecto, $empresa);
            $visitas_unicas_per = $mongo->getDnisUnicos($fechaInicio, $fechaFin, $empresa);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('F10', $visitas_unicas_per)
                ->setCellValue('F11', $session_cat_periodo)
                ->setCellValue('C10', $visitas_unicas)
                ->setCellValue('C11', $session_cat_acumulado)
                ->setCellValue('B16', 'DESCARGAS POR RUBRO');

            //rubros
            $column = 1;
            foreach ($getRubros->fetchAll() as $data) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($column, 17, $data->Nombre);
                $column++;
            }
            $row = 18;
            foreach ($getData as $data) {
                $data = (array)$data;
                $column = 1;
                foreach ($getRubros->fetchAll() as $datar) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($column, $row, (int)$data['Rubro' . $datar->id]);
                    $column++;
                }
                $row++;
            }

            if ($costo != '' && $meta != '') {
                $objPHPExcel->getActiveSheet()->getStyle('B23:D31')->applyFromArray($styleArray2);
                $objPHPExcel->getActiveSheet()->getStyle('C24')->applyFromArray($scosto_meta);
                $objPHPExcel->getActiveSheet()->getStyle('C28')->applyFromArray($scosto_meta);
                $objPHPExcel->getActiveSheet()->getStyle('D23:D31')->applyFromArray($delta);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B23', 'REPORTE 3')
                    ->setCellValue('C23', 'Lineal')
                    ->setCellValue('D23', 'Delta')
                    ->setCellValue('B24', 'Costo por descarga')
                    ->setCellValue('B25', 'Ratio sesiones/dni')
                    ->setCellValue('B26', 'Ratio descarga/sesiones')
                    ->setCellValue('C24', $costo)
                    ->setCellValue('C25', '=+C11/C9')
                    ->setCellValue('C26', '=+C12/C11')
                    ->setCellValue('D24', $costo)
                    ->setCellValue('D25', '=+F11/F9')
                    ->setCellValue('D26', '=+F12/F11')
                    ->setCellValue('B28', 'Meta en dinero')
                    ->setCellValue('B29', 'Descargas necesarias')
                    ->setCellValue('B30', 'Sesiones necesarias')
                    ->setCellValue('B31', 'Base necesaria utilizando el ratio actual')
                    ->setCellValue('C28', $meta)
                    ->setCellValue('C29', '=+C28/C24')
                    ->setCellValue('C30', '=+C29/C26')
                    ->setCellValue('C31', '=+C30/C25')
                    ->setCellValue('D28', $meta)
                    ->setCellValue('D29', '=+D28/D24')
                    ->setCellValue('D30', '=+D29/D26')
                    ->setCellValue('D31', '=+D30/D25');
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_de_Uso_Anterior.xlsx"');
        header('Cache-Control: max-age=0');

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Transfer-Encoding: binary ");

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/descargas/' . $nombre_excel);
        ReporteExcel::create(array('name' => $nombre_excel));
    }

    public function exportacion_3($fechaInicio2, $fechaFin2, $empresa, $nombre_excel)
    {
        $mongo = new ClienteMongo();
        $getMetCliente = new DmMetCliente();
        $getMetClientePreguntas = new DmMetClientePreguntas();
        $getRubro = new Rubro();
        $getEmpresa = new Empresa();
        $getEstadoCivil = new EstadoCivil();
        $getHijos = new Hijos();

        $fechaInicio_defecto = '2016-05-01';
        $fechaFin_defecto = date('Y-m-d');

        $fechaInicio = ($fechaInicio2 == '') ? $fechaInicio_defecto : $fechaInicio2;
        $fechaFin = ($fechaFin2 == '') ? $fechaFin_defecto : $fechaFin2;
        $id_empresa = ($empresa == '') ? '' : $empresa;

        $nombre_empresa = ($id_empresa == '') ? 'Todo el sitio' :
            'Empresa: ' . $getEmpresa->getEmpresa($id_empresa)->NombreComercial;

        //Recuperar Datos de rubro
        if ($id_empresa == 298) {
            $cabeceras = array(
                'Correos',
                'Fecha última Descarga'
            );
        } else {
            //Cabeceras principales del reporte
            $cabeceras = array(
                'Tipo Documento',
                'Nro de documento',
                'Correos',
                'Nombre',
                'Apellidos',
                'Celular',
                'Año Nacimiento',
                'Estado Civil',
                'Nivel Educativo',
                'Género',
                'Hijos',
                'Distrito',
                'Lugar de Trabajo',
                'Fecha última Descarga'
            );
        }
        $rubros = $getRubro->fetchAll();
        $rubros_id = array();
        for ($i = 0; $i < 2; $i++) {
            foreach ($rubros as $value) {
                $cabeceras[] = ($i) ? $value->Nombre . ' %' : $value->Nombre;
                if ($i) {
                    $rubros_id[$value->id] = $value->Nombre . '-' . $value->id;
                }
            }
        }

        //Sesiones del periodo
        $session_cat_per = $mongo->get_session_category($fechaInicio, $fechaFin, $empresa);
        $session_total = 0;
        foreach ($session_cat_per as $value) {
            $session_total += $value['count_e_n'];
        }
        $session_unicas = $mongo->getRegistrosUnicos($fechaInicio, $fechaFin, $empresa);


        //creacion excel
        $styleArray2 = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            ),
        );

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->
        getProperties()
            ->setCreator("Beneficios.pe")
            ->setLastModifiedBy("Beneficios.pe")
            ->setTitle("Reporte de Uso")
            ->setSubject("Reporte de Uso")
            ->setDescription("Reporte de Uso")
            ->setKeywords("Beneficios.pe")
            ->setCategory("Reporte de Uso");
        //Desactivar cuadricula
        $objPHPExcel->getActiveSheet()->setShowGridlines(false);

        if ($id_empresa == 298) {

            //Recuperar Datos de los Clientes
            $data = null;
            try {
                $data = $getMetCliente->getCorreos($fechaInicio, $fechaFin, $empresa, $rubros_id);
            } catch (\Exception $e) {
                dd($e->getFile(), $e->getLine(), $e->getMessage());
            }

            $columnas_final = 66 + count($cabeceras);
            $columna = null;

            for ($i = 65; $i < $columnas_final; $i++) {
                if ($i <= 90) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
                    $columna = chr($i);
                } else {
                    $columna = ($i - 90) + 64;
                    $columna = 'A' . chr($columna);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columna)->setAutoSize(true);
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle('A1:C6')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A11:' . $columna . (count($data) + 11))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()
                ->getStyle('B11:B' . (count($data) + 10))
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow(0, 1, 'REPORTE DE USO')
                ->setCellValueByColumnAndRow(0, 2, 'Empresa')
                ->setCellValueByColumnAndRow(1, 2, $nombre_empresa)
                ->setCellValueByColumnAndRow(0, 3, 'Periodo')
                ->setCellValueByColumnAndRow(1, 3, $fechaInicio)
                ->setCellValueByColumnAndRow(2, 3, $fechaFin)
                ->setCellValueByColumnAndRow(0, 5, 'Visitas del periodo')
                ->setCellValueByColumnAndRow(1, 5, $session_total)
                ->setCellValueByColumnAndRow(0, 6, 'Descargas del periodo');

            for ($i = 0; $i < count($cabeceras); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($i, 11, $cabeceras[$i]);
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 11, 'Total Descargas');

            $total_descargas = 0;
            $fila = 12;
            try {
                foreach ($data as $value) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow(0, $fila, $value->Correo)
                        ->setCellValueByColumnAndRow(1, $fila, $value->FechaGenerado);

                    $total_descargas_cliente = 0;
                    $columna_rubros = 2;
                    foreach ($rubros as $valueR) {
                        $value = (array)$value;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($columna_rubros, $fila, $value[$valueR->Nombre . '-' . $valueR->id]);
                        $total_descargas_cliente += $value[$valueR->Nombre . '-' . $valueR->id];
                        $columna_rubros++;
                    }

                    foreach ($rubros as $valueR) {
                        $value = (array)$value;
                        $porcentaje = ((int)$value[$valueR->Nombre . '-' . $valueR->id])
                            ? round(($value[$valueR->Nombre . '-' . $valueR->id] / $total_descargas_cliente) * 100, 2) : 0;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($columna_rubros, $fila, $porcentaje . ' %');
                        $columna_rubros++;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($columna_rubros, $fila, $total_descargas_cliente);
                    $fila++;
                    $total_descargas += $total_descargas_cliente;
                }
            } catch (\Exception $e) {
                dd($e->getFile(), $e->getLine(), $e->getMessage());
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow(1, 6, $total_descargas);
        } else {
            $getEmpresaCCTable = new EmpresaClienteCliente();

            $dnis_acumulado = $getEmpresaCCTable
                ->getClientesXEmpresa($id_empresa, '2015-01-01', $fechaFin);

            //Cantidad de hijos
            $hijos = $getHijos->fetchAll();
            $arrayHijos = array();
            foreach ($hijos as $value) {
                $arrayHijos[$value->id] = $value->hijos;
            }

            //Estado Civil
            $estadoCivil = $getEstadoCivil->fetchAll();
            $arrayEstadoCivil = array();
            foreach ($estadoCivil as $value) {
                $arrayEstadoCivil[$value->id] = $value->estado;
            }

            //Recuperar Datos de los Clientes
            $data = null;
            try {
                $data = $getMetCliente->getClientes($fechaInicio, $fechaFin, $empresa, $rubros_id);
            } catch (\Exception $e) {
                dd($e->getFile(), $e->getLine(), $e->getMessage());
            }

            $columnas_final = 66 + count($cabeceras);
            $columna = null;

            for ($i = 65; $i < $columnas_final; $i++) {
                if ($i <= 90) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($i))->setAutoSize(true);
                    $columna = chr($i);
                } else {
                    $columna = ($i - 90) + 64;
                    $columna = 'A' . chr($columna);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columna)->setAutoSize(true);
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle('A1:C8')->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getStyle('A11:' . $columna . (count($data) + 11))->applyFromArray($styleArray2);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
            $objPHPExcel->getActiveSheet()
                ->getStyle('B11:B' . (count($data) + 10))
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow(0, 1, 'REPORTE DE USO')
                ->setCellValueByColumnAndRow(0, 2, 'Empresa')
                ->setCellValueByColumnAndRow(1, 2, $nombre_empresa)
                ->setCellValueByColumnAndRow(0, 3, 'Periodo')
                ->setCellValueByColumnAndRow(1, 3, $fechaInicio)
                ->setCellValueByColumnAndRow(2, 3, $fechaFin)
                ->setCellValueByColumnAndRow(0, 5, 'DNIs registrados a hoy')
                ->setCellValueByColumnAndRow(1, 5, $dnis_acumulado)
                ->setCellValueByColumnAndRow(0, 6, 'Visitas del periodo')
                ->setCellValueByColumnAndRow(1, 6, $session_total)
                ->setCellValueByColumnAndRow(0, 7, 'Visitas Unicas del periodo')
                ->setCellValueByColumnAndRow(1, 7, $session_unicas)
                ->setCellValueByColumnAndRow(0, 8, 'Descargas del periodo');

            for ($i = 0; $i < count($cabeceras); $i++) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($i, 11, $cabeceras[$i]);
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 11, 'Total Descargas');

            $total_descargas = 0;
            $fila = 12;
            try {
                foreach ($data as $value) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow(0, $fila, $value->Nombre)
                        ->setCellValueByColumnAndRow(1, $fila, $value->NumeroDocumento)
                        ->setCellValueByColumnAndRow(2, $fila, $value->Correo)
                        ->setCellValueByColumnAndRow(3, $fila, $value->nombres)
                        ->setCellValueByColumnAndRow(4, $fila, $value->apellidos)
                        ->setCellValueByColumnAndRow(5, $fila, $value->celular)
                        ->setCellValueByColumnAndRow(6, $fila, $value->FechaNacimiento)
                        ->setCellValueByColumnAndRow(7, $fila, $arrayEstadoCivil[$value->BNF_DM_Dim_EstadoCivil_id])
                        ->setCellValueByColumnAndRow(8, $fila, $value->nivel_estudios)
                        ->setCellValueByColumnAndRow(9, $fila, $value->Genero)
                        ->setCellValueByColumnAndRow(10, $fila, ($arrayHijos[$value->BNF_DM_Dim_Hijos_id] == -1)
                            ? 'no-definido' : $arrayHijos[$value->BNF_DM_Dim_Hijos_id])
                        ->setCellValueByColumnAndRow(11, $fila, $value->distrito_vive)
                        ->setCellValueByColumnAndRow(12, $fila, $value->distrito_trabaja)
                        ->setCellValueByColumnAndRow(13, $fila, $value->FechaGenerado);

                    $total_descargas_cliente = 0;
                    $columna_rubros = 14;
                    foreach ($rubros as $valueR) {
                        $value = (array)$value;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($columna_rubros, $fila, $value[$valueR->Nombre . '-' . $valueR->id]);
                        $total_descargas_cliente += $value[$valueR->Nombre . '-' . $valueR->id];
                        $columna_rubros++;
                    }

                    foreach ($rubros as $valueR) {
                        $value = (array)$value;
                        $porcentaje = ((int)$value[$valueR->Nombre . '-' . $valueR->id])
                            ? round(($value[$valueR->Nombre . '-' . $valueR->id] / $total_descargas_cliente) * 100, 2) : 0;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($columna_rubros, $fila, $porcentaje . ' %');
                        $columna_rubros++;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($columna_rubros, $fila, $total_descargas_cliente);
                    $fila++;
                    $total_descargas += $total_descargas_cliente;
                }
            } catch (\Exception $e) {
                dd($e->getFile(), $e->getLine(), $e->getMessage());
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow(1, 8, $total_descargas);
        }


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_de_Uso.xlsx"');
        header('Cache-Control: max-age=0');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Transfer-Encoding: binary ");

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(public_path() . '/descargas/' . $nombre_excel);
        ReporteExcel::create(array('name' => $nombre_excel));
    }
}