<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Session\Middleware\StartSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            // StartSession::class,
        ]);

        $middleware->group('api', [
            // EnsureFrontendRequestsAreStateful::class,
            'throttle:1000,1',
            SubstituteBindings::class,
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {

                return apiErrorGetResponse(
                    $e,
                    'Validation error',
                    [
                        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    'Validation error',
                    [
                        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    ]
                );
            }

            return null;
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_NOT_FOUND,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_NOT_FOUND,
                    ]
                );
            }

            return null;
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_UNAUTHORIZED,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_UNAUTHORIZED,
                    ]
                );
            }

            return null;
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_FORBIDDEN,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_FORBIDDEN,
                    ]
                );
            }

            return null;
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->expectsJson()) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_BAD_REQUEST,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_BAD_REQUEST,
                    ]
                );
            }

            return null;
        });

        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_NOT_FOUND,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_NOT_FOUND,
                    ]
                );
            }

            return null;
        });

        $exceptions->render(function (\Exception $e, Request $request) {
            if ($request->expectsJson()) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ]
                );
            } elseif ($request->is('api/*')) {
                return apiErrorGetResponse(
                    $e,
                    $e->getMessage(),
                    [
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ]
                );
            }

            return null;
        });
    })->create();
