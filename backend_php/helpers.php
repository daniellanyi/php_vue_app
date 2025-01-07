<?php

use Framework\Session;

    /**
     * Get the base path
     * 
     * @param string $path
     * @return string
     */
    function basePath($path = '')
    {
        return __DIR__ . '/' . $path;
    }
    
    /**
     * Load a view
     * 
     * @param string $name
     * @return void
     * 
     */
    function loadView($name, $data = [])
    {
    $viewPath = basePath("App/views/{$name}.view.php");

    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View '{$name} not found!'";
    }
    }
    

    /**
     * Inspect a value(s)
     * 
     * @param mixed $value
     * @return void
     */
    function inspect($value)
    {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    }

    /**
     * Inspect a value(s) and die
     * 
     * @param mixed $value
     * @return void
     */
    function inspectAndDie($value)
    {
    echo '<pre>';
    die(var_dump($value));
    echo '</pre>';
    }

    /**
     *
     * @param string $name
     * @param array<string, string> $data
     * @return string
     */
    function loadEmailTemplate($name, $data)
    {
        $templatePath = basePath(("App/email_templates/{$name}.html.php"));
        $userLocale = Session::getInstance()->get('locale');
        if (!$userLocale) $userLocale = 'en';
        $translations = json_decode(file_get_contents(basePath(("App/email_templates/translations/{$userLocale}.json"))), true);
        if (file_exists($templatePath)) {
            extract($data);
            ob_start();
            require $templatePath;
            $htmlContent = ob_get_clean();
            return $htmlContent;
        } else {
            echo "Template not found";
        }
    }


    /**
     * Send an HTTP Exception
     * 
     * @param integer $statusCode HTTP status code
     * @param string $message Response message
     * @return void
     */
    function HTTPException($statusCode, $message, $error) {
        if (!headers_sent()) {
            http_response_code($statusCode);
        }
        if ($error) {
            error_log($error);
        }
        echo $message;
        exit(); 
    }




    /**
     * Send an HTTP response
     *
     * @param integer $statusCode HTTP status code
     * @param string $message Response message
     * @return void
     */
    function HTTPResponse(int $statusCode, string $message = '', array $headers = []) {
        if (!headers_sent()) {
            http_response_code($statusCode);
            foreach ($headers as $header)
            {
                header($header);
            }
        }
        if ($message) echo $message;
        exit(); 


    }

    /**
     * Send a JSON response
     * 
     * @param integer $statusCode
     * @param array<string, mixed> $data
     * @return void
     */
    function JSONResponse($statusCode, array $data, array $headers = []) {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json');
            foreach ($headers as $header)
            {
                if ($header === 'Content-Type: application/json') continue;
                header($header);
            }
        }
        echo json_encode($data);
        exit;
    }


    /**
     * Redirect to a given url
     * 
     * @param string $url
     * @return void
     */
    function redirect($url)
    {
    header("Location: {$url}");
    exit;
    }
?>

