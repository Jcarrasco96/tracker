<?php

namespace app\controllers;

use app\core\Controller;
use app\core\exceptions\NotFoundHttpException;
use app\core\Permission;
use app\core\services\Response;
use app\core\widgets\Alert;
use app\models\Event;
use app\models\Website;
use Random\RandomException;

class WebsiteController extends Controller
{

    /**
     * @throws NotFoundHttpException
     */
    #[Permission(['@'])]
    public function actionDetails(string $website): string|Response
    {
        $this->loadScript('https://cdn.jsdelivr.net/npm/chart.js', inHead: true);

        $model = Website::findById($website);

        if (!$model) {
            Alert::flash('info', 'Website not found.');
            $this->redirect('site/index');
        }

        $summary = Event::summary($model->id);
        $pages = Event::pages($model->id);
        $referrers = Event::referrers($model->id);
        $events = Event::events($model->id);
        $timeSeries = Event::timeSeries($model->id);
        $languages = Event::languages($model->id);
        $userAgents = Event::userAgents($model->id);
        $browsers = Event::browsers($model->id);
        $os = Event::os($model->id);
        $devices = Event::devices($model->id);

        $visitsByHour = array_fill(0, 24, 0);

        foreach ($timeSeries as $r) {
            $hour = (int)$r['hour'];
            $visitsByHour[$hour] = (int)$r['visits'];
        }

        $timeSeries = [];
        foreach ($visitsByHour as $hour => $visits) {
            $label = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            $timeSeries[] = [
                'date'   => $label,
                'visits' => $visits,
            ];
        }

        return $this->render('details', [
            'title' => 'Website',
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
     * @throws RandomException
     */
    public function actionCreate(): string|Response
    {
        $isAjaxPost = $this->request->isAjax() && $this->request->isPost();

        if ($isAjaxPost) {
            $errors = [];

            $domain = $this->request->post('domain');

            if (!$domain) {
                $errors['domain'] = "Domain is required.";
            }

            if ($errors) {
                return $this->asJson([
                    'success' => false,
                    'errors' => $errors
                ]);
            }

            if (isset($_POST['_validate'])) {
                return $this->asJson(['success' => true]);
            }

            $website = new Website([
                'domain' => strtoupper($domain),
            ]);
            $website->create();

            Alert::flash('success', 'Website created successfully.');

            return $this->asJson(['success' => true]);
        }

        return $this->renderPartial('create');
    }

    public function actionDelete(string $website): Response
    {
        $deleted = Website::delete($website);

        if ($deleted) {
            Alert::flash('success', 'Website deleted successfully.');
            return $this->asJson(['success' => true]);
//            return $this->asJson(['status' => 200, 'message' => 'Website eliminado correctamente.']);
        }

//        Alert::flash('success', 'Website created successfully.');
//        return $this->asJson(['status' => 400, 'message' => 'No se pudo eliminar el producto.']);
        return $this->asJson(['success' => false]);
    }

}