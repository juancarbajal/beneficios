<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 01/09/15
 * Time: 07:58 PM
 */

namespace Paquete\Form;

use Auth\Form\BaseForm;


class BuscarPaqueteForm extends BaseForm
{
    public function __construct($name = 'paquete', $values = array() )
    {
        parent::__construct($name);
        $this->init($values);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed


        $this->add(
            array(
                'name' => 'NombrePais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'pais',
                ),
                'options' => array(
                    'value_options' => $value['pais'],
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'RazonSocial',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['emp'],
                    'empty_option' => 'Listar Todos',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'TipoPaquete',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => $value['tip'],
                    'empty_option' => 'Seleccione...',
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
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
        $this->setUseInputFilterDefaults(false);
    }

    /**
     * Set a single option for an element
     *
     * @param  string $key
     * @param  mixed $value
     * @return self
     */
    public function setOption($key, $value)
    {
        // TODO: Implement setOption() method.
    }
}
