<?php
namespace App;
use App\Controllers\ControllerRoutes;
use Framework\Middleware\CSRF;
use Framework\Router;
use Framework\Session;
use BaseRoute;

class Server extends BaseRoute {
    

    private static $instance = null;

    public static function getRouter() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->router;
    }


    protected $session;

    public function __construct()
    {
        $this->session = Session::getInstance();
        parent::__construct();
        $this->init();
    }


    private function init() {
        $this->router->use('/api', ControllerRoutes::getRouter(), [[new CSRF(), 'checkToken']]);
        $this->router->get('/csrf', [$this, 'getCSRFToken']);
    }

    public function getCSRFToken() {
        $CSRFToken = bin2hex(random_bytes(32));
        $this->session->set('CSRFToken', $CSRFToken);
        JSONResponse(200, [
            'CSRFToken'=> $CSRFToken
        ]);
    }
}

?>