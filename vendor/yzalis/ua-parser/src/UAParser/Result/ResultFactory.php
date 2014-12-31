<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
class ResultFactory implements ResultFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    final public function createFromArray(array $data)
    {
        $result = $this->newInstance();
        $result->fromArray($data);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function newInstance()
    {
        return new Result();
    }
}