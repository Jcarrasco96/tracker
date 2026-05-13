<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\exceptions\BadRequestHttpException;
use app\core\exceptions\NotFoundHttpException;
use app\core\helpers\Url;
use app\core\services\Response;
use app\models\Event;
use app\models\Website;
use app\utils\VisitorInfo;
use Exception;
use JShrink\Minifier;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

final class ApiController extends Controller
{

    /**
     * @throws RandomException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionTrack(): Response
    {
        header("Access-Control-Allow-Origin: *");

        $input  = App::$request->input();

        if (!$input) {
            throw new BadRequestHttpException('No input provided.');
        }

        $model = Website::findById($input['website_id']);

        if (!$model) {
            throw new NotFoundHttpException('Website not found.');
        }

        $languageHeader = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;

        $language = null;

        if ($languageHeader) {
            $language = substr(explode(',', $languageHeader)[0], 0, 10);
        }

        $info = VisitorInfo::getInfo();

        $browser = empty($info['browser']) ? null : "{$info['browser']['name']} {$info['browser']['version']}";
        $os = empty($info['os']) ? null : "{$info['os']['name']} {$info['os']['version']}";

        $data = [
            'id' => Uuid::uuid4()->toString(),
            'website_id' => $input['website_id'],
            'event_type' => $input['event_type'],

            'url' => $input['url'],
            'referrer' => $_SERVER['HTTP_REFERER'] ?? $input['referrer'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'ip_hash' => hash('sha256', $info['ip']),
            'language' => $language,

            'label' => $input['label'] ?? null,
            'value' => $input['value'] ?? null,

            'browser' => $browser,
            'os' => $os,
            'device_type' => $info['device']['type'] ?? null,
        ];

        $event = new Event($data);

        $event->create();

//        return $this->asJson($data);
        return new Response(null, 204);
    }

    public function actionScript(): Response
    {
        $script = file_get_contents(BASE_PATH . 'assets/script.min.js');

        $js = str_replace("%URL%", Url::to('api/e'), $script);

//        try {
//            $js = Minifier::minify($js, ['flaggedComments' => false]);
//        } catch (Exception $e) {
//            App::$logger->throwable($e);
//        }

        $response = new Response($js, 200);

        $response->header('Content-Type', 'application/javascript');

        return $response;
    }

}