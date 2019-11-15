<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 05/10/15
 * Time: 11:31 AM
 */

namespace Cliente\Model\Validator;

use Zend\Validator\AbstractValidator;

class ValidDateBirthday extends AbstractValidator
{
    const NOT_VALID = 'not_valid';

    protected $messageTemplates = array(
        self::NOT_VALID => "La fecha no es válida"
    );

    public function isValid($value)
    {
        $this->error(self::NOT_VALID);
        if (strtotime($value) > time()) {
            return false;
        }
        return true;
    }
}
