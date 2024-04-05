<?php

namespace Controllers;

use Hnqca\Router\Response;

class ErrorController
{
    private function getMessage($code)
    {
        $messages = [
            400 => "Bad Request",
            404 => "Not Found",
            405 => "Method Not Allowed",
            501 => "Not Implemented"
        ];

        return $messages[$code] ?? "unknow";
    }

    public function show($code)
    {
        if (!filter_var($code, FILTER_VALIDATE_INT)) {
            die;
        }

        $context = $this->getMessage($code);

        return (new Response)->send($code, "Routing error: {$code} - {$context}");
    }
}