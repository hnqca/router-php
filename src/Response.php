<?php

namespace Hnqca\Router;

class Response
{
    public function redirect(string $endpoint, int $code = 0)
    {
        http_response_code($code);

        header("location: {$endpoint}");
        die;
    }

    public function send(int $status, mixed $data)
    {
        http_response_code($status);

        if (!is_array($data)) {
            die($data);
        }

        return $this->json($status, $data);
    }

    public function json(int $status, array $data)
    {
        http_response_code($status);
        header("Content-Type: application/json");

        $data = json_encode($data, JSON_PRETTY_PRINT);
        die($data);
    }
}