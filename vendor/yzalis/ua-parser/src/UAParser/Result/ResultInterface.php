<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface ResultInterface
{
    public function getBrowser();

    public function getOperatingSystem();

    public function getDevice();

    /**
     * Extracts data from an array.
     *
     * @param array $data An array.
     */
    public function fromArray();
}