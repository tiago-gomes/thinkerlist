<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use App\Enums\ErrorCode;
use Throwable;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CustomExceptionHandler extends ExceptionHandler
{
    protected $dontReport = [
        // Add any exception classes that should not be reported to the logs here
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        // Customize the JSON response here
        $status = $this->getStatusCode($exception);

        $customMessage = $this->getCustomMessage($exception);

        $data = [
            'status' => $status,
            'message' => $customMessage,
            'data' => null,
        ];

        return new JsonResponse($data, $status);
    }

    private function getStatusCode(Throwable $exception): int
    {
        $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 0;

        if ($status == 0 && $exception instanceof ValidationException) {
            $status = ErrorCode::UNPROCESSABLE_ENTITY->value;
        } elseif ($status == 0 && $exception instanceof InvalidArgumentException) {
            $status = ErrorCode::UNPROCESSABLE_ENTITY->value;
        } elseif ($status == 0) {
            $status = ErrorCode::INTERNAL_SERVER_ERROR->value;
        }

        return $status;
    }

    private function getCustomMessage(Throwable $exception): string
    {
        $customMessage = method_exists($exception, 'getCustomMessage') ? $exception->getCustomMessage() : '';

        if (empty($customMessage)) {
            $customMessage = $exception->getMessage();
        }

        return $customMessage;
    }
}
