<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Throwable;

class ValidationException extends \Exception implements \Throwable
{
    protected int $http_code = 500;

    /**
     * @param array|string $message
     * @param int $http_code
     * @param Throwable|null $previous
     */

    public function __construct(string $message, int $http_code = 500, Throwable $previous = null)
    {
        parent::__construct($message);

        $this->http_code = $http_code;
    }

    /**
     * @return int
     */

    public function getHttpCode(): int
    {
        return $this->http_code;
    }

    public function render(): JsonResponse
    {
        $data = $this->getMessage();

        return Response()->json(json_decode($data), $this->getHttpCode());
    }
}
