<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 24/01/16
 * Time: 11:34 PM
 */

namespace Oferta\Form;

use Auth\Form\BaseForm;
use Zend\Form\ElementInterface;

class RegistrarCodigosForm extends BaseForm
{
    public function __construct($name = 'registrar-codigos', $value = array())
    {
        parent::__construct($name);
        $this->init($value);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'Oferta',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'ofertas'
                ),
                'options' => array(
                    'value_options' => $value['ofe'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'file_csv',
                'attributes' => array(
                    'type' => 'file',
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\File\Size',
                        'options' => array(
                            'min' => 120,
                            'max' => 500000,
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => array(
                            'extension' => 'csv',
                        ),
                    ),
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
