<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/01/16
 * Time: 11:34 PM
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;
use Zend\Form\ElementInterface;

class DeliveryForm extends BaseForm
{
    public function __construct($name = 'registrar-delivery', $value = array())
    {
        parent::__construct($name);
        $this->init($value);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed

        $this->setAttribute('method', 'post');

        $this->add(
            array(
                'name' => 'Oferta',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'ofertas'
                ),
                'options' => array(
                    'value_options' => $value,
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Condiciones',
                'type' => 'Textarea',
                'attributes' => array(
                    'class' => 'form-control textarea',
                    'id' => 'Condiciones',
                    'style' => "width: 100%; height: 200px;"
                )
            )
        );

        $this->add(
            array(
                'name' => 'CondicionesTexto',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'CondicionesTexto',
                )
            )
        );

        $this->add(
            array(
                'name' => 'CorreoContactoDelivery',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'email',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'CondicionesEstado',
                'attributes' => array(
                    'id' => 'CondicionesEstado',
                ),
                'options' => array(
                    'use_hidden_element' => true,
                    'checked_value' => '1',
                    'unchecked_value' => '0'
                )
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
