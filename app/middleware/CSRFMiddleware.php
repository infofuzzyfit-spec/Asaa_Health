<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\Session;

class CSRFMiddleware
{
    /**
     * Generate CSRF token
     */
    public function generateToken(): string
    {
        Session::start();
        
        if (!Session::has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        } else {
            $token = Session::get('csrf_token');
        }
        
        return $token;
    }

    /**
     * Verify CSRF token
     */
    public function verifyToken(string $token): bool
    {
        Session::start();
        
        if (!Session::has('csrf_token')) {
            return false;
        }
        
        return hash_equals(Session::get('csrf_token'), $token);
    }

    /**
     * CSRF protection middleware
     */
    public function handle(Request $request, Response $response, callable $next)
    {
        // Skip CSRF for GET requests and AJAX requests with proper headers
        if ($request->getMethod() === 'GET') {
            return $next($request, $response);
        }

        // Skip CSRF for AJAX requests with X-Requested-With header
        if ($request->isAjax() && $request->getHeader('X-Requested-With') === 'XMLHttpRequest') {
            return $next($request, $response);
        }

        // Get token from request
        $token = $request->get('csrf_token') ?? $request->getHeader('X-CSRF-Token');

        if (!$token) {
            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'CSRF token missing',
                    'error_code' => 'CSRF_TOKEN_MISSING'
                ], 400);
            }

            return $response->json([
                'success' => false,
                'message' => 'CSRF token missing'
            ], 400);
        }

        if (!$this->verifyToken($token)) {
            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Invalid CSRF token',
                    'error_code' => 'CSRF_TOKEN_INVALID'
                ], 400);
            }

            return $response->json([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ], 400);
        }

        return $next($request, $response);
    }

    /**
     * Get CSRF token for forms
     */
    public function getToken(): string
    {
        return $this->generateToken();
    }

    /**
     * Get CSRF meta tag for HTML head
     */
    public function getMetaTag(): string
    {
        $token = $this->getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }

    /**
     * Get CSRF input field for forms
     */
    public function getInputField(): string
    {
        $token = $this->getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Regenerate CSRF token
     */
    public function regenerateToken(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    /**
     * Clear CSRF token
     */
    public function clearToken(): void
    {
        Session::start();
        Session::remove('csrf_token');
    }
}