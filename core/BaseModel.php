<?php

namespace app\core;

use app\core\exceptions\NotFoundHttpException;

abstract class BaseModel
{

    protected array $attributes = [];

    public static function fromArray(array $data): static
    {
        $obj = new static();

        foreach ($data as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->$key = $value;
            }
        }

        return $obj;
    }

    public function toArray(): array
    {
        $data = get_object_vars($this);

        if (property_exists($this, 'attributes') && isset($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function __get(string $name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return null;
    }

    public function __set(string $name, $value): void
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function loadRelation(string $relation_name): array|self
    {
        return $this->attributes[$relation_name] ?? throw new NotFoundHttpException("The relation '$relation_name' does not exist");
    }

    public function unloadRelation(string $relation_name): void
    {
        if (isset($this->attributes[$relation_name])) {
            unset($this->attributes[$relation_name]);
        }
    }

    abstract protected static function tableName(): string;

}