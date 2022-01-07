<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $errorLog = collect([
                [
                    'file' => $e->getFile(),
                    'line' => $e->getFile(),
                    'code' => $e->getCode(),
                ]
            ])->concat(collect($e->getTrace())->take(5))->toArray();

            Log::channel('slack')->error($e->getMessage(), $errorLog);
        });
    }
}
