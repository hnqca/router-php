<?php

namespace Controllers;

use Hnqca\Router\Request;
use Hnqca\Router\Response;

class AdminController
{
    /**
     * @GET '/admin'
     */
    public function index(Request $req, Response $res)
    {
        return $res->send(200, "displaying administrative access information...");
    }
}