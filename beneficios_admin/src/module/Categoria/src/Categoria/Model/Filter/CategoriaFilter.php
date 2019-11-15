<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 10/09/15
 * Time: 04:48 PM
 */

namespace Categoria\Model\Filter;


use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class CategoriaFilter
{

    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter($pais = array())
    {
        if ($pais == array()) {
            $pais_id['1'] = '1';
        }
        foreach ($pais as $key => $dato):
            $pais_id[] = $key;
        endforeach;
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //pais
            $inputFilter->add(
                array(
                    'name' => 'NombrePais',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo País no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $pais_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un País',
                                )
                            )
                        )
                    ),
                )
            );
            //Nombre
            $inputFilter->add(
                array(
                    'name' => 'Nombre',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombre no puede quedar vacío.'
                            )
                        )
                    )
                )
            );
            //Descripcion
            $inputFilter->add(
                array(
                    'name' => 'Descripcion',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Descripción no puede quedar vacío.'
                            )
                        ),
                    ),
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
