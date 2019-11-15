<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/01/16
 * Time: 10:14 AM
 */

namespace Cron\Controller;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class ClienteController extends AbstractActionController
{
    protected $cliente;
    protected $empresaCliente;
    protected $empresaSegmento;
    protected $pregunta;

    const DIR_LOGS = './data/logs/cron/';
    const NAME_LOG_CLIENTE = 'delete-client.log';
    const NAME_LOG_PREGUNTAS = 'delete-preguntas.log';

    public function deleteAction()
    {
        $connection = null;
        $message = null;
        $idOriginal = 0;
        $dniOriginal = 0;
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_CLIENTE);

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $this->cliente = $this->serviceLocator->get('Cliente\Model\ClienteTable');
        $this->empresaCliente = $this->serviceLocator->get('Cliente\Model\EmpresaClienteClienteTable');
        $this->empresaSegmento = $this->serviceLocator->get('Cliente\Model\EmpresaSegmentoClienteTable');
        $this->pregunta = $this->serviceLocator->get('Cliente\Model\Table\PreguntasTable');

        try {
            //Recuperando Clientes
            $clientes = $this->cliente->getDuplicates();
            $message = "Total de Clientes: " . count($clientes) . "\n";
            $totalEliminados = 0;
            foreach ($clientes as $value) {
                //Iniciamos datos
                $idOriginal = 0;
                $dniOriginal = 0;
                $clientesDuplicados = array();
                $empresasDuplicadas = array();
                $segmentosDuplicados = array();

                //Omitimos las que no estan duplicados
                if ($value->Total > 1) {
                    //Listado de los Ids Duplicados
                    $listadni = $this->cliente->getClienteList($value->NumeroDocumento);
                    foreach ($listadni as $key => $item) {
                        if ($key == 0) {
                            $idOriginal = $item["id"];
                            $dniOriginal = $item["NumeroDocumento"];
                        } else {
                            array_push($clientesDuplicados, $item["id"]);
                        }
                    }
                    //Listado de Empresas
                    $empresas = $this->empresaCliente->searchByDoc($dniOriginal);
                    foreach ($empresas as $key => $item) {
                        if ($key == 0) {
                            array_push($empresasDuplicadas, $item->BNF_Empresa_id);
                            $empresasDuplicadas = array_unique($empresasDuplicadas);
                        } else {
                            if (in_array($item->BNF_Empresa_id, $empresasDuplicadas)) {
                                //array_push($empresasDuplicadas, $item->id);
                                $this->empresaCliente->delete(
                                    array(
                                        "id" => $item->id
                                    )
                                );
                            } else {
                                array_push($empresasDuplicadas, $item->BNF_Empresa_id);
                                $this->empresaCliente->updateArray(
                                    array(
                                        "BNF_Cliente_id" => $idOriginal
                                    ),
                                    array(
                                        "id" => $item->id
                                    )
                                );
                            }
                        }
                    }
                    //Listado de Empresa Segmento
                    $segmentos = $this->empresaSegmento->searchByDoc($dniOriginal);
                    foreach ($segmentos as $key => $item) {
                        if ($key == 0) {
                            array_push($segmentosDuplicados, $item->BNF_EmpresaSegmento_id);
                            $segmentosDuplicados = array_unique($segmentosDuplicados);
                        } else {
                            if (in_array($item->BNF_EmpresaSegmento_id, $segmentosDuplicados)) {
                                //array_push($segmentosDuplicados, $item->idBNF_EmpresaSegmentoCliente);
                                $this->empresaSegmento->delete(
                                    array(
                                        "idBNF_EmpresaSegmentoCliente" => $item->idBNF_EmpresaSegmentoCliente
                                    )
                                );
                            } else {
                                array_push($segmentosDuplicados, $item->BNF_EmpresaSegmento_id);
                                $this->empresaSegmento->updateArray(
                                    array(
                                        "BNF_Cliente_id" => $idOriginal
                                    ),
                                    array(
                                        "idBNF_EmpresaSegmentoCliente" => $item->idBNF_EmpresaSegmentoCliente
                                    )
                                );
                            }
                        }
                    }
                    //Eliminando Registro de Preguntas
                    $preguntas = $this->pregunta->searchByDoc($dniOriginal);
                    foreach ($preguntas as $key => $item) {
                        if ($key != 0) {
                            $this->pregunta->delete(
                                array(
                                    "id" => $item->id
                                )
                            );
                        }
                    }
                    //Eliminando Registros de Clientes Duplicados
                    foreach ($clientesDuplicados as $item) {
                        $this->cliente->delete(
                            array(
                                "id" => $item
                            )
                        );
                        $totalEliminados++;
                    }
                }
            }
            $message = $message . "Total de Eliminados: " . $totalEliminados . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::INFO, $message);
        } catch (\Exception $e) {
            $logger->addWriter($writer);
            $logger->log(Logger::ERR, $e . " Cliente Id: " . $idOriginal . ", Doc: " . $dniOriginal . "\n");
            return $e . " Cliente Id: " . $idOriginal . ", Doc: " . $dniOriginal . "\n";
        }
        return $message . "\n";
    }

    public function cleanpreguntasAction()
    {
        $connection = null;
        $message = null;
        $idOriginal = 0;
        $dniOriginal = 0;
        $logger = new Logger;
        $writer = new Stream($this::DIR_LOGS . $this::NAME_LOG_PREGUNTAS);

        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $this->cliente = $this->serviceLocator->get('Cliente\Model\ClienteTable');
        $this->pregunta = $this->serviceLocator->get('Cliente\Model\Table\PreguntasTable');

        try {
            //Recuperando Clientes
            $clientes = $this->cliente->getDuplicatesClients();
            $message = "Total de Clientes: " . count($clientes) . "\n";
            $totalEliminados = 0;
            foreach ($clientes as $value) {
                //Iniciamos datos
                $idOriginal = 0;
                $dniOriginal = 0;

                //Omitimos las que no estan duplicados
                if ($value->Total > 1) {
                    //Listado de los Ids Duplicados
                    $listadni = $this->cliente->getClienteList($value->NumeroDocumento);
                    foreach ($listadni as $key => $item) {
                        if ($key == 0) {
                            $idOriginal = $item["id"];
                            $dniOriginal = $item["NumeroDocumento"];
                        }
                    }

                    //Eliminando Registro de Preguntas
                    $preguntas = $this->pregunta->searchByDoc($dniOriginal);
                    foreach ($preguntas as $key => $item) {
                        if ($key != 0) {
                            $this->pregunta->delete(
                                array(
                                    "id" => $item->id
                                )
                            );
                            $totalEliminados++;
                        }
                    }
                }
            }
            $message = $message . "Total de Eliminados: " . $totalEliminados . "\n";
            $logger->addWriter($writer);
            $logger->log(Logger::INFO, $message);
        } catch (\Exception $e) {
            $logger->addWriter($writer);
            $logger->log(Logger::ERR, $e . " Cliente Id: " . $idOriginal . ", Doc: " . $dniOriginal . "\n");
            return $e . " Cliente Id: " . $idOriginal . ", Doc: " . $dniOriginal . "\n";
        }
        return $message . "\n";
    }
}
