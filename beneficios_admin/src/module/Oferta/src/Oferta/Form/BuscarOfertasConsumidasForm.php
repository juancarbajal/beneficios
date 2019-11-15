<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/11/15
 * Time: 10:51 AM
 */

namespace Oferta\Form;

use Auth\Form\BaseForm;
use Zend\Form\Form;

class BuscarOfertasConsumidasForm extends BaseForm
{
    public function __construct($nombre = 'paquete-ofertas', $values = array())
    {
        parent::__construct($nombre);
        $this->init($values);
    }

    public function init($values = array())
    {
        // we want to ignore the name passed

        $this->add(
            array(
                'name' => 'Titulo',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $values['ofe'],
                    'empty_option' => 'Listar Todos',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Estado',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => $values['est'],
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
