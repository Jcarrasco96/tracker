<?php

declare(strict_types=1);

namespace app\models;

use app\core\BaseModel;
use app\core\database\query\DeleteSafeQuery;
use app\core\database\query\InsertSafeQuery;
use app\core\database\query\SelectSafeQuery;
use app\core\database\RawExpression;
use app\core\exceptions\NotFoundHttpException;
use Exception;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

final class Website extends BaseModel
{

    public string $id;
    public string $domain;

    protected static function tableName(): string
    {
        return 'website';
    }

    public function __construct(array $data = [])
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        } else {
            $this->id = Uuid::uuid4()->toString();
        }

        if (isset($data['domain'])) {
            $this->domain = $data['domain'];
        }
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public static function findById(string $uuid, bool $throwsOnError = false): ?self
    {
        $data = (new SelectSafeQuery())
            ->from(self::tableName())
            ->data()
            ->where('id', $uuid)
            ->limit(1)
            ->execute();

        if (empty($data) && $throwsOnError) {
            throw new NotFoundHttpException('Website not found');
        }

        if (empty($data)) {
            return null;
        }

        return self::fromArray(array_shift($data));
    }

    public static function findAll(): array
    {
        $data = (new SelectSafeQuery())
            ->from(self::tableName())
            ->data()
            ->orderBy('domain')
            ->execute();

        return array_map(static fn(array $data) => self::fromArray($data), $data);
    }

    public static function delete(string $website): bool
    {
        return (new DeleteSafeQuery())
            ->from(self::tableName())
            ->where('id', $website)
            ->execute();
    }

    public function create(): bool
    {
        return (new InsertSafeQuery())
            ->from(self::tableName())
            ->data([
                'id' => $this->id,
                'domain' => $this->domain,
            ])
            ->execute();
    }

    public function summary(): array
    {
        $summary = (new SelectSafeQuery())
            ->data([
                new RawExpression('COUNT(*) as total'),
                new RawExpression('COUNT(DISTINCT ip_hash) as unique_visitors'),
                new RawExpression('SUM(created_at >= CURDATE()) as today'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->execute();

        return array_shift($summary);
    }

    public function pages(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'url',
                new RawExpression('COUNT(*) as visits'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('url')
            ->orderBy('visits', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function referrers(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'referrer',
                new RawExpression('COUNT(*) as visits'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('referrer')
            ->orderBy('visits', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function events(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'event_type',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('event_type')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function timeSeries(): array
    {
        $day = date('Y-m-d');
        $start = $day . ' 00:00:00';
        $end = $day . ' 23:59:59';

        return (new SelectSafeQuery())
            ->data([
                new RawExpression('DATE(created_at) as date'),
//                new RawExpression('HOUR(created_at) as hour'),
                new RawExpression('COUNT(*) as visits'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
//            ->whereAdvanced('created_at', '>=', $start)
//            ->whereAdvanced('created_at', '<=', $end)
            ->groupBy('date')
//            ->groupBy('hour')
            ->orderBy('date')
//            ->orderBy('hour')
            ->execute();
    }

    public function languages(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'language',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('language')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function userAgents(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'user_agent',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('user_agent')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function browsers(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'browser',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('browser')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function os(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'os',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('os')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

    public function devices(): array
    {
        return (new SelectSafeQuery())
            ->data([
                'device_type',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $this->id)
            ->groupBy('device_type')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

}