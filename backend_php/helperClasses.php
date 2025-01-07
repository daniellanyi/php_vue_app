<?php



use Framework\Router;

class BaseRoute
{

    protected $router;

    public function __construct()
    {
        $this->router = new Router();
    }
}


class Request
    {
        public string $method;
        public array $headers;
        public array $cookies;
        public array $args = [];

        public function __construct()
        {
            $this->init();
        }

        private function init() {
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->cookies = $_COOKIE;
            $this->getAllHeaders();
        }

        private function getAllHeaders() {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') 
                {
                    $this->headers[str_replace('_', '-', substr($name, 5))] = $value;
                }
            }
        }
    }


class Validators {

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
        
    }

}

abstract class BaseBodyValidation
{

    protected array $validators;

    public function __construct(array $data) {

        $this->validate($data);
    }
   

    public function toArray() {
        $reflection = new ReflectionClass($this);
        $fields = $reflection->getProperties();
        $returnArray = [];
        foreach ($fields as $field) {
            $fieldName = $field->getName();
            if ($fieldName === 'validators') continue;
            $returnArray[$fieldName] = $field->getValue($this);
        }
        return $returnArray;
    }

    protected function registerValidator(string $fieldName, callable $validator, string $typeName, bool $pre = false, string | null $errorMessage = null) {
        $this->validators[$fieldName] = [
            'method'=> $validator,
            'pre' => $pre,
            'typeName' => $typeName,
            'errorMessage'=>$errorMessage
        ];
    }

    private function validate(array $data): void {
        $reflection = new ReflectionClass($this);
        $requiredFields = $reflection->getProperties();
        if (count($data) > count($requiredFields)) {
            throw new InvalidArgumentException("Too many fields in request body. Expected: " . count($requiredFields) . ". Got: " . count($data));
        }
        foreach ($requiredFields as $field) {
            $fieldName = $field->getName();
            if ($fieldName === 'validators') continue;
            $fieldType = $field->getType()->getName();
            if (!array_key_exists($fieldName, $data)) {
                if (!$field->hasDefaultValue()){
                     throw new InvalidArgumentException("Missing required field: $fieldName");
                } else {
                    continue;
                }
            }
            $value = $data[$fieldName];
            
            if (is_subclass_of($fieldType, 'BaseBodyValidation')) {
                $value = new $fieldType($value);
            }
            if (isset($this->validators, $fieldName)) {
                
                $fieldType = $this->validators[$fieldName]['typeName'];
                if ($this->validators[$fieldName]['pre'] === true) {
                    $value = $this->validators[$fieldName]['method']($value);
                    if ($value === false) {
                        $errorMessage = $this->validators[$fieldName]['errorMessage'];
                        throw new  InvalidArgumentException($errorMessage ?? "Field $fieldName must be of type $fieldType");
                    }
                }
            }
            try {
                $field->setAccessible(true);
                $field->setValue($this, $value);
                
            } catch (Error $e) {
                error_log($e);
                if ($e instanceof TypeError) {
                    throw new InvalidArgumentException("Field $fieldName must be of type $fieldType");
                }
            } 
            if (isset($this->validators, $fieldName)) {
                if ($this->validators[$fieldName]['pre'] === false) {
                    $value = $this->validators[$fieldName]['method']($value);
                    
                    if ($value === false) {
                        $errorMessage = $this->validators[$fieldName]['errorMessage'];
                        throw new  InvalidArgumentException($errorMessage ?? "Field $fieldName must be of type $fieldType");
                    }
                    $field->setValue($this, $value);
                }
            }
        }
    }
}


?>