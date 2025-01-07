<?php
namespace Framework\Middleware;
use Framework\Session;
use Request;
use ForbiddenHTTPException;

class CSRF {

    protected $session;

    public function __construct()
    {
        $this->session = Session::getInstance();
    }

    public function checkToken(Request $request) {
        $headers = $request->headers;
        $CSRFToken = isset($headers['CSRF-TOKEN']) ? $headers['CSRF-TOKEN'] : null;
        if ($request->method === 'POST') {
            if ($CSRFToken === null) throw new ForbiddenHTTPException('No CSRF token');
            if (!hash_equals($CSRFToken, $this->session->get('CSRFToken'))) {
                throw new ForbiddenHTTPException('Invalid CSRF token');
            }
        
        }

        return $request->args;
    }
}

?>