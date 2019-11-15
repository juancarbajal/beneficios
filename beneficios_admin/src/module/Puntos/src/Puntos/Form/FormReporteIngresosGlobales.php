<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/09/16
 * Time: 11:00
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;

class FormReporteIngresosGlobales extends BaseForm
{
    public function __construct($name = 'reporte', $value = array(), $type = null)
    {
        parent::__construct($name);
        $this->init($value, $type);
    }

    public function init($value = array(), $type = null)
    {
        $this->setAttribute('method', 'post');

        if ($type == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'empresa',
                    'attributes' => array(
                        'id' => 'empresas',
                        'value' => $value
                    )
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                        'id' => 'empresas'
                    ),
                    'options' => array(
                        'value_options' => $value['emp'],
                        'empty_option' => 'Seleccione...'
                    )
                )
            );
        }

        $this->add(
            array(
                'name' => 'campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                )
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
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Exportar',
                    'id' => 'addButton',
                    'class' => 'btn btn-primary',
                )
            )
        );
    }
}