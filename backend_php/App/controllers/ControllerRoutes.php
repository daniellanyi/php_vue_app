<?php
namespace App\Controllers;

use BadRequestHTTPException;
use BaseBodyValidation;
use BaseRoute;
use Framework\Session;

class LocaleSyncBody extends BaseBodyValidation {
    public string $locale;
}

class ControllerRoutes extends BaseRoute {

    private static $instance = null;

    protected $session;

    public static function getRouter() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->router;
    }


    public function __construct()
    {
        $this->session = Session::getInstance();
        parent::__construct();
        $this->init();
    }

    private function init() {
        $this->router->use('/auth', AuthController::getRouter());
        $this->router->post('/locale', [$this, 'userLocale']);
    }

    public function userLocale(LocaleSyncBody $localeBody) {
        $supportedLocales = ['en', 'fr', 'de'];
        if (!in_array($localeBody->locale, $supportedLocales)) throw new BadRequestHTTPException;
        $this->session->set('locale', $localeBody->locale);
    }
}


?>