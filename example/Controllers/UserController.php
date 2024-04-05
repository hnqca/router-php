<?php

namespace Controllers;

use Hnqca\Router\Request;
use Hnqca\Router\Response;

class UserController
{
    /**
     * @GET '/users'
     */
    public function index(Request $req, Response $res)
    {
        $users = [
            [
                'id'   => 1,
                'name' => "John Doe"
            ],
            // ...
        ];

        return $res->json(200, ['users' => $users]);
    }

    /**
     * @GET '/users/{id}'
     */
    public function show(Request $req, Response $res)
    {
        $userId = $req->getParams()->id;

        if (!filter_var($userId, FILTER_VALIDATE_INT)) {
            return $res->send(400, "It is expected to receive a value of integer type.");
        }
        
        return $res->send(200, "Viewing user data with ID: {$userId}");
    }

    /**
     * @POST '/users'
     */
    public function create(Request $req, Response $res)
    {
        // Requires all data specified in the array to exist in the request
        $req->checkDataRequest(requiredFields: ['name', 'email']);

        // Get the data sent in the request
        $data = $req->getData();

        if (!preg_match('/^[a-zA-Z\x{00C0}-\x{00FF} ]+$/u', $data->name)) {
            return $res->json(400, [
                'error' => "Invalid name"
            ]);
        }

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            return $res->json(400, [
                'error' => "Invalid email address"
            ]);
        }

        return $res->json(201, [
            'message' => "Registering a new user",
            'method'  => $req->getRequestMethod(),
            'data'    => $data
        ]);
    }

    /**
     * @PUT|@PATCH '/users/{id}'
     */
    public function update(Request $req, Response $res)
    {
        $userId = $req->getParams()->id;

        return $res->json(200, [
            'message' => "Updating the user with ID: {$userId}",
            'method'  => $req->getRequestMethod(),
            'data'    => $req->getData()
        ]);
    }

    /**
     * @DELETE '/users/{id}'
     */
    public function delete(Request $req, Response $res)
    {
        $userId = $req->getParams()->id;

        return $res->json(200, [
            'message' => "Deleting the user with ID: {$userId}",
            'method'  => $req->getRequestMethod()
        ]);
    }
}