<?php

namespace Cupon\Controller;

use Cupon\Form\BusquedaCupon;
use Cupon\Form\FormCupon;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const ESTADO_CUPON_REDIMIDO = 'Redimido';
    const ESTADO_CUPON_CADUCADO = 'Caducado';
    const ESTADO_CUPON_GENERADO = 'Generado';

    #region ObjectTable
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    public function getOfertaAtributoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaAtributosTable');
    }

    public function getCuponTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\CuponTable');
    }

    public function getConfigTable()
    {
        return $this->serviceLocator->get('Cupon\Model\Table\ConfiguracionesTable');
    }
    #endregion

    public function indexAction()
    {
        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $mensaje = null;
        $fomData = array();
        $formS = new BusquedaCupon();
        $form = new FormCupon();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $codigoCupon = $tipo = $request->getPost()->cupon ? $request->getPost()->cupon : null;
            if ($codigoCupon != null) {
                $cupon = $this->getCuponTable()->searchCupon($codigoCupon, $this->identity()->BNF_Empresa_id);
                if (is_object($cupon)) {
                    $oferta = $this->getOfertaTable()->getOferta($cupon->BNF_Oferta_id);
                    $atributo = null;
                    if ($oferta->TipoAtributo == "Split") {
                        $atributo = $this->getOfertaAtributoTable()->getOfertaAtributos($cupon->BNF_Oferta_Atributo_id);
                    }

                    $fomData['id'] = $cupon->id;
                    $fomData['CodigoCupon'] = $codigoCupon;
                    $fomData['Titulo'] = ($oferta->TipoAtributo == "Split")
                        ? $atributo->NombreAtributo : $oferta->Titulo;
                    $fomData['CondicionesUso'] = strip_tags($oferta->CondicionesUso);
                    $fomData['FechaFinVigencia'] = ($oferta->TipoAtributo == "Split")
                        ? date_format(date_create($atributo->FechaVigencia), 'Y-m-d')
                        : date_format(date_create($oferta->FechaFinVigencia), 'Y-m-d');
                    $form->setData($fomData);
                } else {
                    $mensaje = "El cupon no es válido.";
                }
                $formS->setData(array('cupon' => $codigoCupon));
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'cupon' => 'active',
                'mensaje' => $mensaje,
                'formS' => $formS,
                'form' => $form,
            )
        );
    }

    public function addAction()
    {
        $dias = $this->getConfigTable()->getConfig('dias_expiracion')->Atributo;
        $request = $this->getRequest();
        $response = $this->getResponse();

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['id'];
            $cupon = $this->getCuponTable()->getCupon($id);
            if (!is_object($cupon)) {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'condition' => false,
                            'message' => 'El cupon no existe.'
                        )
                    )
                );
            } else {
                if ($cupon->EstadoCupon == $this::ESTADO_CUPON_REDIMIDO) {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'condition' => false,
                                'tittle' => 'Lo sentimos',
                                'message' => 'El cupon ya fue utilizado el día ' .
                                    date_format(date_create($cupon->FechaRedimido), 'Y-m-d') . "."
                            )
                        )
                    );
                } elseif ($cupon->EstadoCupon == $this::ESTADO_CUPON_CADUCADO) {
                    $oferta = $this->getOfertaTable()->getOferta($cupon->BNF_Oferta_id);
                    $atributo = null;
                    if ($oferta->TipoAtributo == "Split") {
                        $atributo = $this->getOfertaAtributoTable()->getOfertaAtributos($cupon->BNF_Oferta_Atributo_id);
                    }

                    $hoy = date_create('now');
                    $vigencia = ($oferta->TipoAtributo == "Split")
                        ? date_create($atributo->FechaVigencia)
                        : date_create($oferta->FechaFinVigencia);
                    date_add($vigencia, date_interval_create_from_date_string($dias . ' days'));
                    $diferencia = date_diff($hoy, $vigencia);

                    if ($diferencia->format("%r%a") >= 0) {
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'condition' => true,
                                    'message' => 'El cupon expiró el día ' .
                                        date_format(date_create($cupon->FechaCaducado), 'Y-m-d') .
                                        ". Desea redimir?"
                                )
                            )
                        );
                    } else {
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'condition' => false,
                                    'tittle' => 'Lo sentimos',
                                    'message' => 'El cupon expiró el día ' .
                                        date_format(date_create($cupon->FechaCaducado), 'Y-m-d') .
                                        ". No se puede redimir porque paso el periodo de gracia"
                                )
                            )
                        );
                    }
                } elseif ($cupon->EstadoCupon == $this::ESTADO_CUPON_GENERADO) {
                    $oferta = $this->getOfertaTable()->getOferta($cupon->BNF_Oferta_id);
                    $atributo = null;
                    if ($oferta->TipoAtributo == "Split") {
                        $atributo = $this->getOfertaAtributoTable()->getOfertaAtributos($cupon->BNF_Oferta_Atributo_id);
                    }

                    $hoy = date_create('now');
                    $vigencia = ($oferta->TipoAtributo == "Split")
                        ? date_create($atributo->FechaVigencia)
                        : date_create($oferta->FechaFinVigencia);
                    date_add($vigencia, date_interval_create_from_date_string($dias . ' days'));
                    $diferencia = date_diff($hoy, $vigencia);

                    if ($diferencia->format("%r%a") >= 0) {
                        $this->getCuponTable()->redimirCupon($cupon->id);
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => true,
                                    'condition' => false,
                                    'tittle' => 'Operación Completada',
                                    'message' => 'El cupon fue redimido correctamente.'
                                )
                            )
                        );
                    } else {
                        $cupon->EstadoCupon = 'Caducado';
                        $cupon->FechaCaducado = ($oferta->TipoAtributo == "Split")
                            ? $atributo->FechaVigencia
                            : $oferta->FechaFinVigencia;
                        $this->getCuponTable()->saveCupon($cupon);
                        $response->setContent(
                            Json::encode(
                                array(
                                    'response' => false,
                                    'condition' => false,
                                    'tittle' => 'Lo sentimos',
                                    'message' => 'El cupon expiró el día ' .
                                        date_format(date_create($cupon->FechaCaducado), 'Y-m-d') .
                                        ". No se puede redimir porque paso el periodo de gracia"
                                )
                            )
                        );
                    }
                } else {
                    $response->setContent(
                        Json::encode(
                            array(
                                'response' => false,
                                'condition' => false,
                                'tittle' => 'Lo sentimos',
                                'message' => 'El cupon está ' . $cupon->EstadoCupon
                            )
                        )
                    );
                }
            }
        }
        return $response;
    }

    public function redimirAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $id = (int)$post_data['iddata'];
            $cupon = $this->getCuponTable()->getCupon($id);
            if ($cupon == false) {
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => false,
                            'tittle' => 'Error',
                            'message' => 'El cupon no existe.'
                        )
                    )
                );
            } else {
                $this->getCuponTable()->redimirCupon($cupon->id);
                $response->setContent(
                    Json::encode(
                        array(
                            'response' => true,
                            'tittle' => 'Operación Completada',
                            'message' => 'El cupon fue redimido correctamente.'
                        )
                    )
                );
            }
        }
        return $response;
    }
}
