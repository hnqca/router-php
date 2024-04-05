<?php

namespace Controllers;

use Hnqca\Router\Request;
use Hnqca\Router\Response;

class HomeController
{
    /**
     * @GET '/'
     */
    public function index(Request $req, Response $res)
    {
        return $res->send(200, "Hello World!");
    }
}