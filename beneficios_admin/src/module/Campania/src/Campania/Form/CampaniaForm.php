<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 10:55 PM
 */

namespace Campania\Form;

use Auth\Form\BaseForm;


class CampaniaForm extends BaseForm
{

    public function __construct($pais = array(), $name = null)
    {
        parent::__construct('campania');
        $this->init($pais);
    }

    public function init($pais = array())
    {
        // we want to ignore the name passed

        $this->add(
            array(
                'name' => 'id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Nombre',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control md',
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NombrePais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'pais'
                ),
                'options' => array(
                    'value_options' => $pais,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Registrar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Descripcion',
                'attributes' => array(
                    'type' => 'textarea',
                    'class' => 'form-control md',
                    'rows' => 10,
                    'cols' => 10,
                    'maxLength' => 255
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
