<?php

namespace app\models;

use app\core\BaseModel;
use app\core\database\query\InsertSafeQuery;
use app\core\database\query\SelectSafeQuery;
use app\core\database\RawExpression;
use app\core\exceptions\NotFoundHttpException;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

class Event extends BaseModel
{

    public string $id;
    public string $website_id;
    public string $event_type;
    public string $url;
    public string $referrer;
    public string $user_agent;
    public string $language;
    public string $ip_hash;
    public string $created_at;
    public ?string $label;
    public ?string $value;

    protected static function tableName(): string
    {
        return 'event';
    }

    public function __construct(array $data = [])
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        } else {
            $this->id = Uuid::uuid4()->toString();
        }

        if (isset($data['website_id'])) {
            $this->website_id = $data['website_id'];
        }
        if (isset($data['event_type'])) {
            $this->event_type = $data['event_type'];
        }
        if (isset($data['url'])) {
            $this->url = $data['url'];
        }
        if (isset($data['referrer'])) {
            $this->referrer = $data['referrer'];
        }
        if (isset($data['user_agent'])) {
            $this->user_agent = $data['user_agent'];
        }
        if (isset($data['language'])) {
            $this->language = $data['language'];
        }
        if (isset($data['ip_hash'])) {
            $this->ip_hash = $data['ip_hash'];
        }
        if (isset($data['created_at'])) {
            $this->created_at = $data['created_at'];
        }
        if (isset($data['label'])) {
            $this->label = $data['label'];
        } else {
            $this->label = null;
        }
        if (isset($data['value'])) {
            $this->value = $data['value'];
        } else {
            $this->value = null;
        }
    }

    /**
     * @throws NotFoundHttpException
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
            throw new NotFoundHttpException('Event not found');
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
            ->execute();

        return array_map(static fn(array $data) => self::fromArray($data), $data);
    }

    /**
     * @throws RandomException
     */
    public function create(): bool
    {
        return (new InsertSafeQuery())
            ->from(self::tableName())
            ->data([
                'id' => $this->id,
                'website_id' => $this->website_id,
                'event_type' => $this->event_type,
                'url' => $this->url,
                'referrer' => $this->referrer,
                'user_agent' => $this->user_agent,
                'language' => $this->language,
                'ip_hash' => $this->ip_hash,
                'label' => $this->label,
                'value' => $this->value,
            ])
            ->execute();
    }

    public static function summary(string $website): array
    {
        $summary = (new SelectSafeQuery())
            ->data([
                new RawExpression('COUNT(*) as total'),
                new RawExpression('COUNT(DISTINCT ip_hash) as unique_visitors'),
                new RawExpression('SUM(created_at >= CURDATE()) as today'),
            ])
            ->from('event')
            ->where('website_id', $website)
            ->execute();

        return array_shift($summary);
    }

    public static function pages(string $website): array
    {
        return (new SelectSafeQuery())
            ->data([
                'url',
                new RawExpression('COUNT(*) as visits'),
            ])
            ->from('event')
            ->where('website_id', $website)
            ->groupBy('url')
            ->orderBy('visits', 'DESC')
            ->limit(10)
            ->execute();
    }

    public static function referrers(string $website): array
    {
        return (new SelectSafeQuery())
            ->data([
                'referrer',
                new RawExpression('COUNT(*) as visits'),
            ])
            ->from('event')
            ->where('website_id', $website)
            ->groupBy('referrer')
            ->orderBy('visits', 'DESC')
            ->limit(10)
            ->execute();
    }

    public static function events(string $website): array
    {
        return (new SelectSafeQuery())
            ->data([
                'event_type',
                new RawExpression('COUNT(*) as total'),
            ])
            ->from('event')
            ->where('website_id', $website)
            ->groupBy('event_type')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->execute();
    }

    public static function timeSeries(string $website): array
    {
        return (new SelectSafeQuery())
            ->data([
                new RawExpression('DATE(created_at) as date'),
                new RawExpression('COUNT(*) as visits'),
            ])
            ->from('event')
            ->where('website_id', $website)
            ->groupBy('date')
            ->orderBy('date')
            ->execute();
    }
}