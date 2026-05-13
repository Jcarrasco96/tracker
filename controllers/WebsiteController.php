<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\exceptions\NotFoundHttpException;
use app\core\exceptions\ServerErrorHttpException;
use app\core\Permission;
use app\core\services\Response;
use app\core\widgets\Alert;
use app\models\Event;
use app\models\Website;

final class WebsiteController extends Controller
{

    protected function beforeAction(string $methodName): void
    {
        $this->loadScript('/assets/js/bootstrap.bundle.js');
        $this->loadScript('/assets/js/index.js');
        $this->loadScript('/assets/js/bootstrap-notify.js');
        $this->loadScript('/assets/js/notify.js');

        parent::beforeAction($methodName);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    #[Permission(['@'])]
    public function actionDetails(string $website): string
    {
        $this->loadScript('https://cdn.jsdelivr.net/npm/chart.js', inHead: true);

        $model = Website::findById($website);

        if (!$model) {
            Alert::flash('info', 'Website not found.');
            Response::redirect('site/index');
        }

        $summary = $model->summary();
        $pages = $model->pages();
        $referrers = $model->referrers();
        $events = $model->events();
        $timeSeries = $model->timeSeries();
        $languages = $model->languages();
        $userAgents = $model->userAgents();
        $browsers = $model->browsers();
        $os = $model->os();
        $devices = $model->devices();

//        $visitsByHour = array_fill(0, 24, 0);
//
//        foreach ($timeSeries as $r) {
//            $hour = (int)$r['hour'];
//            $visitsByHour[$hour] = (int)$r['visits'];
//        }
//
//        $timeSeries = [];
//        foreach ($visitsByHour as $hour => $visits) {
//            $label = str_pad((string)$hour, 2, '0', STR_PAD_LEFT) . ':00';
//            $timeSeries[] = [
//                'date'   => $label,
//                'visits' => $visits,
//            ];
//        }

        return $this->render('details', [
            'website' => $website,
            'summary' => $summary,
            'pages' => $pages,
            'referrers' => $referrers,
            'events' => $events,
            'timeSeries' => $timeSeries,
            'languages' => $languages,
            'userAgents' => $userAgents,
            'browsers' => $browsers,
            'os' => $os,
            'devices' => $devices,
        ]);
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): string|array
    {
        $isAjaxPost = App::$request->isAjax() && App::$request->isPost();

        if ($isAjaxPost) {
            $errors = [];

            $domain = App::$request->post('domain');

            if (!$domain) {
                $errors['domain'] = "Domain is required.";
            }

            if ($errors) {
                return ['success' => false, 'errors' => $errors];
            }

            if (isset($_POST['_validate'])) {
                return ['success' => true];
            }

            $website = new Website([
                'domain' => strtoupper($domain),
            ]);
            $website->create();

            Alert::flash('success', 'Website created successfully.');

            return ['success' => true];
        }

        return $this->renderPartial('create');
    }

    public function actionDelete(string $website): array
    {
        $deleted = Website::delete($website);

        if ($deleted) {
            return ['success' => true, 'message' => 'Website deleted successfully.'];
        }

        return ['success' => false];
    }

}