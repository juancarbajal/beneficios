<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 29/08/16
 * Time: 12:38 PM
 */

namespace Cupon\Form;

use Auth\Form\BaseForm;

class BusquedaCuponPremios extends BaseForm
{
    public function __construct($name = 'buscar', $value = array(), $empresa = "")
    {
        parent::__construct($name);
        $this->init($value, $empresa);
    }

    public function init($value = array(), $empresa = "")
    {
        if (!empty($empresa)) {
            $this->add(
                array(
                    'name' => 'Empresa',
                    'type' => 'Hidden',
                    'attributes' => array(
                        'id' => 'empresa-prov',
                    ),
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'Empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                        'id' => 'empresa-prov'
                    ),
                    'options' => array(
                        'value_options' => $value['prov'],
                        'empty_option' => 'Seleccione...'
                    ),
                )
            );
        }

        $this->add(
            array(
                'name' => 'Campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Oferta',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'ofertas'
                )
            )
        );

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
                        'Anulado' => 'Anulado',
                        'Stand By' => 'Reclamo',
                        'Caducado' => 'Caducado'
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaInicio',
                'options' => array(
                    'format' => 'Y-m-d',
                ),
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaFin',
                'options' => array(
                    'format' => 'Y-m-d',
                ),
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );

        $this->add(
            array(
                'name' => 'Cupon',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
