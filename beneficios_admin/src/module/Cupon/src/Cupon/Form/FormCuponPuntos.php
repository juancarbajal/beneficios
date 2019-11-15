<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/08/16
 * Time: 12:00 PM
 */

namespace Cupon\Form;

use Auth\Form\BaseForm;

class FormCuponPuntos extends BaseForm
{
    public function __construct($name = null, $empresa = null)
    {
        parent::__construct($name);
        $this->init($empresa);
    }

    public function init($empresa = null)
    {
        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
                'attributes' => array(
                    'class' => 'form-cupon',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CodigoCupon',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon coupon-code',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Titulo',
                'type' => 'textarea',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );


        $this->add(
            array(
                'name' => 'Campania',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'EstadoCampania',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );


        $this->add(
            array(
                'name' => 'comentario_uno',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'comentario_dos',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                ),

            )
        );

        if (!empty($empresa)) {
            $this->add(
                array(
                    'name' => 'EstadoCupon',
                    'type' => 'Text',
                    'attributes' => array(
                        'class' => 'form-control form-cupon',
                        'id' => 'estado-cupon',
                        'disabled' => 'disabled'
                    ),
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'EstadoCupon',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control',
                        'id' => 'estado-cupon',
                    ),
                    'options' => array(
                        'value_options' => array(
                            'Generado' => 'Descargado',
                            'Redimido' => 'Redimido',
                            'Por Pagar' => 'Por Pagar',
                            'Pagado' => 'Pagado',
                            'Stand By' => 'Stand By',
                            'Anulado' => 'Anulado',
                            'Caducado' => 'Caducado'
                        ),
                    ),
                )
            );
        }

        $this->add(
            array(
                'name' => 'PrecioCupon',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'PrecioBeneficio',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'EmpresaProv',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'PuntosUtilizados',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'PrecioFinal',
                'type' => 'Text',
              c
            )
        );

        $this->add(
            array(
                'name' => 'Comentarios',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CondicionesUso',
                'type' => 'textarea',
                'attributes' => array(
                    'class' => 'form-control textarea form-cupon',
                    'style' => "width: 100%; height: 200px",
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'FechaFinVigencia',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Submit',
                'name' => 'submit',
                'attributes' => array(
                    'value' => 'Redimir',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
