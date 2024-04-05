<?php

namespace Middlewares;

use Hnqca\Router\Response;

class AuthMiddleware
{
    const ADMIN_ACCESS = 2;

    public function onlyConnected()
    {
        if (!isset($_SESSION['userId'])) {
            // return (new Response)->redirect('/login');
            return (new Response)->send(401, "Only logged-in users can access this route.");
        }

        return true;
    }

    public function onlyAdmin()
    {
        $this->onlyConnected();

        if ($_SESSION['access'] < self::ADMIN_ACCESS) {
            return (new Response)->send(403, "Only administrators can access this route.");
        }

        return true;
    }
}
