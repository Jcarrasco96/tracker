<?php

namespace app\controllers;

use app\core\App;
use app\core\Controller;
use app\core\exceptions\BadRequestHttpException;
use app\core\exceptions\NotFoundHttpException;
use app\core\helpers\Url;
use app\core\RateLimitChecker;
use app\core\services\FileCache;
use app\core\services\Response;
use app\models\Event;
use app\models\Website;
use app\utils\VisitorInfo;
use Exception;
use JShrink\Minifier;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

class ApiController extends Controller
{

    /**
     * @throws RandomException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionTrack(): Response
    {
        header("Access-Control-Allow-Origin: *");

        $input  = $this->request->input();

        if (!$input) {
            throw new BadRequestHttpException('No input provided.');
        }

        $model = Website::findById($input['website_id']);

        if (!$model) {
            throw new NotFoundHttpException('Website not found.');
        }

//        $ip = $this->request->getIp();

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

    #[RateLimitChecker]
    public function actionScript(): Response
    {
        $cache = new FileCache();
        $value = $cache->getValue('script');

        if ($value) {
            $response = new Response($value, 200);
            $response->header('Content-Type', 'application/javascript');
            //$response->header('X-Content-Cache', 'using-file-cache');
            return $response;
        }

        $url = Url::to('api/e');

        $js = <<< JS
        (function () {
            const script = document.currentScript;
            const websiteId = script.getAttribute("data-website-id");
        
            if (!websiteId) {
                return;
            }
        
            function track(eventType, data = {}) {
                const payload = {
                    website_id: websiteId,
                    event_type: eventType,
                    referrer: document.referrer,
                    url: location.href,
                    ...data
                };
        
                fetch("$url", {
                    method: "POST",
                    keepalive: true,
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                }).catch(() => {});
            }
        
            track("pageview");
        
            let lastUrl = location.href;
        
            setInterval(() => {
                if (location.href !== lastUrl) {
                    lastUrl = location.href;
                    track("pageview");
                }
            }, 1000);
        
            document.addEventListener("click", function (e) {
                const el = e.target.closest("[data-track-event]");
                if (!el) {
                    return;
                }
        
                track(el.getAttribute("data-track-event"), {
                    label: el.getAttribute("data-track-label"),
                    value: el.getAttribute("data-track-value")
                });
            });
        })();
        JS;

        try {
            $js = Minifier::minify($js, ['flaggedComments' => false]);
        } catch (Exception $e) {
            App::$logger->throwable($e);
        }

        $cache->setValue('script', $js, 604800);

        $response = new Response($js, 200);

        $response->header('Content-Type', 'application/javascript');

        return $response;
    }

}