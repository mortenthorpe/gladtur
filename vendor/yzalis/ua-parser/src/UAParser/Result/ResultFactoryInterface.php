<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface ResultFactoryInterface
{
    /**
     * @param array $data An array of data.
     *
     * @return ResultInterface
     */
    public function createFromArray(array $data);

    /**
     * @return ResultInterface
     */
    public function newInstance();
}