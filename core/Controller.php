<?php

/**
 * Base Controller Class
 * All controllers extend this class
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/Session.php';

abstract class Controller
{
    protected $db;
    protected $request;
    protected $response;
    protected $session;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
    }

    protected function view($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new Exception("View not found: {$view}");
        }
    }

    protected function json($data, $statusCode = 200)
    {
        $this->response->setStatusCode($statusCode);
        $this->response->setHeader('Content-Type', 'application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    protected function isPost()
    {
        return $this->request->getMethod() === 'POST';
    }

    protected function isGet()
    {
        return $this->request->getMethod() === 'GET';
    }

    protected function isAjax()
    {
        return $this->request->isAjax();
    }

    protected function validateCsrf()
    {
        $token = $this->request->post('csrf_token');
        if (!$token || $token !== $this->session->get('csrf_token')) {
            throw new Exception('CSRF token validation failed');
        }
    }

    protected function generateCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set('csrf_token', $token);
        return $token;
    }
}
