<?php

namespace App\Exceptions;

use Throwable;
use App\Libraries\ResponseBase;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if ($e instanceof NotFoundHttpException && str_contains($e->getMessage(), 'model')) {
                $firstIndex = strpos($e->getMessage(), "Models\\") + 7;
                $lastIndex = strpos($e->getMessage(), "]");
                $model = substr($e->getMessage(), $firstIndex, $lastIndex - $firstIndex);
                Log::error($e->getMessage());
                
                return ResponseBase::error("Id $model tidak ditemukan", 409);
            } else if ($e instanceof ModelNotFoundException) {
                Log::error($e->getMessage());
                return ResponseBase::error('Data tidak ditemukan', 404);
            } else if ($e instanceof NotFoundHttpException) {
                Log::error($e->getMessage());
                return ResponseBase::error('Url tidak ditemukan', 404);
            } else if ($e instanceof MethodNotAllowedHttpException) {
                Log::error($e->getMessage());
                return ResponseBase::error('Method tidak diizinkan', 405);
            } else if ($e instanceof ValidationException) {
                Log::error($e->getMessage());
                return ResponseBase::error($e->validator->errors(), 422);
            } else if ($e instanceof UnauthorizedException) {
                Log::error($e->getMessage());
                return ResponseBase::error('Tidak ada hak akses', 401);
            }  else {
                Log::error($e->getMessage());
                return ResponseBase::error('Internal server error', 500);
            }
        });
    }
}
