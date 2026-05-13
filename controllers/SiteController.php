<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Permission;
use app\core\services\Response;
use app\models\Website;
use Exception;

final class SiteController extends Controller
{

    /**
     * @throws Exception
     */
    #[Permission(['@'])]
    public function actionIndex(): string|Response
    {
        $this->loadScript('/assets/js/views/index.js');

        $websites = Website::findAll();

        return $this->render('index', [
            'websites' => $websites,
        ]);
    }

    #[Permission(['@'])]
    public function actionSelectRole(string $role): Response
    {
        App::$session->setSelectedRole($role);

        return $this->asJson(['status' => 200, 'message' => $role]);
    }

    public function actionBefore(): string
    {
        header('x-powered-by: Tracker');

        return "";
    }

}