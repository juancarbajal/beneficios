<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 17/06/16
 * Time: 04:10 PM
 */

namespace Premios\Form;

use Auth\Form\BaseForm;

class FormCampaniasPR extends BaseForm
{
    public function __construct($name = 'asignar', $value = array())
    {
        parent::__construct('asignar');
        $this->init($value);
    }

    public function init($value = array())
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
                'name' => 'Empresa',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa-prov'
                ),
                'options' => array(
                    'value_options' => $value['emp'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'NombreCampania',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaCampania',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );
        
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'VigenciaInicio',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'VigenciaFin',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'PresupuestoNegociado',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'PresupuestoAsignado',
                'attributes' => array(
                    'class' => 'form-control',
                    'disabled' => 'disabled'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Number',
                'name' => 'ParametroAlerta',
                'attributes' => array(
                    'class' => 'form-control',
                    'step' => '1',
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
                'type' => 'Zend\Form\Element\Text',
                'name' => 'Relacionado',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'TipoSegmento',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'tipo_segmento'
            ),
            'options' => array(
                'value_options' => array(
                    'Clasico' => 'Clásico',
                    'Personalizado' => 'Personalizado',
                ),
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'EstadoCampania',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'estado'
            ),
            'options' => array(
                'value_options' => array(
                    'Borrador' => 'Borrador',
                    'Publicado' => 'Publicado',
                    'Caducado' => 'Caducado',
                ),
            )
        ));

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Guardar',
                    'id' => 'submitSave',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
