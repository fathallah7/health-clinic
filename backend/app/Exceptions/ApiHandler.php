<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use App\Traits\ApiResponse;

class ApiHandler
{
    use ApiResponse;

    public function __invoke(Throwable $e, Request $request)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return $this->error('Resource not found', [], 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->error('Method not allowed', [], 405);
            }

            if ($e instanceof AuthenticationException) {
                return $this->error('Unauthenticated', [], 401);
            }

            if ($e instanceof AuthorizationException) {
                return $this->error('Forbidden', [], 403);
            }

            if ($e instanceof ThrottleRequestsException) {
                return $this->error('Too many requests', [], 429);
            }

            if ($e instanceof ValidationException) {
                return $this->error(
                    'Validation failed',
                    $e->errors(),
                    422,
                );
            }

            return $this->error('Server error', [], 500);
        }

        return null;
    }
}
