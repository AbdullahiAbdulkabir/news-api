<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\Response;

class HttpException extends Exception implements Renderable
{
    public function __construct(
        ?string $message = null,
        int $code = Response::HTTP_BAD_REQUEST,
        public mixed $response = null,
        public mixed $data = [],
    ) {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return is_callable($this->response) ? value($this->response) :
            response()->json([
                'status' => false,
                'message' => $this->message,
                'data' => $this->data ?? []
            ], $this->code);
    }
}
