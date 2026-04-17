<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Concerns;

trait IsJsonable
{
    public function jsonSerialize(): mixed
    {
        if (method_exists($this, 'jsonSerializeUsing')) {
            return $this->jsonSerializeUsing();
        }

        $include = [];
        if (method_exists($this, 'getJsonSerializeProperties')) {
            $include = $this->getJsonSerializeProperties();
        }
        $include = array_unique($include);

        $ret = [];
        $refProps = new \ReflectionObject($this)->getProperties();

        foreach ($refProps as $prop) {
            if (!$prop->isInitialized($this)) continue;
            if (filled($include) && !in_array($prop->getName(), $include)) continue;
            if (blank($include) && (!$prop->isPublic() && !$prop->isProtected())) continue;

            $value = $prop->getValue($this);
            if (is_object($value)) {
                if (is_subclass_of($value, \JsonSerializable::class)) {
                    $value = $value->jsonSerialize();
                }elseif (method_exists($value, 'toArray')) {
                    $value = $value->toArray();
                }
            }

            $ret[$prop->getName()] = $value;
        }

        return $ret;
    }

    public function toArray(): array
    {
        if (method_exists($this, 'toArrayUsing')) {
            return $this->toArrayUsing();
        }

        return (array) $this->jsonSerialize();
    }

    public function toJson(int $options = 0): string
    {
        if (method_exists($this, 'toJsonUsing')) {
            return $this->toJsonUsing($options);
        }
        return json_encode($this->toArray(), $options);
    }
}
