<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/11/15
 * Time: 12:38 PM
 */

namespace Paquete\Form;

use Auth\Form\BaseForm;


class BuscarPaquetesComprados extends BaseForm
{
    public function __construct($values)
    {
        parent::__construct('paquete');
        $this->init($values);
    }

    public function init($values = array())
    {
        // we want to ignore the name passed

        parent::__construct('paquetes-comprados');

        $this->add(
            array(
                'name' => 'Paquete',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $values,
                    'empty_option' => 'Listar Todos',
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'Factura',
                'attributes' => array(
                    'class' => 'form-control',
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
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
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
