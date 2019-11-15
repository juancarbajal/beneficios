<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 01/07/16
 * Time: 04:12 PM
 */

namespace Premios\Form;

use Auth\Form\BaseForm;

class FormOfertaPremios extends BaseForm
{
    public function __construct($name = 'registrar', $value = array())
    {
        parent::__construct($name);
        $this->init($value);
    }

    public function init($value = array())
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'oferta_id'
                ),
            )
        );

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
                'name' => 'EmpresaProv',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa-prov'
                ),
                'options' => array(
                    'value_options' => $value['empprov'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'EmpresaCli',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa-cli'
                ),
                'options' => array(
                    'value_options' => $value['empcli'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CampaniaPremios',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'SegmentoPremios',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control searchable',
                    'multiple' => 'multiple',
                    'id' => 'segmentos'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Nombre',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Titulo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'TituloCorto',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Imagen',
                'attributes' => array(
                    'id' => 'input-file',
                    'type' => 'file',
                    'class' => 'form-control btn btn-flat btn-upload',
                    'maxLength' => 255,
                    "style" => "text-align: center; vertical-align: middle;"
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CondicionesUso',
                'type' => 'Textarea',
                'attributes' => array(
                    'class' => 'form-control textarea',
                    'style' => "width: 100%; height: 200px"
                )
            )
        );

        $this->add(
            array(
                'name' => 'Direccion',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Telefono',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 50
                )
            )
        );

        $this->add(
            array(
                'name' => 'Correo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Premium',
                'type' => 'Checkbox',
                'options' => array(
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'value' => 0,
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'name' => 'Rubro',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => $value['rubro'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        //************* Precio *************//

        $this->add(
            array(
                'name' => 'TipoPrecio',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'tipoPrecio',
                ),
                'options' => array(
                    'value_options' => array(
                        'Split' => 'Split',
                        'Unico' => 'Único',
                    ),
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'PrecioVentaPublico',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'precio-venta',
                )
            )
        );

        $this->add(
            array(
                'name' => 'PrecioBeneficio',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'precio-beneficio'
                )
            )
        );

        //************* Ubicacion *************//

        $this->add(
            array(
                'name' => 'Pais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'pais'
                ),
                'options' => array(
                    'value_options' => $value['pais'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Distrito',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\MultiCheckbox',
                'name' => 'Departamento',
                'options' => array(
                    'use_hidden_element' => true,
                    'value_options' => $value['depas']
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaVigencia',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );

        $this->add(
            array(
                'name' => 'DescargaMaxima',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
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
                    'value_options' => array(
                        'Borrador' => 'Borrador',
                        'Publicado' => 'Publicado',
                        'Caducado' => 'Caducado'
                    ),
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Stock',
                'type' => 'Number',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'stock',
                    'step' => '1',
                )
            )
        );

        $this->add(
            array(
                'name' => 'copy',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Grabar de nuevo',
                    'id' => 'copyButton',
                    'class' => 'btn btn-default',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Registrar',
                    'id' => 'addButton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
