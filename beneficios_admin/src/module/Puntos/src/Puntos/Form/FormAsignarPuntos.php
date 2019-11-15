<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 11/07/16
 * Time: 12:20 PM
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;

class FormAsignarPuntos extends BaseForm
{
    public function __construct($name = 'upload')
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'action',
                'attributes' => array(
                    'type' => 'hidden',
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
                    'value' => 'Grabar',
                    'id' => 'submitButton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
