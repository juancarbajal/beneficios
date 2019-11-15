<?php

namespace Auth\Service;

use Zend\Session\SessionManager;
use Zend\Validator\Csrf as Token;

class Csrf
{
    public function verifyToken($token_temp)
    {
        $token = new Token();
        $state = false;

        if ($token->isValid($token_temp)) {
            $state = true;
        }

        return $state;
    }

    public function cleanCsrf()
    {
        $manager = new SessionManager();
        $storage = $manager->getStorage();
        $storage->clear('Zend_Validator_Csrf_salt_csrf');
    }
}
