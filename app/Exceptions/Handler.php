<?php

namespace App\Exceptions;

use App\Enums\ResponseCodeEnum;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Resources\ErrorResource;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param $request
     * @param Throwable $e
     * @return ErrorResource|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e)) {
            if ($request->expectsJson()) {
                return new ErrorResource(
                    statusCode: $e->getStatusCode()
                );
            } else {
                if ($e->getStatusCode() === ResponseCodeEnum::NOT_FOUND) {
                    return response()->view('error_404', [], ResponseCodeEnum::NOT_FOUND);
                }
            }
        }

        return parent::render($request, $e);
    }
}
