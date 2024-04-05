<?php

namespace Controllers;

use Hnqca\Router\Request;
use Hnqca\Router\Response;

class ProductController
{
    /**
     * @GET '/products'
     */
    public function index(Request $req, Response $res)
    {
        $filter = $req->getQuery()->filter ?? null;

        if ($filter) {
            return $res->send(200, "Filtering products by {$filter}"); 
        }

        return $res->send(200, "Listing all products..."); 
    }
}