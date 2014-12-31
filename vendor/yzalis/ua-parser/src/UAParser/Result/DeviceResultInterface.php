<?php

namespace UAParser\Result;

/**
 * @author Benjamin Laugueux <benjamin@yzalis.com>
 */
interface DeviceResultInterface
{
    /**
     * Returns the device contructor.
     *
     * @return string
     */
    public function getConstructor();

    /**
     * Returns the device model.
     *
     * @return string
     */
    public function getModel();

    /**
     * Returns the device type.
     *
     * @return string
     */
    public function getType();

    /**
     * Check the device type.
     *
     * @param string $type Check if type is (mobile|tablet|desktop)
     * 
     * @return boolean
     */
    public function is($type);
}