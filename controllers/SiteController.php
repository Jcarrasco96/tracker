<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\Permission;
use app\core\services\Response;
use app\models\Event;
use app\models\Website;
use Exception;

class SiteController extends Controller
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

    /**
     * @throws Exception
     */
    #[Permission(['@'])]
    public function actionWebsite(string $website): string|Response
    {
        $this->loadScript('https://cdn.jsdelivr.net/npm/chart.js', inHead: true);

        $summary = Event::summary($website);
        $pages = Event::pages($website);
        $referrers = Event::referrers($website);
        $events = Event::events($website);
        $timeSeries = Event::timeSeries($website);

        return $this->render('website', [
            'title' => 'Website',
            'website' => $website,
            'summary' => $summary,
            'pages' => $pages,
            'referrers' => $referrers,
            'events' => $events,
            'timeSeries' => $timeSeries,
        ]);
    }

    #[Permission(['@'])]
    public function actionSelectRole(string $role): Response
    {
        App::$session->setSelectedRole($role);

        return $this->asJson(['status' => 200, 'message' => $role]);
    }

}