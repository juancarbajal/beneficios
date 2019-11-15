<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 17/08/16
 * Time: 04:54 PM
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;

class FormSegmentoP extends BaseForm
{
    public function __construct($name = 'editar-segmento')
    {
        parent::__construct('editar-segmento');
        $this->init();
    }

    public function init()
    {
        $this->setAttribute('method', 'post');

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
                'name' => 'NombreSegmento',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'CantidadPuntos',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'CantidadPersonas',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Textarea',
                'name' => 'Comentario',
                'attributes' => array(
                    'class' => 'form-control textarea',
                    'style' => "width: 100%; height: 200px"
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Editar',
                    'id' => 'submitSave',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
