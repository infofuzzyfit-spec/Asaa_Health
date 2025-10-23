<?php

/**
 * Response Class
 * HTTP response handling
 */

class Response
{
    private $statusCode = 200;
    private $headers = [];
    private $body = '';

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        header("{$name}: {$value}");
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        $this->setBody(json_encode($data));
        return $this;
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        return $this;
    }

    public function send()
    {
        echo $this->body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }
}
