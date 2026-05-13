<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Permission;
use app\core\services\Response;
use app\models\User;
use Exception;
use JetBrains\PhpStorm\NoReturn;

final class AuthController extends Controller
{

    private const MAX_LOGIN_ATTEMPTS = 3;
    private const LOCKOUT_TIME = 30;

    /**
     * @throws Exception
     */
    #[Permission(['?'])]
    public function actionLogin(): string
    {
        $this->layout = 'guest';

        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
            if (isset($_SESSION['last_attempt_time'])) {
                $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];
                if ($timeSinceLastAttempt < self::LOCKOUT_TIME) {
                    return $this->render('login', ['errors' => ['password' => 'Too many failed attempts. Please try again later.'], 'old' => ['email' => '']]);
                } else {
                    $_SESSION['login_attempts'] = 0;
                }
            }
        }

        if (App::$request->isPost()) {
            $this->validateCsrf('auth/login');

            $email = App::$request->post('email');
            $password = App::$request->post('password');

            $errors = [];

            if (empty($email)) {
                $errors['email'] = 'Email is required';
            }

            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }

            if (!empty($errors)) {
                return $this->render('login', ['errors' => $errors, 'old' => ['email' => $email]]);
            }

            $user = User::findByCredentials($email, $password);

            if (!$user) {
                App::$session->attemptFailed();
                $this->logLoginAttempt($email, false);

                $errors['password'] = 'Email or password is incorrect.';
                return $this->render('login', ['errors' => $errors, 'old' => ['email' => $email]]);
            }

            App::$session->removeCSRF();
            App::$session->create($user->id, $user->email, $user->roles());
            App::$user = $user;
            $this->logLoginAttempt($email);

            $redirect = urldecode($_GET['redirect'] ?? 'site/index');
            $redirectUrl = str_contains($redirect, '//') ? 'site/index' : $redirect;
            Response::redirect($redirectUrl);
        }

        App::$session->generateCSRF(true);

        return $this->render('login', ['errors' => [], 'old' => '']);
    }

    #[Permission(['@'])]
    #[NoReturn]
    public function actionLogout(): void
    {
        App::$session->destroy();
        Response::redirect('auth/login');
    }

    public function actionRegister(): Response
    {
        return $this->asJson([
            'data' => 'not implemented'
        ], 404);
    }

    private function logLoginAttempt(string $username, bool $success = true): void
    {
        $logEntry = date('Y-m-d H:i:s') . " - User: $username - " . ($success ? "SUCCESS" : "FAILED") . "\n";
        file_put_contents(APP_LOGS_FOLDER . 'login_attempts.log', $logEntry, FILE_APPEND | LOCK_EX);
    }

}