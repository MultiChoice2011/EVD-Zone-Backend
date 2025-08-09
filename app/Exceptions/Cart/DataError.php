<?php
namespace App\Exceptions\Cart;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DataError extends HttpException
{
    public function __construct($message = null, $statusCode = 400)
    {
        parent::__construct($statusCode, $message);
    }

    public function render($request)
    {
        return response()->json([
            'status' => false,
            "code"  => $this->getStatusCode(),
            "message"  => $this->getMessage() ,
            "data"  => [],
        ], $this->getStatusCode());
    }
}
