<?php

namespace app\utils;

class VisitorInfo
{

    private static function getBrowser(string $ua): array
    {
        $browsers = [
            '/Edg\/([\d\.]+)/i' => ['Edge', '$1'],
            '/Edge\/([\d\.]+)/i' => ['Edge', '$1'],
            '/OPR\/([\d\.]+)/i' => ['Opera', '$1'],
            '/Opera\/.*Version\/([\d\.]+)/i' => ['Opera', '$1'],
            '/Chrome\/([\d\.]+)/i' => ['Chrome', '$1'],
            '/Version\/([\d\.]+).*Safari/i' => ['Safari', '$1'],
            '/Firefox\/([\d\.]+)/i' => ['Firefox', '$1'],
            '/Trident\/7.0.*rv:([\d\.]+)/i' => ['IE', '$1'],
            '/MSIE\s([\d\.]+)/i' => ['IE', '$1'],
        ];

        foreach ($browsers as $regex => $info) {
            if (preg_match($regex, $ua, $m)) {
                return [
                    'name' => $info[0],
                    'version' => $m[1],
                ];
            }
        }

        return ['name' => 'Unknown', 'version' => ''];
    }

    private static function getOS(string $ua): array
    {
        $oses = [
            '/Windows NT 10.0/i' => ['Windows', '10'],
            '/Windows NT 6.3/i' => ['Windows', '8.1'],
            '/Windows NT 6.2/i' => ['Windows', '8'],
            '/Windows NT 6.1/i' => ['Windows', '7'],
            '/Windows NT 6.0/i' => ['Windows', 'Vista'],
            '/Windows NT 5.1/i' => ['Windows', 'XP'],
            '/Mac OS X ([\d_\.]+)/i' => ['macOS', '$1'],
            '/Android ([\d\.]+)/i' => ['Android', '$1'],
            '/iPhone OS ([\d_]+)/i' => ['iOS', str_replace('_', '.', '$1')],
            '/iPad; CPU OS ([\d_]+)/i' => ['iOS', str_replace('_', '.', '$1')],
            '/Linux/i' => ['Linux', ''],
            '/CrOS/i' => ['Chrome OS', ''],
            '/FreeBSD/i' => ['FreeBSD', ''],
        ];

        foreach ($oses as $regex => $info) {
            if (preg_match($regex, $ua, $m)) {
                $version = $m[1] ?? $info[1];
                $version = str_replace('_', '.', $version);
                return [
                    'name' => $info[0],
                    'version' => $version,
                ];
            }
        }

        return ['name' => 'Unknown', 'version' => ''];
    }

    private static function getDeviceType(string $ua): string
    {
        $ua = strtolower($ua);

        $tabletKeywords = [
            'tablet', 'ipad', 'playbook', 'silk', 'kindle',
            'nexus 7', 'nexus 9', 'nexus 10', 'sm-t',
        ];

        foreach ($tabletKeywords as $kw) {
            if (str_contains($ua, $kw)) {
                return 'tablet';
            }
        }

        $mobileKeywords = [
            'mobile', 'android', 'iphone', 'ipod', 'blackberry',
            'iemobile', 'opera mini', 'opera mobi', 'fennec',
            'windows phone', 'bb10', 'nokia', 'samsung-sgh', 'htc'
        ];

        foreach ($mobileKeywords as $kw) {
            if (str_contains($ua, $kw)) {
                return 'mobile';
            }
        }

        return 'desktop';
    }

    private static function getIp(): ?string
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) === true) {
            $ip = $_SERVER['REMOTE_ADDR'];
            if (preg_match('/^(?:127|10)\.0\.0\.[12]?\d{1,2}$/', $ip)) {
                if (isset($_SERVER['HTTP_X_REAL_IP'])) {
                    $ip = $_SERVER['HTTP_X_REAL_IP'];
                } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
                }
            }
        } else {
            $ip = '127.0.0.1';
        }
        if (in_array($ip, ['::1', '0.0.0.0', 'localhost'], true)) {
            $ip = '127.0.0.1';
        }
        $filter = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if ($filter === false) {
            $ip = '127.0.0.1';
        }

        return $ip;
    }

    private static function getLocation(string $ip): array
    {
        $context = stream_context_create([
            'http' => ['timeout' => 2],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);

        $url = "https://ipinfo.io/$ip/json";

        $json = @file_get_contents($url, false, $context);

        if ($json !== false) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data['country'])) {
                $loc = $data['loc'] ?? null;
                $lat = $lon = null;
                if ($loc && str_contains($loc, ',')) {
                    [$lat, $lon] = explode(',', $loc);
                }

                return [
                    'country' => $data['country'],
                    'country_name' => $data['country'],
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'postal' => $data['postal'] ?? null,
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'timezone' => $data['timezone'] ?? null,
                ];
            }
        }

        $url = "https://freegeoip.app/json/162.43.188.71";
        $json = @file_get_contents($url, false, $context);

        if ($json !== false) {
            $data = json_decode($json, true);
            if (is_array($data) && !empty($data['country_code'])) {
                return [
                    'country' => $data['country_code'],
                    'country_name' => $data['country_name'] ?? null,
                    'region' => $data['region_name'] ?? null,
                    'city' => $data['city'] ?? null,
                    'postal' => $data['zip_code'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'timezone' => $data['time_zone'] ?? null,
                ];
            }
        }

        return [];
    }

    public static function getInfo(): array
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $browser = self::getBrowser($ua);
        $os = self::getOS($ua);
        $device = self::getDeviceType($ua);
        $ip = self::getIp();
        $location = [];//$ip ? self::getLocation($ip) : [];

        return [
            'ip' => $ip,
            'browser' => $browser,
            'os' => $os,
            'device' => [
                'type' => $device,
                'brand' => null,
                'model' => null,
            ],
            'location' => $location,
        ];
    }

}