<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 17/12/15
 * Time: 12:59 PM
 */

return array(
    'preguntas' => array(
        'pregunta_01' => array(
            'titulo' => '¿Cuál es su Nombre?',
            'tipo_campo' => "text"
        ),
        'pregunta_02' => array(
            'titulo' => '¿Cuáles son sus Apellidos?',
            'tipo_campo' => "text"
        ),
        'pregunta_03' => array(
            'titulo' => '¿Cuál es su año de nacimiento?',
            'tipo_campo' => "date"
        ),
        'pregunta_04' => array(
            'titulo' => '¿Cuál es su género?',
            'tipo_campo' => "combo",
            'value_combo' => array("Masculino", "Femenino")
        ),
        'pregunta_05' => array(
            'titulo' => '¿Cuál es su estado civil?',
            'tipo_campo' => "combo",
            'value_combo' => array('Soltero','Casado','Viudo','Divorciado')
        ),
        'pregunta_06' => array(
            'titulo' => '¿En qué distrito vives actualmente?',
            'tipo_campo' => "text"
        ),
        'pregunta_07' => array(
            'titulo' => '¿En qué distrito se encuentra su trabajo?',
            'tipo_campo' => "text"
        ),
        'pregunta_08' => array(
            'titulo' => '¿Cuántos hijos tiene usted?',
            'tipo_campo' => "numb"
        ),
        'pregunta_09' => array(
            'titulo' => '¿Cuál es tu número de celular?',
            'tipo_campo' => "textnumb"
        ),
        'pregunta_10' => array(
            'titulo' => '¿Cuál es tu nivel de estudios?',
            'tipo_campo' => "combo",
            'value_combo' => array('Secundaria','Superior','Post grado')
        ),
    )
);
