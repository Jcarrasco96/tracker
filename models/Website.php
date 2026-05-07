<?php

namespace app\models;

use app\core\BaseModel;
use app\core\database\query\DeleteSafeQuery;
use app\core\database\query\InsertSafeQuery;
use app\core\database\query\SelectSafeQuery;
use app\core\exceptions\NotFoundHttpException;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

class Website extends BaseModel
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

    /**
     * @throws RandomException
     */
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
}