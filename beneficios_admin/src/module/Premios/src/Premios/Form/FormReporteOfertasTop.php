<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/09/16
 * Time: 11:00
 */

namespace Premios\Form;

use Auth\Form\BaseForm;

class FormReporteOfertasTop extends BaseForm
{
    public function __construct($name = 'reporte', $value = array(), $type = null)
    {
        parent::__construct($name);
        $this->init($value, $type);
    }

    public function init($value = array(), $type = null)
    {
        $this->setAttribute('method', 'post');

        if ($type == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'empresa',
                    'attributes' => array(
                        'id' => 'empresas',
                        'value' => $value
                    )
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                        'id' => 'empresas'
                    ),
                    'options' => array(
                        'value_options' => $value['emp'],
                        'empty_option' => 'Seleccione...'
                    )
                )
            );
        }

        $this->add(
            array(
                'name' => 'campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                )
            )
        );

        $this->add(
            array(
                'name' => 'segmento',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'segmentos'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                )
            )
        );

        $this->add(
            array(
                'name' => 'estado',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'segmentos'
                ),
                'options' => array(
                    'empty_option' => 'Todos',
                    'value_options' => array(
                        'Publicado' => 'Publicado',
                        'Borrador' => 'Borrador',
                        'Caducado' => 'Caducado',
                    ),
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Exportar',
                    'id' => 'addButton',
                    'class' => 'btn btn-primary',
                )
            )
        );
    }
}