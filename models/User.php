<?php

namespace app\models;

use app\core\BaseModel;
use app\core\database\query\InsertSafeQuery;
use app\core\database\query\SelectSafeQuery;
use app\core\exceptions\NotFoundHttpException;
use app\core\services\Security;
use Exception;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

class User extends BaseModel
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETED = -1;

    const ROLE_ADMIN = 'administrator';

    public string $id;
    public string $name;
    public string $email;
    public string $password;
    public string $auth_key;

    public bool $is_admin;

    public string $status;

    /**
     * @throws Exception
     */
    public function __construct(array $data = [])
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        } else {
            $this->id = Uuid::uuid4()->toString();
        }

        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
        if (isset($data['email'])) {
            $this->email = $data['email'];
        }
        if (isset($data['password'])) {
            $this->password = $data['password'];
        }
        if (isset($data['auth_key'])) {
            $this->auth_key = $data['auth_key'];
        } else {
            $this->auth_key = Security::generateRandomString();
        }

        if (isset($data['status'])) {
            $this->status = $data['status'];
        } else {
            $this->status = self::STATUS_ACTIVE;
        }
    }

    /**
     * @throws Exception
     */
    public static function findByCredentials(string $email, string $password): ?self
    {
        $data = (new SelectSafeQuery())
            ->from(self::tableName())
            ->data()
            ->where('email', $email)
            ->execute();

        $data = array_shift($data);

        if (empty($data)) {
            return null;
        }

        if (!Security::validatePassword($data['auth_key'] . $password, $data['password'])) {
            return null;
        }

        return self::fromArray($data);
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
            ->where('status', self::STATUS_ACTIVE)
            ->limit(1)
            ->execute();

        if (empty($data) && $throwsOnError) {
            throw new NotFoundHttpException('User not found');
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
                'email' => $this->email,
                'password' => Security::generatePasswordHash($this->auth_key . $this->password),
                'auth_key' => $this->auth_key,
                'status' => $this->status,
            ])
            ->execute();
    }

    static protected function tableName(): string
    {
        return 'user';
    }

    public function roles(): array
    {
        $roles = [];

        if ($this->is_admin) {
            $roles[] = self::ROLE_ADMIN;
        }

        return $roles;
    }

}