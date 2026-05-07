<?php

namespace app\controllers;

use app\core\Controller;
use app\core\exceptions\BadRequestHttpException;
use app\core\services\Response;
use app\models\Event;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

class ApiController extends Controller
{

    /**
     * @throws RandomException
     * @throws BadRequestHttpException
     */
    public function actionTrack(): Response
    {
        header("Access-Control-Allow-Origin: *");

        $input  = $this->request->input();

        if (!$input) {
            throw new BadRequestHttpException('No input provided.');
        }

        $ip = $this->request->getIp();

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $referer = $_SERVER['HTTP_REFERER'] ?? $input['referrer'] ?? null;
//        $requestUri = $_SERVER['REQUEST_URI'] ?? null;

        $languageHeader = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;

        $language = null;

        if ($languageHeader) {
            $language = substr(explode(',', $languageHeader)[0], 0, 10);
        }

        $data = [
            'id' => Uuid::uuid4()->toString(),
            'website_id' => $input['website_id'],
            'event_type' => $input['event_type'],

            'url' => $input['url'],
            'referrer' => $referer,
            'user_agent' => $userAgent,
            'ip_hash' => hash('sha256', $ip),
            'language' => $language,

            'label' => $input['label'] ?? null,
            'value' => $input['value'] ?? null,
        ];

        $event = new Event($data);

        $event->create();

//        return $this->asJson($data);
        return new Response(null, 204);
    }

}