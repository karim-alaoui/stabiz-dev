<?php

namespace App\Exceptions;

use App\Actions\ErrorRes;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
        ActionException::class,
        OAuthServerException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

        });
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response|ResponseAlias
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse|ResponseAlias
    {
        $defaultMsg = __('exception.something_wrong');
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            switch (true) {
                case $e instanceof MethodNotAllowedHttpException:
                    return ErrorRes::execute($e->getMessage(), 405);

                case $e instanceof ModelNotFoundException:
                    return ErrorRes::execute(__('exception.model_not_found'), 404);

                case $e instanceof NotFoundHttpException:
                case ($e instanceof HttpException && $e->getStatusCode() === 404) :
                    return ErrorRes::execute(__('exception.invalid_route'), 404);

                case $e instanceof ActionValidationException:
                    return ErrorRes::execute($defaultMsg, 400, [
                        'actual_error' => 'Missed some validation in Action class. Check errors and file to debug',
                        'file' => $e->getFile(),
                        'errors' => $e->errors(),
                    ]);

                case $e instanceof ValidationException:
                    return ErrorRes::execute(__('exception.validation_error'), 422, [
                        'errors' => $this->handleValidationError($e)
                    ]);

                case $e instanceof AuthorizationException:
                case $e instanceof MissingScopeException:
                    return ErrorRes::execute(__('exception.no_permission'), 403);

                case $e instanceof AuthenticationException:
                    return ErrorRes::execute($e->getMessage() ?? __('exception.unauthorized'), 401);

                case $e instanceof ActionException:
                    return ErrorRes::execute($e->getMessage() ?? __('exception.something_wrong'), $e->getCode() ?? 400);

                case $e instanceof ThrottleRequestsException:
                    $extra = [];
                    if (!App::environment(['production'])) {
                        $extra = [
                            'actual_error' => 'Too many request sent in a min than the limit for this API endpoint'
                        ];
                    }
                    $msg = __('Too many requests. Please wait a min before sending another request');
                    return ErrorRes::execute($msg, 429, $extra);

                default:
                    $extra = [];
                    if (App::environment(['staging', 'dev', 'local'])) {
                        $extra = [
                            'actual_error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'class' => get_class($e)
                        ];
                    }
                    return ErrorRes::execute($defaultMsg, 500, $extra);
            }
        }
        return parent::render($request, $e);
    }

    /**
     * This will catch any English phrase(usually it's one word, two or three)
     * in non English (here Non english means Japanese)
     * sentence and then, it translates that English to Japanese.
     * Like if first_name is required and in validation the request key name
     * is first_name, even after the translation it would show first name (japanese here for is required)
     * By this, we translate that first_name to Japanese.
     * This is used in validation error.
     * @param $msg
     * @param $phrases
     * @return string|string[]
     */
    protected function replaceEngToJP($msg, $phrases): array|string
    {
        if (App::getLocale() == 'ja') {
            preg_match_all('/((\w+)(\s\w+)?(\s\w+)?(\s\w+)?(\s\w+)?)/i', $msg, $matches);
            $matches = $matches[0]; // the english text would be the first one in the array
            foreach ($matches as $match) {
                // convert the english word to underscore and check if there's any key with
                // that underscore value
                $underscorePhrase = str_replace(' ', '_', strtolower($match));
                if (Arr::has($phrases, $underscorePhrase)) {
                    $msg = str_replace($match, Arr::get($phrases, $underscorePhrase), $msg);
                }
            }
        }
        return $msg;
    }

    /**
     * @param ValidationException $exception
     * @return array
     */
    protected function handleValidationError(ValidationException $exception): array
    {
        $errorMessage = $exception->getMessage();
        $statusCode = $exception->getCode();

        if (empty($message)) {
            $errorMessage = __('validation.validation_error');
        }
        if (empty($statusCode)) {
            $statusCode = 422;
        }
        $errors = $exception->errors();

        if (\app()->getLocale() == 'ja') { // check if locale is japanese
            try {
                /**
                 * this will return the array from phrases file
                 * getcwd() return the current working dir to be public folder and not App/Exceptions
                 */
                /** @noinspection PhpIncludeInspection */
                $phrases = include './../resources/lang/ja/phrase.php';

                /**
                 * When the locale is Japanese, in the error message we translate the English
                 * attribute to Japanese. Eg- the error msg may be like
                 * title フィールドは必須です。which means title is required in English.
                 * We translate the English word title to Japanese here and it will be
                 * タイトル フィールドは必須です。- title field is required.
                 * Wrap this entire thing in try catch so that it doesn't break the application
                 * on any error during the translation.
                 */
                foreach ($errors as $errorKey => $error) {
                    $msgs = $error;
                    if (gettype($msgs) == 'array') {
                        foreach ($msgs as $msgKey => $msg) {
                            $msgs[$msgKey] = $this->replaceEngToJP($msg, $phrases);
                        }
                        $errors[$errorKey] = $msgs;
                    }
                }
            } catch (Exception $e) {
                if (\app()->environment(['local', 'staging', 'production'])) report($e);
            }
        }

        return $errors;
    }
}
