<?php
    namespace Framework;
    use BadRequestHTTPException;
    use Exception;
    use HTTPException;
    use InvalidArgumentException;
    use NotFoundHTTPException;
    use Request;
    use ReflectionMethod;
    use Throwable;
    use TypeError;

    class Router
    {

        
        protected $routes =[];

        protected $routers =[];

        public function registerRoute(string $requestMethod, string $uri, callable $controllerMethod, array $middleware =[]) {
            if (is_callable($controllerMethod)) {
                $reflection = new ReflectionMethod($controllerMethod[0], $controllerMethod[1]);
            }
            $parameters = [];
            foreach ($reflection->getParameters() as $parameter) {
                
                $parameters[] = [
                    'name'=> $parameter->getName(),
                    'isOptional' => $parameter->isOptional(),
                    'type'=>$parameter->hasType() ?  $parameter->getType()->getName() : null
                ];
            }
            for ($i = 0; $i < count($middleware); $i++) {
                if (!is_callable($middleware[$i])) throw new InvalidArgumentException("Middleware function at key $i should be a callable not ". gettype($middleware[$i]));
            }


            $this->routes[] = [
                 
                    'uri'=>$uri,
                    'requestMethod'=> $requestMethod,
                    'controllerMethod'=>$controllerMethod,
                    'parameters'=> $parameters,
                    'middleware'=>$middleware
                
            ];
        }

        public function use(string $uri, Router $router, array $middleware = []) {


            for ($i = 0; $i < count($middleware); $i++) {
                if (!is_callable($middleware[$i])) throw new InvalidArgumentException("Middleware function at key $i should be a callable not ". gettype($middleware[$i]));
            }
            
          
            $this->routers[] = [
                "router" => $router,
                "uri" => $uri,
                "middleware"=>$middleware
            ];
        }


         /**
         * Add a GET route
         * 
         * @param string $uri
         * @param callable $action
         * @param array $middleware
         * @return void
         */
        public function get(string $uri, callable $action, array $middleware = [])
        {
            $this->registerRoute('GET', $uri, $action, $middleware);
        }

        /**
         * Add a POST route
         * 
         * @param string $uri
         * @param callable $action
         * @param array $middleware
         * 
         * @return void
         */
        public function post(string $uri, callable $action, array $middleware = [])
        {
            $this->registerRoute('POST', $uri, $action, $middleware);
        }

         /**
         * Add a PUT route
         * 
         * @param string $uri
         * @param callable $action
         * @param array $middleware
         * 
         * @return void
         */
        public function put(string $uri, callable $action, array $middleware = [])
        {
            $this->registerRoute('PUT', $uri, $action, $middleware);
        }

        /**
         * Add a DELETE route
         * 
         * @param string $uri
         * @param callable $action
         * @param array $middleware
         * 
         * @return void
         */
        public function delete(string $uri, callable $action, array $middleware = [])
        {
            $this->registerRoute('DELETE', $uri, $action, $middleware);
        }

        /**
         * Route the request
         * 
         * @param string $uri
         * @return void
         */
        public function route($uri)
        {
            $query = parse_url($uri, PHP_URL_QUERY);
            $uri = parse_url($uri, PHP_URL_PATH);
            
            $queryParams = [];
            if ($query) {
                parse_str($query, $queryParams);
            }

            $request = new Request();

            $uriSegments = explode('/', ltrim($uri, '/'));
            
            $currentRouter = $this;
            
            while (true) {
                $match = false;
                foreach($currentRouter->routers as $router) {
                    $match = true;
                    $routeSegments = explode('/',trim($router['uri'], '/'));
                    
                    for ($i=0; $i < count($routeSegments); $i++) {
                        if ($uriSegments[$i] !== $routeSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                            $match = false;
                            break;
                        };
                        if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                            $request->args[$matches[1]] = $uriSegments[$i];
                        }
                    }
                    if ($match === true) {
                        foreach($router['middleware'] as $middleware) {
                            try {
                                $request->args = call_user_func($middleware, $request);
                            } catch (Exception $e) {
                                if ($e instanceof HTTPException) {
                                    throw $e;
                                }
                            }
                        }
                        $currentRouter = $router['router'];
                        array_splice($uriSegments, 0, count($routeSegments));
                        break;
                    };
                };
                
                if ($match === true) continue;
                $match = true;
                foreach ($currentRouter->routes as $route) {
                    $routeSegments = explode('/',trim($route['uri'], '/'));
                    if (count($uriSegments) === count($routeSegments))
                    {
                        $match = true;
                        for ($i = 0; $i < count($uriSegments); $i++) {
                            if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                                $match = false;
                                break;
                            };

                            if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                                $request->args[$matches[1]] = $uriSegments[$i];
                            }
                        }

                        if ($match) {
                            if ($route['requestMethod'] !== strtoupper($request->method)) throw new BadRequestHTTPException('Method not allowed');
                            $params = $route['parameters'];
                            foreach ($params as $param) {
                                $paramName = $param['name'];
                                $paramType = $param['type'];
                                if (isset($request->args[$paramName])) {
                                    if (!gettype($request->args[$paramName]) === $paramType) {
                                        $cast = settype($request->args[$paramName], $paramType);
                                        if ($cast === false) throw new BadRequestHTTPException("Variable $paramName expects a $paramType");
                                    }
                                    continue;
                                }
                                if (is_subclass_of($paramType, 'BaseBodyValidation')) {
                                    $validation = $paramType;
                                    $json = file_get_contents('php://input');
                                    $data = json_decode($json, true);
                                    try {
                                        $requestBody = new $validation($data);
                                        
                                    } catch (Exception $e) {
                                        if ($e instanceof InvalidArgumentException) {
                                            throw new BadRequestHTTPException($e->getMessage());
                                        }
                                    }
                                    
                                    $request->args[$paramName] = $requestBody;
                                    break;
                                }
                                if (isset($queryParams[$paramName])) {
                                    if (!(gettype($queryParams[$paramName]) === $paramType)) {
                                        $cast = settype($queryParamValue, $paramType);
                                        if ($cast === false) {
                                            if ($param['isOptional']) continue;
                                            throw new BadRequestHTTPException("Variable $paramName expects a $paramType");
                                        }   
                                    }
                                    $request->args[$paramName] = $queryParams[$paramName];
                                } else if (!isset($request->args[$paramName]) && !$param['isOptional']) {
                                    throw new BadRequestHTTPException("Missing required query parameter $paramName", "Query parameter $paramName missing");
                                }
                            }
                            foreach($route['middleware'] as $middleware) {
                                try {
                                    $request->args = call_user_func($middleware, $request);
                                } catch (Exception $e) {
                                    if ($e instanceof HTTPException) throw $e;
                                }
                            }
                            $method = $route['controllerMethod'];
                            try {
                                call_user_func($method, ...$request->args);
                            } catch (Throwable $e) {
                                if ($e instanceof TypeError) {
                                    error_log($e);
                                    throw new BadRequestHTTPException("Wrong data type");
                                } else if ($e instanceof HTTPException) {
                                    throw $e;
                                } 
                            }
                            
                            return;
                        }
                    }
                }
                throw new NotFoundHTTPException('Not found');
            }
        }
    }

    

?>