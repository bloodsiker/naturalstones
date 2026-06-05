<?php

namespace AppBundle\Services;

class SaveStateValue
{
    /** @var array<string, mixed> */
    private array $state = [];

    public function setValue(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
    }

    public function getValue(string $key): mixed
    {
        return $this->state[$key] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array
    {
        return $this->state;
    }

    /**
     * @param int|array<int, object>|\ArrayIterator<int, object> $result
     */
    public function setExcludedIds(string $key, int|array|\ArrayIterator $result): void
    {
        $excluded = $this->state[$key] ?? [];

        if (is_array($result) || $result instanceof \ArrayIterator) {
            foreach ($result as $value) {
                if (is_object($value) && !in_array($value->getId(), $excluded, true)) {
                    $excluded[] = (int) $value->getId();
                }
            }
        } elseif (!empty($result) && !in_array($result, $excluded, true)) {
            $excluded[] = (int) $result;
        }

        $this->state[$key] = $excluded;
    }
}