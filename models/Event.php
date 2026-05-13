<?php

declare(strict_types=1);

namespace app\models;

use app\core\BaseModel;
use app\core\database\query\InsertSafeQuery;
use app\core\database\query\SelectSafeQuery;
use app\core\database\RawExpression;
use app\core\exceptions\NotFoundHttpException;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

final class Event extends BaseModel
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

    public ?string $browser;
    public ?string $os;
    public ?string $device_type;

    protected static function tableName(): string
    {
        return 'event';
    }

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? Uuid::uuid4()->toString();

        $this->website_id = $data['website_id'];
        $this->event_type = $data['event_type'];
        $this->url = $data['url'];
        $this->referrer = $data['referrer'];
        $this->user_agent = $data['user_agent'];
        $this->language = $data['language'];
        $this->ip_hash = $data['ip_hash'];
        $this->created_at = $data['created_at'] ?? '';

        $this->label = $data['label'] ?? null;
        $this->value = $data['value'] ?? null;

        $this->browser = $data['browser'] ?? null;
        $this->os = $data['os'] ?? null;
        $this->device_type = $data['device_type'] ?? null;
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

                'browser' => $this->browser,
                'os' => $this->os,
                'device_type' => $this->device_type,
            ])
            ->execute();
    }

}