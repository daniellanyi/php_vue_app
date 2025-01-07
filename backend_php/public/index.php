<?php
    require __DIR__ . '/../vendor/autoload.php';
    require '../helpers.php';
    require '../exceptions.php';
    require '../helperClasses.php';

    use Framework\Session;
    use App\Server;

    Session::getInstance()->start();
    
    $router = Server::getRouter();

    $uri = $_SERVER['REQUEST_URI'];
    
    $router->route($uri); 
?>

