<?php


class HTTPException extends Exception
{
    protected int $statusCode;
    protected string $responseMessage;
    protected ?string $errorDetails;

    public function __construct(int $statusCode, string $responseMessage, ?string $errorDetails = null)
    {
        $this->statusCode = $statusCode;
        $this->responseMessage = $responseMessage;
        $this->errorDetails = $errorDetails;


        $this->logError();
        $this->sendResponse();
        // Call the parent constructor
        parent::__construct($responseMessage, $statusCode);
    }

    public function logError(): void
    {
        if ($this->errorDetails) {
            error_log("HTTP Error [{$this->statusCode}]: {$this->responseMessage} - {$this->errorDetails}");
        } else {
            error_log("HTTP Error [{$this->statusCode}]: {$this->responseMessage}");
        }
    }

    // Send the appropriate HTTP response
    public function sendResponse(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
        }
        echo $this->responseMessage;
    }

    // Override __toString() to display error message for debugging
    public function __toString(): string
    {
        return "HTTP Exception [{$this->statusCode}]: {$this->responseMessage}";
    }
}

class GoneHTTPException extends HTTPException {
    public function __construct(string $message = "The requested resource is no longer available and has been removed permanently", string $errorDetails = null)
    {
        parent::__construct(410, $message, $errorDetails);
    }
}


class ConflictHTTPException extends HTTPException
{
    public function __construct(string $message = "Record already exists", string $errorDetails = null)
    {
        parent::__construct(409, $message, $errorDetails);
    }
}

class NotFoundHTTPException extends HTTPException
{
    public function __construct(string $message = "Resource not found", string $errorDetails = null)
    {
        parent::__construct(404, $message, $errorDetails);
    }
}


class UnauthorizedHTTPException extends HTTPException
{
    public function __construct(string $message = "Access to resource unauthorized", string $errorDetails = null)
    {
        parent::__construct(401, $message, $errorDetails);
    }
}

class BadRequestHTTPException extends HTTPException
{
    public function __construct(string $message = "Bad request", string $errorDetails = null)
    {
        parent::__construct(400, $message, $errorDetails);
    }
}

class ForbiddenHTTPException extends HTTPException
{
    public function __construct(string $message = "Forbidden", string $errorDetails = null)
    {
        parent::__construct(403, $message, $errorDetails);
    }
}

?>