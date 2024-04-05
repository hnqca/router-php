<?php

namespace Hnqca\Router;

class Request
{
    private $params;
    private ?object $data    = null;
    private ?object $headers = null;

    public function __construct(array $parameters = [])
    {
        if ($parameters) {
            $this->setParams($parameters);
        }

        $this->setData();
        $this->setHeaders();
    }

    public function getData(): ?object
    {
        return $this->data;
    }

    public function getParams(): ?object
    {
        return $this->params;
    }

    public function getQuery(): ?object
    {
        return (object) $_GET;
    }

    public function getHeaders(): ?object
    {
        return $this->headers;
    }

    public function getRequestMethod(): ?string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getAuthorizationBearer(): ?string
    {
        $authorization = $this->getHeaders()->Authorization ?? null;

        if (!$authorization) {
            return null;
        }

        return explode('Bearer ', $authorization)[1] ?? null;
    }

    public function checkDataRequest(array $requiredFields): ?object
    {
        $data = $this->getData();

        if (!$data) {
            return (new Response)->json(400, [
                'error'           => "no data sent in the request",
                'required_fields' => $requiredFields
            ]);
        }

        foreach ($requiredFields as $field) {
            if (!isset($data->$field)) {
                return (new Response)->json(400, [
                    'error'           => "send all necessary data",
                    'required_fields' => $requiredFields
                ]);
                break;
            }
        }

        return $data;
    }

    private function setParams(array|object $parameters): void
    {
        $this->params = (object) $parameters;
    }

    private function setHeaders(): void
    {
        $this->headers = (object) getallheaders();
    }

    private function getContentType(): ?string
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            return null;
        }

        return $_SERVER['CONTENT_TYPE'];
    }

    private function setData(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            return;
        }

        $contentType = $this->getContentType();

        if ($contentType === 'application/json') {
            $this->data = json_decode(file_get_contents('php://input'));
            return;
        }

        if ($contentType === 'application/x-www-form-urlencoded') {
            $this->data = (object) $_POST;
            return;
        }
    }

}