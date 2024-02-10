<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;

class CustomExceptionHandler extends ExceptionHandler
{
    protected $dontReport = [
        // Add any exception classes that should not be reported to the logs here
    ];

    public function report(\Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, \Throwable $exception)
    {
        // Customize the JSON response here
        $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 0;
        $status = $status == 0 ? $exception->status : 500;

        $data = [
            'status' => $status,
            'message' => $exception->getMessage(),
            'data' => null,
        ];

        return new JsonResponse($data, $status);
    }
}
