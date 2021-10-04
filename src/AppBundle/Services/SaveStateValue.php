<?php
namespace AppBundle\Services;

/**
 * Class SaveStateValue
 */
class SaveStateValue
{
    /**
     * @var array $state
     */
    private $state = [];

    /**
     * @param string           $key
     * @param int|string|array $value
     */
    public function setValue($key, $value)
    {
        $this->state[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getValue($key)
    {
        if (array_key_exists($key, $this->state)) {
            return $this->state[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->state;
    }

    /**
     * @param string    $key
     * @param int|array $result
     *
     * @return mixed
     */
    public function setExcludedIds(string $key, $result)
    {
        if (!array_key_exists($key, $this->state)) {
            $excluded = [];
        } else {
            $excluded = $this->state[$key];
        }

        if (is_array($result) || $result instanceof \ArrayIterator) {
            foreach ($result as $value) {
                if (is_object($value) && !in_array($value->getId(), $excluded)) {
                    $excluded[] = (int) $value->getId();
                }
            }
        } else {
            if (!empty($result) && !in_array($result, $excluded)) {
                $excluded[] = (int) $result;
            }
        }
        $this->state[$key] = $excluded;
    }
}