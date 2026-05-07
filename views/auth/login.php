<?php

/** @var array $errors */
/** @var array $old */

use app\core\App;
use app\core\helpers\Html;
use app\core\helpers\Url;

?>

<section class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                <div class="card">

                    <div class="card-body">

                        <div class="text-center">
                            <img src="<?= Html::img('folder_64.png') ?>" alt="Logo"/>

                            <h1 class="display-4"><?= App::$config['name'] ?></h1>

                            <h5 class="card-title fs-4">Login to your account</h5>
                            <p class="small">Enter your username and password to login</p>
                        </div>

                        <form id="loginForm" class="row g-3 needs-validation" method="post" novalidate>

                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars(App::$session->_csrf()) ?>">

                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email" name="email" value="<?= $old['email'] ?? '' ?>" class="form-control <?= isset($errors['email']) ? 'is-invalid' : 'is-valid' ?>" id="floatingInput" placeholder="name@example.com" required>
                                    <label for="floatingInput">Email address</label>
                                    <div class="invalid-feedback"><?= $errors['email'] ?? 'Please enter your email.' ?></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating mb-0">
                                    <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : 'is-valid' ?>" id="floatingInput2" placeholder="" required>
                                    <label for="floatingInput2">Password</label>
                                    <div class="invalid-feedback"><?= $errors['password'] ?? 'Please enter your password.' ?></div>
                                </div>
                            </div>

                            <?php if (isset($errors['general'])): ?>
                                <div class="col">
                                    <div class="alert alert-danger mb-0" role="alert">
                                        <?= $errors['general'] ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-12">
                                <button class="btn btn-dark btn-lg w-100" type="submit" data-track-event="login-page" data-track-label="button" data-track-value="view login button"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                            </div>

                            <div class="col-12 d-none">
                                <p class="small mb-0">Don't have account? <a href="<?= Url::to('auth/register') ?>">Create an account</a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>