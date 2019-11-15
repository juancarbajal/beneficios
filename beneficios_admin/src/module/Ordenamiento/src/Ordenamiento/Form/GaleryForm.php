<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 23/10/15
 * Time: 11:14 AM
 */

namespace Ordenamiento\Form;

use Auth\Form\BaseForm;


class GaleryForm extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct('galery');
        $this->init();
    }

    public function init()
    {


        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        //Galeria
        $this->add(
            array(
                'name' => 'Galeria',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'GaleriaUrl',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        $this->add(
            array(
                'name' => 'empresa_g',
                'type' => 'hidden',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'empresa_g'
                )
            )
        );
        ///submit
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Agregar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
