<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/09/15
 * Time: 04:09 PM
 */

namespace Oferta\Form;

use Auth\Form\BaseForm;

class BuscarEmpresaForm extends BaseForm
{
    public function __construct($name = 'buscar',  $values = array())
    {
        parent::__construct( $name );
        $this->init( $values );
    }

    public function init( $values = array())
    {
        // we want to ignore the name passed

        $this->add(
            array(
                'name' => 'Empresa',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $values['emp'],
                    'empty_option' => 'Listar Todos'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Oferta',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'oferta',
                ),
                'options' => array(
                    'value_options' => $values['ofe'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Ruc',
                'type' => 'Text',
                'attributes' => array(
                    'maxLength' => 11,
                    'class' => 'form-control',
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
